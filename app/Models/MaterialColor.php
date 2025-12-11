<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialColor extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'name',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Relasi dengan Material
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
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
     * Get total price (material base price + color price)
     */
    public function getTotalPriceAttribute()
    {
        return $this->material->price + $this->price;
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    /**
     * Check if color is used in any order
     */
    public function isUsedInOrders()
    {
        return $this->orderItems()->exists();
    }
}
