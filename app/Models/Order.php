<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING_CANCEL   = 'pending_cancel';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED         = 'approved';
    const STATUS_PREPARING        = 'preparing';
    const STATUS_READY            = 'ready';
    const STATUS_COMPLETED        = 'completed';
    const STATUS_REJECTED         = 'rejected';
    const STATUS_CANCELLED        = 'cancelled';

    protected $fillable = [
        'restaurant_id', 'table_session_id', 'status',
        'rejection_reason', 'cancel_deadline', 'eta_minutes', 'notes',
    ];

    protected $casts = ['cancel_deadline' => 'datetime'];

    public function restaurant()  { return $this->belongsTo(Restaurant::class); }
    public function session()     { return $this->belongsTo(TableSession::class, 'table_session_id'); }
    public function items()       { return $this->hasMany(OrderItem::class); }

    public function totalAmount(): float
    {
        return $this->items->sum(fn($item) => $item->quantity * $item->unit_price);
    }

    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_PENDING_CANCEL
            && $this->cancel_deadline
            && now()->lt($this->cancel_deadline);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDING_CANCEL   => 'Pending (Cancellable)',
            self::STATUS_PENDING_APPROVAL => 'Waiting for Approval',
            self::STATUS_APPROVED         => 'Approved',
            self::STATUS_PREPARING        => 'Preparing',
            self::STATUS_READY            => 'Ready for Pickup',
            self::STATUS_COMPLETED        => 'Completed',
            self::STATUS_REJECTED         => 'Rejected',
            self::STATUS_CANCELLED        => 'Cancelled',
            default                       => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            self::STATUS_PENDING_CANCEL   => 'warning',
            self::STATUS_PENDING_APPROVAL => 'info',
            self::STATUS_APPROVED         => 'primary',
            self::STATUS_PREPARING        => 'purple',
            self::STATUS_READY            => 'success',
            self::STATUS_COMPLETED        => 'secondary',
            self::STATUS_REJECTED         => 'danger',
            self::STATUS_CANCELLED        => 'dark',
            default                       => 'secondary',
        };
    }
}
