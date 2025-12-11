<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user that performed the activity
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get action badge color
     */
    public function getActionBadgeColorAttribute()
    {
        return match ($this->action) {
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger',
            'status_change' => 'info',
            'login' => 'primary',
            'logout' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get action icon
     */
    public function getActionIconAttribute()
    {
        return match ($this->action) {
            'create' => 'fa-plus',
            'update' => 'fa-edit',
            'delete' => 'fa-trash',
            'status_change' => 'fa-exchange-alt',
            'login' => 'fa-sign-in-alt',
            'logout' => 'fa-sign-out-alt',
            default => 'fa-info-circle',
        };
    }

    /**
     * Get formatted action text
     */
    public function getActionTextAttribute()
    {
        return match ($this->action) {
            'create' => 'Menambahkan',
            'update' => 'Mengubah',
            'delete' => 'Menghapus',
            'status_change' => 'Mengubah Status',
            'login' => 'Login',
            'logout' => 'Logout',
            default => ucfirst($this->action),
        };
    }
}
