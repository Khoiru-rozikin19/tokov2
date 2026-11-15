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
        'commission_earned',
        'telegram_message_id',
        'expired_at',
        'escrow_status',
        'escrow_amount',
        'escrow_released_at',
        'paid_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'commission_earned' => 'decimal:2',
        'escrow_released_at' => 'datetime',
        'paid_at' => 'datetime',
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
}
