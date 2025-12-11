<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalItem extends Model
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
     * Relasi dengan AdditionalItemOption
     */
    public function options()
    {
        return $this->hasMany(AdditionalItemOption::class);
    }

    /**
     * Relasi dengan OrderItem melalui AdditionalItemOption
     */
    public function orderItems()
    {
        return $this->hasManyThrough(
            OrderItem::class,
            AdditionalItemOption::class,
            'additional_item_id',
            'additional_item_option_id'
        );
    }

    /**
     * Accessor untuk format harga
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get total options count
     */
    public function getOptionsCountAttribute()
    {
        return $this->options()->count();
    }

    /**
     * Check if additional item is used in any order
     */
    public function isUsedInOrders()
    {
        return $this->options()->whereHas('orderItems')->exists();
    }

    /**
     * Get price range from options
     */
    public function getPriceRangeAttribute()
    {
        $minPrice = $this->options()->min('price') ?? 0;
        $maxPrice = $this->options()->max('price') ?? 0;

        if ($minPrice == $maxPrice) {
            return 'Rp ' . number_format($minPrice, 0, ',', '.');
        }

        return 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.');
    }

    /**
     * Delete all options when deleting item
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($item) {
            $item->options()->delete();
        });
    }
}
