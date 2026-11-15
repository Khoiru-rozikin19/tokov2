<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'discount_amount',
        'roles',
        'is_active',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'roles' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship with the discounted product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope: only active discounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

