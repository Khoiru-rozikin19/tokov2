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
     * Format supplier response for buyer display without leaking internal H2H balance figures.
     */
    public function getBuyerSafeFailureReason(): string
    {
        $raw = trim((string) ($this->supplier_response ?? ''));
        if ($raw === '') {
            return 'Pesanan tidak dapat diproses oleh supplier.';
        }

        // Remove internal H2H balance info like "Saldo 267.994 @10:57" or "Saldo 508.744 - 62.350 = 446.394"
        $cleaned = preg_replace('/\.?\s*Saldo\s+[\d\.,\s\-=@:]+.*$/i', '', $raw);
        $cleaned = preg_replace('/^(?:T#\d+\s+)?(?:R#ORD-[A-Z0-9-]+\s+)?/i', '', trim((string) $cleaned));

        return trim((string) $cleaned, " .") ?: 'Pesanan tidak dapat diproses oleh supplier.';
    }
}
