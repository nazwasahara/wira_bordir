<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_date',
    ];

    protected $casts = [
        'invoice_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function details()
    {
        return $this->hasMany(PurchaseInvoiceDetail::class, 'invoice_id');
    }

    /**
     * ✅ FIX: Calculate total from details (manual calculation)
     */
    public function getTotalAmountAttribute()
    {
        return $this->details->sum(function ($detail) {
            return $detail->quantity * $detail->unit_price;
        });
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Get invoice number (generated)
     */
    public function getInvoiceNumberAttribute()
    {
        return 'INV-' . $this->created_at->format('Ymd') . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get total items count
     */
    public function getTotalItemsAttribute()
    {
        return $this->details()->count();
    }

    /**
     * Get total quantity
     */
    public function getTotalQuantityAttribute()
    {
        return $this->details()->sum('quantity');
    }
}
