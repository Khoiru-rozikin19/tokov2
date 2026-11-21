<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'image_path', 'sort_order', 'type'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get normalized category type (kuota_xl, pln, digital, general).
     * Falls back to smart keyword detection from name if type is not explicitly set.
     */
    public function getTypeAttribute(?string $value): string
    {
        if (!empty($value)) {
            return $value;
        }

        $lower = strtolower((string) $this->name);
        if (str_contains($lower, 'pln') || str_contains($lower, 'token') || str_contains($lower, 'listrik')) {
            return 'pln';
        }
        if (str_contains($lower, 'xl') || str_contains($lower, 'kuota') || str_contains($lower, 'pulsa') || str_contains($lower, 'paket')) {
            return 'kuota_xl';
        }
        if (str_contains($lower, 'digital') || str_contains($lower, 'vpn') || str_contains($lower, 'ssh') || str_contains($lower, 'akun')) {
            return 'digital';
        }

        return 'general';
    }

    /**
     * Readable human label for the category type.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'kuota_xl' => 'Kuota & Pulsa XL (H2H OKEConnect)',
            'pln' => 'Token Listrik PLN (H2H OKEConnect)',
            'digital' => 'Digital & Akun VPN (VPS / Stok Akun)',
            default => 'Produk Umum',
        };
    }

    /**
     * Short badge label.
     */
    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'kuota_xl' => 'Kuota XL',
            'pln' => 'Token PLN',
            'digital' => 'Digital / VPN',
            default => 'Umum',
        };
    }
}
