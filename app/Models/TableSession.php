<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id', 'table_id', 'customer_name', 'guests', 'status', 'opened_at', 'closed_at',
    ];

    protected $casts = ['opened_at' => 'datetime', 'closed_at' => 'datetime'];

    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function table()      { return $this->belongsTo(Table::class); }
    public function orders()     { return $this->hasMany(Order::class); }
    public function bill()       { return $this->hasOne(Bill::class); }

    public function activeOrders()
    {
        return $this->orders()->whereNotIn('status', ['completed', 'rejected', 'cancelled']);
    }

    public function isActive(): bool { return $this->status === 'active'; }
}
