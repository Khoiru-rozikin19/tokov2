<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'product_id',
        'email_or_whatsapp',
        'customer_name',
        'target_phone',
        'base_amount',
        'unique_code',
        'total_amount',
        'cost_price',
        'profit',
        'status',
        'fulfillment_stage',
        'is_preorder',
        'payment_method',
        'qris_payload',
        'vpn_config',
        'sn',
        'supplier_response',
        'commission_earned',
        'telegram_message_id',
        'expired_at',
        'escrow_status',
        'escrow_amount',
        'escrow_released_at',
        'paid_at',
        'refunded_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'commission_earned' => 'decimal:2',
        'escrow_released_at' => 'datetime',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'is_preorder' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function isExpired(): bool
    {
        return in_array($this->status, ['pending', 'pending_manual']) && $this->expired_at && $this->expired_at->isPast();
    }

    /**
     * Get the effective modal/cost price for this order (snapshot first, fallback to product modal).
     */
    public function getEffectiveCostPrice(): int
    {
        if ($this->cost_price !== null && (int) $this->cost_price > 0) {
            return (int) $this->cost_price;
        }
        return (int) ($this->product ? ($this->product->harga_modal ?? 0) : 0);
    }

    /**
     * Get the effective net profit for this order.
     */
    public function getEffectiveProfit(): int
    {
        if ($this->profit !== null && $this->cost_price !== null && (int) $this->cost_price > 0) {
            return (int) $this->profit;
        }
        return (int) $this->total_amount - $this->getEffectiveCostPrice();
    }

    public function processEscrowAndNotification()
    {
        $this->paid_at = now();
        $product = $this->product;
        $seller = $product ? $product->seller : null;

        // Snapshot harga modal dan keuntungan pada saat pembayaran berhasil
        $modal = ($this->cost_price !== null && (int) $this->cost_price > 0)
            ? (int) $this->cost_price
            : (int) ($product ? ($product->harga_modal ?? 0) : 0);
        $this->cost_price = $modal;
        $this->profit = (int) $this->total_amount - $modal;
        
        // Produk milik admin (bukan seller) → tidak perlu escrow dan notifikasi seller
        if ($product && $product->user_id && $seller && $seller->role !== 'admin') {
            // Seller product
            $escrowAmount = $this->profit;
            $this->escrow_status = 'held';
            $this->escrow_amount = $escrowAmount;
            $this->save();
            
            // Increment seller held balance
            $sellerBalance = \App\Models\UserBalance::firstOrCreate(
                ['user_id' => $product->user_id],
                ['balance' => 0, 'held_balance' => 0]
            );
            $sellerBalance->increment('held_balance', $escrowAmount);
            
            // Send seller Telegram notification
            if (!empty($seller->telegram_chat_id)) {
                $telegramService = app(\App\Services\TelegramService::class);
                $telegramService->sendSellerOrderNotification(
                    $this->id,
                    $this->total_amount,
                    $this->email_or_whatsapp,
                    $seller->telegram_chat_id
                );
            }
        } else {
            // Admin/System product
            $this->escrow_status = 'none';
            $this->save();
        }
    }

    /**
     * Atomically mark a failed order and refund its total_amount to the user's balance (idempotent).
     *
     * @return bool True if user balance was credited, false if already refunded or guest order.
     */
    public function refundToBuyer(string $reason = '', string $failedStatus = 'gagal'): bool
    {
        $refunded = \Illuminate\Support\Facades\DB::transaction(function () use ($reason, $failedStatus) {
            $locked = self::where('id', $this->id)->lockForUpdate()->first();
            if (!$locked) {
                return false;
            }

            $locked->status = $failedStatus;
            $locked->is_preorder = false;
            if ($reason !== '') {
                $locked->supplier_response = $reason;
            }

            // Tandai tahap mana yang gagal (dipakai tampilan progres transaksi)
            $currentStage = (string) $locked->fulfillment_stage;
            if (in_array($currentStage, ['vpn', 'supplier'], true)) {
                $locked->fulfillment_stage = 'failed_' . $currentStage;
            } elseif (!str_starts_with($currentStage, 'failed')) {
                $lockedProduct = $locked->product;
                if ($lockedProduct && !empty($lockedProduct->orderkuota_product_code)) {
                    $locked->fulfillment_stage = 'failed_supplier';
                } elseif ($lockedProduct && !empty($lockedProduct->vps_server_id)) {
                    $locked->fulfillment_stage = 'failed_vpn';
                } else {
                    $locked->fulfillment_stage = 'failed';
                }
            }

            // Deduct held balance from seller if applicable
            if (in_array($locked->escrow_status, ['held', 'disputed'])) {
                $sellerId = $locked->product ? $locked->product->user_id : null;
                if ($sellerId) {
                    $sellerBalance = UserBalance::where('user_id', $sellerId)->lockForUpdate()->first();
                    if ($sellerBalance) {
                        $newHeld = max(0, $sellerBalance->held_balance - (float) $locked->escrow_amount);
                        $sellerBalance->update(['held_balance' => $newHeld]);
                    }
                }
                $locked->escrow_status = 'none';
            }

            // Prevent duplicate refunds
            $alreadyRefunded = $locked->refunded_at !== null || BalanceTransaction::where('reference_id', $locked->id)
                ->where('description', 'like', 'Refund%')
                ->exists();

            $didRefundBalance = false;
            if (!$alreadyRefunded && $locked->user_id && (int) $locked->total_amount > 0) {
                $buyer = User::find($locked->user_id);
                if ($buyer) {
                    $buyerBalance = UserBalance::where('user_id', $buyer->id)->lockForUpdate()->first();
                    if (!$buyerBalance) {
                        $buyerBalance = $buyer->getOrCreateBalance();
                        $buyerBalance = UserBalance::where('user_id', $buyer->id)->lockForUpdate()->first();
                    }

                    $balanceBefore = $buyerBalance->balance;
                    $buyerBalance->increment('balance', (int) $locked->total_amount);
                    $buyerBalance->refresh();

                    $productLabel = $locked->product ? $locked->product->name : 'Produk H2H';
                    $desc = "Refund pesanan gagal ({$locked->id}): {$productLabel}";
                    if ($reason !== '') {
                        $desc .= ' — ' . \Illuminate\Support\Str::limit($reason, 120);
                    }

                    BalanceTransaction::create([
                        'user_id' => $buyer->id,
                        'type' => 'topup',
                        'amount' => (int) $locked->total_amount,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $buyerBalance->balance,
                        'description' => $desc,
                        'reference_id' => $locked->id,
                        'status' => 'success',
                    ]);

                    $locked->refunded_at = now();
                    $didRefundBalance = true;
                }
            }

            $locked->save();
            return $didRefundBalance;
        });

        $this->refresh();
        return $refunded;
    }

    /**
     * Alasan gagal yang aman ditampilkan ke pembeli:
     * tidak membocorkan nama supplier, saldo H2H, kode produk, maupun detail teknis internal.
     */
    public function getBuyerSafeFailureReason(): string
    {
        $generic = 'Pesanan tidak dapat diproses saat ini.';
        $raw = trim((string) ($this->supplier_response ?? ''));
        if ($raw === '') {
            return $generic;
        }

        // Error operasional/internal (saldo H2H, kredensial, koneksi) -> pesan umum saja
        if (preg_match('/saldo\s*(?:anda\s*)?(?:tidak\s*cukup|kurang|habis|minus|tidak\s*mencukupi)|\b(?:pin|password|member\s*id|ip|key|callback)\b.*(?:salah|tidak\s*terdaftar|tidak\s*diizinkan|ditolak|invalid)|timeout|koneksi|connection|curl|https?:/i', $raw)) {
            return 'Layanan sedang mengalami gangguan sementara. Dana Anda aman, silakan coba lagi beberapa saat lagi.';
        }

        // Buang info saldo internal, mis. "Saldo 267.994 @10:57" atau "Saldo 508.744 - 62.350 = 446.394"
        $cleaned = trim((string) preg_replace('/\.?\s*Saldo\s+[\d\.,\s\-=@:]+.*$/i', '', $raw));
        // Buang prefix referensi transaksi "T#123 R#ORD-XXXX"
        $cleaned = trim((string) preg_replace('/^(?:T#\d+\s+)?(?:R#ORD-[A-Z0-9-]+\s+)?/i', '', $cleaned));

        // Ambil keterangan setelah kata GAGAL (membuang nama produk/kode supplier di depannya)
        if (preg_match('/\b(?:gagal|failed)\b[\.\:\-\s]*(.*)$/is', $cleaned, $m)) {
            $cleaned = trim($m[1]);
            if ($cleaned === '') {
                return 'Pesanan ditolak oleh penyedia layanan.';
            }
        }

        // Samarkan nama supplier bila sempat muncul
        $cleaned = (string) preg_replace('/\b(?:okeconnect|oke\s*connect|orderkuota|order\s*kuota|h2h)\b/i', 'sistem', $cleaned);
        $cleaned = trim($cleaned, " .");

        if ($cleaned === '') {
            return $generic;
        }

        return mb_strtoupper(mb_substr($cleaned, 0, 1)) . mb_substr($cleaned, 1);
    }

    /**
     * Kelompok status untuk tampilan: success | failed | processing | pending | expired | cancelled | rejected | other
     */
    public function getStatusGroup(): string
    {
        return match (true) {
            in_array($this->status, ['success', 'paid', 'sukses'], true) => 'success',
            in_array($this->status, ['gagal', 'failed'], true) => 'failed',
            $this->status === 'proses' => 'processing',
            in_array($this->status, ['pending', 'pending_manual'], true) => 'pending',
            $this->status === 'expired' => 'expired',
            $this->status === 'dibatalkan' => 'cancelled',
            in_array($this->status, ['rejected', 'ditolak'], true) => 'rejected',
            default => 'other',
        };
    }

    /**
     * Data progres transaksi untuk halaman checkout (aman ditampilkan ke pembeli,
     * tanpa informasi supplier).
     */
    public function toProgressPayload(): array
    {
        $this->loadMissing('product.category');
        $product = $this->product;
        $group = $this->getStatusGroup();

        return [
            'id' => $this->id,
            'status' => $this->status,
            'group' => $group,
            'stage' => $this->fulfillment_stage,
            'preorder' => (bool) $this->is_preorder,
            'refunded' => $this->refunded_at !== null,
            'reason' => $group === 'failed' ? $this->getBuyerSafeFailureReason() : null,
            'sn' => $this->sn,
            'date' => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '-',
            'category' => ($product && $product->category) ? $product->category->name : '-',
            'product_name' => $product->name ?? 'Produk',
            'email' => $this->email_or_whatsapp,
            'phone' => $this->target_phone,
            'total_amount' => (int) $this->total_amount,
            'formatted_total' => 'Rp ' . number_format((int) $this->total_amount, 0, ',', '.'),
            'payment_method' => $this->payment_method,
            'steps' => [
                'vpn' => (bool) ($product && !empty($product->vps_server_id)),
                'supplier' => (bool) ($product && !empty($product->orderkuota_product_code)),
            ],
            'vpn_config' => $group === 'success' ? $this->vpn_config : null,
            'success_instruction' => ($group === 'success' && $product) ? $product->success_instruction : null,
        ];
    }
}
