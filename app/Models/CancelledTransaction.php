<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelledTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'cancellation_date',
        'cancellation_reason',
    ];

    protected $casts = [
        'cancellation_date' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Relasi dengan Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
