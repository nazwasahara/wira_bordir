<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone_number',
        'customer_address',
        'total_price',
        'amount_paid',
        'payment_proof',
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    // Order Status Constants - SESUAI MIGRATION
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CONFIRM = 'confirm';
    const STATUS_PROCESSING = 'processing';
    const STATUS_DONE = 'done';
    const STATUS_CANCEL = 'cancel';

    /**
     * Get available statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_PAID => 'Sudah Dibayar',
            self::STATUS_CONFIRM => 'Dikonfirmasi',
            self::STATUS_PROCESSING => 'Sedang Diproses',
            self::STATUS_DONE => 'Selesai',
            self::STATUS_CANCEL => 'Dibatalkan',
        ];
    }

    /**
     * Relasi dengan User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi dengan OrderItem
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi dengan CancelledTransaction
     */
    public function cancelledTransaction()
    {
        return $this->hasOne(CancelledTransaction::class);
    }

    /**
     * Accessor untuk format total price
     */
    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    /**
     * Accessor untuk format amount paid
     */
    public function getFormattedAmountPaidAttribute()
    {
        return 'Rp ' . number_format($this->amount_paid ?? 0, 0, ',', '.');
    }

    /**
     * Accessor untuk status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_PAID => 'info',
            self::STATUS_CONFIRM => 'primary',
            self::STATUS_PROCESSING => 'secondary',
            self::STATUS_DONE => 'success',
            self::STATUS_CANCEL => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Accessor untuk status text
     */
    public function getStatusTextAttribute()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Accessor untuk sisa pembayaran
     */
    public function getRemainingPaymentAttribute()
    {
        return $this->total_price - ($this->amount_paid ?? 0);
    }

    /**
     * Accessor untuk format sisa pembayaran
     */
    public function getFormattedRemainingPaymentAttribute()
    {
        return 'Rp ' . number_format($this->remaining_payment, 0, ',', '.');
    }

    /**
     * Check if payment is complete
     */
    public function isPaymentComplete()
    {
        return ($this->amount_paid ?? 0) >= $this->total_price;
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PAID, self::STATUS_CONFIRM]);
    }

    /**
     * Check if order can be confirmed
     */
    public function canBeConfirmed()
    {
        return $this->status === self::STATUS_PAID && $this->payment_proof;
    }

    /**
     * Scope untuk filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope untuk paid orders
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope untuk confirmed orders
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRM);
    }

    /**
     * Scope untuk processing orders
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    /**
     * Scope untuk done orders
     */
    public function scopeDone($query)
    {
        return $query->where('status', self::STATUS_DONE);
    }

    /**
     * Scope untuk cancelled orders
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCEL);
    }

    /**
     * Generate order number
     */
    public function getOrderNumberAttribute()
    {
        return 'ORD-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
