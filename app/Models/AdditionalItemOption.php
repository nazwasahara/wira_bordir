<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalItemOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'additional_item_id',
        'color',
        'model',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Relasi dengan AdditionalItem
     */
    public function additionalItem()
    {
        return $this->belongsTo(AdditionalItem::class);
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
     * Get full name (color + model)
     */
    public function getFullNameAttribute()
    {
        return trim($this->color . ' - ' . $this->model);
    }

    /**
     * Check if option is used in any order
     */
    public function isUsedInOrders()
    {
        return $this->orderItems()->exists();
    }
}
