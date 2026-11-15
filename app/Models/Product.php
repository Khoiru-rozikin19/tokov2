<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'vps_server_id',
        'vps_command_template',
        'name',
        'image_path',
        'description',
        'price',
        'harga_modal',
        'duration_days',
        'config_template',
        'stock',
        'orderkuota_product_code',
        'success_instruction',
        'visibility',
        'status',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function vpsServer()
    {
        return $this->belongsTo(VpsServer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function stocks()
    {
        return $this->hasMany(AccountStock::class);
    }

    public function commissions()
    {
        return $this->hasMany(SellerCommission::class);
    }

    public function discount()
    {
        return $this->hasOne(ProductDiscount::class);
    }

    /**
     * Get discount amount applicable for a given user and optional payment method (defaults to authenticated user).
     */
    public function getDiscountAmountForUser(?User $user = null, ?string $paymentMethod = null): int
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return 0;
        }

        $discount = $this->relationLoaded('discount') ? $this->discount : $this->discount()->first();
        if (!$discount || !$discount->is_active) {
            return 0;
        }

        $roles = is_array($discount->roles) ? $discount->roles : (json_decode($discount->roles, true) ?: []);
        if (!in_array($user->role, $roles, true)) {
            return 0;
        }

        // Validate payment method if specified
        if ($paymentMethod !== null) {
            $paymentMethods = is_array($discount->payment_methods) 
                ? $discount->payment_methods 
                : (json_decode($discount->payment_methods, true) ?: ['qris', 'balance']);
            if (!in_array($paymentMethod, $paymentMethods, true)) {
                return 0;
            }
        }

        return min((int) $this->price, (int) round($discount->discount_amount));
    }

    /**
     * Get final price for a given user after discount (defaults to authenticated user).
     */
    public function getFinalPriceForUser(?User $user = null, ?string $paymentMethod = null): int
    {
        $discountAmount = $this->getDiscountAmountForUser($user, $paymentMethod);
        return max(0, (int) $this->price - $discountAmount);
    }

    /**
     * Get dynamic stock count if account stocks exist,
     * otherwise use static column stock.
     *
     * @return int
     */
    public function getStockAttribute()
    {
        return $this->cekStok();
    }

    /**
     * Get flexible stock count:
     * - If account configurations exist, use dynamic stock from ready account data count.
     * - Otherwise, use manual stock from the static column.
     *
     * @return int
     */
    public function cekStok()
    {
        if ($this->stocks()->where('status', 'ready')->exists()) {
            return $this->stocks()->where('status', 'ready')->count();
        }
        return $this->attributes['stock'] ?? 0;
    }
}
