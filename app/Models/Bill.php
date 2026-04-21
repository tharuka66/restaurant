<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id', 'table_session_id', 'subtotal',
        'tax_amount', 'total_amount', 'payment_method', 'status', 'paid_at',
    ];

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'tax_amount'    => 'decimal:2',
        'total_amount'  => 'decimal:2',
        'paid_at'       => 'datetime',
    ];

    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function session()    { return $this->belongsTo(TableSession::class, 'table_session_id'); }

    public function isPaid(): bool { return $this->status === 'paid'; }
}
