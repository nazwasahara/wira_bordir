<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaceOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'color',
        'size',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Available sizes
    const SIZE_SMALL = 'kecil';
    const SIZE_MEDIUM = 'sedang';
    const SIZE_LARGE = 'besar';

    /**
     * Get available sizes
     */
    public static function getSizes()
    {
        return [
            self::SIZE_SMALL => 'Kecil',
            self::SIZE_MEDIUM => 'Sedang',
            self::SIZE_LARGE => 'Besar',
        ];
    }

    /**
     * Relasi dengan OrderItem
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Accessor untuk format harga
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Accessor untuk ukuran dalam bahasa Indonesia
     */
    public function getSizeIndonesiaAttribute()
    {
        $sizes = self::getSizes();
        return $sizes[$this->size] ?? $this->size;
    }

    /**
     * Accessor untuk badge color berdasarkan size
     */
    public function getSizeBadgeColorAttribute()
    {
        return match ($this->size) {
            self::SIZE_SMALL => 'info',
            self::SIZE_MEDIUM => 'primary',
            self::SIZE_LARGE => 'success',
            default => 'secondary',
        };
    }

    /**
     * Check if lace option is used in any order
     */
    public function isUsedInOrders()
    {
        return $this->orderItems()->exists();
    }

    /**
     * Scope untuk filter by size
     */
    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }
}
