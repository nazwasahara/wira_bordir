<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Relasi dengan MaterialColor
     */
    public function colors()
    {
        return $this->hasMany(MaterialColor::class);
    }

    /**
     * Relasi dengan OrderItem
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope untuk material aktif yang memiliki warna
     */
    public function scopeActive($query)
    {
        return $query->has('colors');
    }

    /**
     * Accessor untuk format harga
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get total colors count
     */
    public function getColorsCountAttribute()
    {
        return $this->colors()->count();
    }

    /**
     * Check if material is used in any order
     */
    public function isUsedInOrders()
    {
        return $this->orderItems()->exists();
    }
}
