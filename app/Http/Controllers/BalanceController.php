<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Setting;
use App\Models\BalanceTransaction;
use App\Services\QrisService;
use App\Services\TelegramService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BalanceController extends Controller
{
    /**
     * Display the user's balance page with history.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $currentBalance = $user->getBalance();

        // Auto-expire past pending topup orders for this user
        $expiredTopupIds = Order::where('user_id', $user->id)
            ->where('payment_method', 'topup_balance')
            ->whereIn('status', ['pending', 'pending_manual'])
            ->where('expired_at', '<=', Carbon::now())
            ->pluck('id');

        if ($expiredTopupIds->isNotEmpty()) {
            Order::whereIn('id', $expiredTopupIds)->update(['status' => 'expired']);
            BalanceTransaction::whereIn('reference_id', $expiredTopupIds)
                ->where('status', 'pending')
                ->update(['status' => 'failed']);
        }

        // Dedicated Top Up Saldo history (from Order where payment_method = topup_balance)
        $topupOrders = Order::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('payment_method', 'topup_balance')
                  ->orWhere('id', 'like', 'TOP-%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'topup_page')
            ->withQueryString();

        // Tab filter for balance usage/mutation history (excluding topup so it is not duplicated)
        $tab = $request->query('tab', 'all');

        $query = BalanceTransaction::where('user_id', $user->id)
            ->where('status', 'success')
            ->where('type', '!=', 'topup')
            ->orderBy('created_at', 'desc');

        if ($tab === 'purchase') {
            $query->where('type', 'purchase');
        } elseif ($tab === 'transfer') {
            $query->whereIn('type', ['transfer_in', 'transfer_out']);
        }

        $transactions = $query->paginate(15)->withQueryString();

        // Pending topups (for showing active topup orders banner)
        $pendingTopups = BalanceTransaction::where('user_id', $user->id)
            ->where('type', 'topup')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('balance', compact('currentBalance', 'topupOrders', 'transactions', 'tab', 'pendingTopups'));
    }

    /**
     * Create a topup order via QRIS.
     */
    public function topup(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'amount' => 'required|integer|min:10000|max:10000000',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();
        $amount = (int) $request->amount;

        // Get static QRIS
        $staticQris = Setting::get('qris_static_string');
        if (empty($staticQris)) {
            return response()->json([
                'success' => false,
                'message' => 'Metode pembayaran QRIS belum dikonfigurasi oleh admin.',
            ], 500);
        }

        // Generate unique code (1-99) that doesn't clash with active pending orders
        $usedCodes = Order::whereIn('status', ['pending', 'pending_manual'])
            ->where('expired_at', '>', Carbon::now())
            ->pluck('unique_code')
            ->toArray();

        $availableCodes = array_diff(range(1, 99), $usedCodes);

        if (empty($availableCodes)) {
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak pesanan pending. Silakan coba lagi beberapa saat lagi.',
            ], 422);
        }

        $uniqueCode = $availableCodes[array_rand($availableCodes)];
        $totalAmount = $amount + $uniqueCode;

        // Generate dynamic QRIS
        $qrisPayload = QrisService::generateDynamicQris($staticQris, $totalAmount);

        // Generate Order ID for topup
        $orderId = 'TOP-' . strtoupper(Str::random(8));

        $order = DB::transaction(function () use ($orderId, $user, $amount, $uniqueCode, $totalAmount, $qrisPayload) {
            // Create the topup order
            $order = Order::create([
                'id' => $orderId,
                'user_id' => $user->id,
                'product_id' => null,
                'email_or_whatsapp' => $user->email,
                'base_amount' => $amount,
                'unique_code' => $uniqueCode,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => 'topup_balance',
                'qris_payload' => $qrisPayload,
                'expired_at' => Carbon::now()->addMinutes(30),
            ]);

            // Create pending balance transaction
            $balanceRecord = $user->getOrCreateBalance();
            BalanceTransaction::create([
                'user_id' => $user->id,
                'type' => 'topup',
                'amount' => $amount,
                'balance_before' => $balanceRecord->balance,
                'balance_after' => $balanceRecord->balance, // Not yet added, pending
                'description' => 'Top up saldo via QRIS - ' . $orderId,
                'reference_id' => $orderId,
                'status' => 'pending',
            ]);

            return $order;
        });

        // Kirim notifikasi ke Telegram Admin (gunakan dispatchAfterResponse di production agar tidak bergantung pada daemon queue worker)
        if (app()->runningUnitTests()) {
            \App\Jobs\SendTelegramNotificationJob::dispatch($order->id, $order->total_amount, $order->email_or_whatsapp);
        } else {
            \App\Jobs\SendTelegramNotificationJob::dispatchAfterResponse($order->id, $order->total_amount, $order->email_or_whatsapp);
        }

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'topup_amount' => $amount,
                'total_amount' => $order->total_amount,
                'qris_payload' => $order->qris_payload,
                'expired_at' => $order->expired_at->toIso8601String(),
                'server_time' => Carbon::now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Check topup order status (polling endpoint).
     */
    public function checkTopupStatus($id)
    {
        $order = Order::findOrFail($id);

        // Only allow the order owner to check
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Auto expire
        if (in_array($order->status, ['pending', 'pending_manual']) && $order->expired_at && $order->expired_at->isPast()) {
            $order->update(['status' => 'expired']);

            // Mark balance transaction as failed
            BalanceTransaction::where('reference_id', $order->id)
                ->where('status', 'pending')
                ->update(['status' => 'failed']);
        }

        $currentBalance = auth()->user()->getBalance();

        return response()->json([
            'status' => $order->status,
            'base_amount' => $order->base_amount,
            'formatted_base_amount' => 'Rp ' . number_format($order->base_amount, 0, ',', '.'),
            'current_balance' => $currentBalance,
            'formatted_balance' => 'Rp ' . number_format($currentBalance, 0, ',', '.'),
        ]);
    }

    /**
     * Get current balance (API endpoint for checkout modal).
     */
    public function getBalance()
    {
        $user = auth()->user();
        $balance = $user->getBalance();

        return response()->json([
            'success' => true,
            'balance' => $balance,
            'formatted_balance' => 'Rp ' . number_format($balance, 0, ',', '.'),
        ]);
    }
}
