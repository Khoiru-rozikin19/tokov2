<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use App\Services\OrderkuotaService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OkeconnectCallbackController extends Controller
{
    /**
     * Handle H2H callback notification from OKEConnect (supports both GET & POST).
     */
    public function handle(Request $request)
    {
        Log::info("OKEConnect Callback Received (" . $request->method() . "): " . json_encode($request->all()));

        // Optional webhook secret check if configured in Supplier Settings
        $configuredSecret = trim((string) Setting::get('orderkuota_webhook_secret', ''));
        if ($configuredSecret !== '') {
            $incomingKey = (string) ($request->input('key') ?? $request->input('secret') ?? $request->header('X-Webhook-Key', ''));
            if (!hash_equals($configuredSecret, $incomingKey)) {
                Log::warning("OKEConnect Callback Unauthorized: Invalid webhook key.");
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized webhook key.',
                ], 401);
            }
        }

        $rawMessage = trim((string) ($request->input('message')
            ?? $request->input('pesan')
            ?? $request->input('text')
            ?? $request->input('ket')
            ?? $request->input('note')
            ?? ''));

        $refId = trim((string) ($request->input('ref_id')
            ?? $request->input('refid')
            ?? $request->input('ref')
            ?? $request->input('idtrx')
            ?? ''));

        // Fallback: extract ORD-... reference from OKEConnect report message (e.g. "R#ORD-BNVPAXXN")
        if ($refId === '' && $rawMessage !== '') {
            if (preg_match('/(?:R#|\b)(ORD-[A-Z0-9-]+)/i', $rawMessage, $m)) {
                $refId = strtoupper(trim($m[1]));
            }
        }

        if ($refId === '') {
            Log::warning("OKEConnect Callback Warning: Missing reference ID in request payload.");
            return response()->json([
                'success' => false,
                'message' => 'Missing transaction reference ID (ref_id/refid).',
            ], 400);
        }

        $order = Order::with('product')->find($refId);
        if (!$order) {
            Log::warning("OKEConnect Callback Warning: Order ID {$refId} not found in database.");
            return response()->json([
                'success' => false,
                'message' => "Order ID {$refId} not found.",
            ], 404);
        }

        $statusParam = trim((string) ($request->input('status') ?? ''));
        $statusUpper = strtoupper($statusParam);
        $explicitSn = trim((string) ($request->input('sn')
            ?? $request->input('serial_number')
            ?? $request->input('token')
            ?? ''));

        $parsed = OrderkuotaService::parseOkeconnectResponse($rawMessage);
        $sn = $explicitSn !== '' ? $explicitSn : $parsed['sn'];

        // Determine final callback state (explicit status param takes precedence, then raw message parsing)
        $outcome = 'unknown';
        if (in_array($statusUpper, ['SUCCESS', 'SUKSES', 'BERHASIL', 'OK', '1'], true)) {
            $outcome = 'success';
        } elseif (in_array($statusUpper, ['FAILED', 'GAGAL', 'ERROR', 'BATAL', 'DIBATALKAN', '0'], true)) {
            $outcome = 'failed';
        } elseif (in_array($statusUpper, ['PROSES', 'PROCESS', 'PENDING'], true)) {
            $outcome = 'process';
        } elseif ($parsed['state'] !== 'unknown') {
            $outcome = $parsed['state'];
        }

        $responseNote = $rawMessage !== '' ? $rawMessage : ($statusParam !== '' ? "Callback status: {$statusParam}" : json_encode($request->all()));

        if ($outcome === 'success') {
            $wasAlreadySuccess = in_array($order->status, ['success', 'paid', 'sukses'], true);
            $targetStatus = $statusUpper === 'SUCCESS' ? 'success' : 'sukses';

            $updateData = [
                'status' => $targetStatus,
                'is_preorder' => false,
                'supplier_response' => $responseNote,
            ];
            if (!empty($sn)) {
                $updateData['sn'] = $sn;
            }
            $order->update($updateData);

            Log::info("OKEConnect Callback Success: Order {$refId} marked as {$targetStatus} with SN: " . ($sn ?: '-'));

            if (!$wasAlreadySuccess && !app()->runningUnitTests()) {
                try {
                    app(WhatsappService::class)->sendAutomatedNotification(
                        'order_success',
                        WhatsappService::formatOrderSuccessMessage($order->fresh('product'), 'OKEConnect Callback')
                    );
                } catch (\Throwable $e) {
                    Log::error("OKEConnect Callback WA notification failed for {$refId}: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Callback processed successfully. Order status updated to {$targetStatus}.",
            ]);
        }

        if ($outcome === 'failed') {
            $failStatus = $statusUpper === 'FAILED' ? 'failed' : 'gagal';
            $wasAlreadyFailedAndRefunded = in_array($order->status, ['gagal', 'failed'], true) && $order->refunded_at !== null;

            $didRefund = $order->refundToBuyer($responseNote, $failStatus);

            Log::info("OKEConnect Callback Failed: Order {$refId} marked as {$failStatus}. Refunded: " . ($didRefund ? 'YES' : 'NO'));

            if (!$wasAlreadyFailedAndRefunded && !app()->runningUnitTests()) {
                app(OrderkuotaService::class)->notifyH2HFailureToAdmin($order, $responseNote, $didRefund);
            }

            return response()->json([
                'success' => true,
                'refunded' => $didRefund,
                'message' => "Callback processed successfully. Order status updated to {$failStatus}.",
            ]);
        }

        // Still processing or informational message
        if ($responseNote !== '') {
            $order->update(['supplier_response' => $responseNote]);
        }

        return response()->json([
            'success' => true,
            'message' => "Callback received with status: " . ($statusParam !== '' ? $statusParam : $outcome) . ".",
        ]);
    }
}
