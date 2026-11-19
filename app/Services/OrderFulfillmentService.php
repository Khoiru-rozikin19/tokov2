<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Menjalankan pemenuhan pesanan (pembuatan akun VPN + penerusan pesanan ke penyedia layanan)
 * secara bertahap dan mencatat tahap terakhir di kolom orders.fulfillment_stage,
 * sehingga halaman checkout dapat menampilkan progres secara real-time.
 *
 * Tahap: queued -> vpn -> supplier -> done | failed_vpn | failed_supplier
 */
class OrderFulfillmentService
{
    public static function needsFulfillment(Order $order): bool
    {
        $product = $order->product;

        return $product
            && !$order->is_preorder
            && (!empty($product->vps_server_id) || !empty($product->orderkuota_product_code));
    }

    /**
     * Jalankan pemenuhan pesanan. Aman dipanggil berulang (hanya jalan jika tahap masih 'queued').
     */
    public function fulfill(string $orderId): ?Order
    {
        @ignore_user_abort(true);
        @set_time_limit(120);

        // Klaim pesanan secara atomik agar tidak diproses ganda
        $order = DB::transaction(function () use ($orderId) {
            $locked = Order::with('product')->where('id', $orderId)->lockForUpdate()->first();
            if (!$locked || $locked->fulfillment_stage !== 'queued') {
                return null;
            }
            if (!in_array($locked->status, ['proses', 'sukses', 'success', 'paid'], true)) {
                return null;
            }

            $product = $locked->product;
            if ($product && !empty($product->vps_server_id)) {
                $locked->fulfillment_stage = 'vpn';
            } elseif ($product && !empty($product->orderkuota_product_code)) {
                $locked->fulfillment_stage = 'supplier';
            } else {
                $locked->fulfillment_stage = 'done';
            }
            $locked->save();

            return $locked;
        });

        if (!$order) {
            return Order::with('product')->find($orderId);
        }

        $product = $order->product;
        $needsVps = $product && !empty($product->vps_server_id);
        $needsSupplier = $product && !empty($product->orderkuota_product_code);

        // === Tahap 1: pembuatan akun VPN ===
        if ($needsVps) {
            $ok = true;
            $internalError = null;
            $ssh = app(VpsSshService::class);

            try {
                $result = $ssh->createVpnAccount($order);
                if ($result === false) {
                    $ok = false;
                    $internalError = $ssh->lastError;
                }
            } catch (\Throwable $e) {
                $ok = false;
                $internalError = $e->getMessage();
            }

            if (!$ok) {
                return $this->failOrder($order, 'Akun VPN gagal dibuat secara otomatis.', $internalError);
            }

            $order->fulfillment_stage = $needsSupplier ? 'supplier' : 'vpn';
            $order->save(); // simpan vpn_config hasil pembuatan akun
        }

        // === Tahap 2: penerusan pesanan (cek nomor HP & proses) ===
        if ($needsSupplier) {
            if ($order->fulfillment_stage !== 'supplier') {
                $order->fulfillment_stage = 'supplier';
                $order->save();
            }

            // Status akhir (sukses / proses menunggu konfirmasi / gagal + refund) ditentukan di dalam method ini
            app(OrderkuotaService::class)->kirimPesananKeOrderkuota($order->id);

            return Order::with('product')->find($order->id);
        }

        // === Selesai (tanpa tahap supplier) ===
        $order->status = 'sukses';
        $order->fulfillment_stage = 'done';
        $order->save();

        return $order;
    }

    /**
     * Tandai pesanan gagal, refund otomatis, dan beri tahu admin.
     */
    protected function failOrder(Order $order, string $buyerReason, ?string $internalError = null): Order
    {
        Log::error("OrderFulfillment: pesanan {$order->id} gagal. {$buyerReason} Detail: " . ($internalError ?: '-'));

        $didRefund = $order->refundToBuyer($buyerReason, 'gagal');

        if (!app()->runningUnitTests()) {
            $adminReason = $buyerReason . ($internalError ? " [{$internalError}]" : '');

            try {
                app(WhatsappService::class)->sendAutomatedNotification(
                    'h2h_order_failed',
                    WhatsappService::formatH2HFailureMessage($order, $adminReason, $didRefund)
                );
            } catch (\Throwable $e) {
                Log::error('OrderFulfillment WA alert error: ' . $e->getMessage());
            }

            try {
                $adminId = env('TELEGRAM_ADMIN_ID') ?: config('services.telegram.admin_id', '6515967618');
                if ($adminId) {
                    $text = "🚨 *Pemenuhan Pesanan Gagal*\n\n"
                          . "📦 *ID Order:* `{$order->id}`\n"
                          . "🛍️ *Produk:* " . ($order->product->name ?? '-') . "\n"
                          . "❌ *Penyebab:* {$adminReason}\n"
                          . "↩️ *Refund:* " . ($didRefund ? 'Saldo pembeli sudah dikembalikan otomatis.' : 'Cek manual.');
                    app(TelegramService::class)->sendMessage($adminId, $text);
                }
            } catch (\Throwable $e) {
                Log::error('OrderFulfillment Telegram alert error: ' . $e->getMessage());
            }
        }

        return Order::with('product')->find($order->id) ?? $order;
    }

    /**
     * Notifikasi admin setelah pembelian via Saldo Akun selesai diproses (sukses / menunggu konfirmasi).
     * Kasus gagal sudah dinotifikasi saat refund.
     */
    public function notifyAdminAfterBalancePurchase(Order $order): void
    {
        if (in_array($order->status, ['gagal', 'failed'], true)) {
            return;
        }

        try {
            $adminId = env('TELEGRAM_ADMIN_ID') ?: config('services.telegram.admin_id', '6515967618');
            if ($adminId) {
                $formattedAmount = number_format($order->total_amount, 0, ',', '.');
                $productName = $order->product->name ?? 'Produk';
                $isSuccess = $order->status === 'sukses';
                $heading = $isSuccess ? '✅ *Transaksi Saldo Sukses*' : '⏳ *Transaksi Saldo Diproses H2H*';
                $desc = $isSuccess
                    ? 'Pembayaran menggunakan *Saldo Akun* berhasil dan pesanan telah selesai diproses.'
                    : 'Pembayaran menggunakan *Saldo Akun* berhasil dan pesanan sedang diproses di OKEConnect (menunggu callback konfirmasi).';

                app(TelegramService::class)->sendMessage(
                    $adminId,
                    "{$heading}\n\n"
                    . "📦 *ID Order:* `{$order->id}`\n"
                    . "🛍️ *Produk:* {$productName}\n"
                    . "💰 *Nominal:* Rp {$formattedAmount}\n"
                    . "👤 *Pelanggan:* {$order->email_or_whatsapp}\n\n"
                    . $desc
                );
            }
        } catch (\Throwable $e) {
            Log::error('OrderFulfillment notifyAdminAfterBalancePurchase Telegram error: ' . $e->getMessage());
        }

        if ($order->status === 'sukses') {
            try {
                app(WhatsappService::class)->sendAutomatedNotification(
                    'order_success',
                    WhatsappService::formatOrderSuccessMessage($order, 'Saldo Akun')
                );
            } catch (\Throwable $e) {
                Log::error('OrderFulfillment notifyAdminAfterBalancePurchase WA error: ' . $e->getMessage());
            }
        }
    }
}

