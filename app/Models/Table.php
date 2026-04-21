<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id', 'room_id', 'number', 'capacity', 'qr_token', 'status',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($table) {
            if (empty($table->qr_token)) {
                $table->qr_token = Str::uuid();
            }
        });
    }

    public function restaurant()    { return $this->belongsTo(Restaurant::class); }
    public function room()          { return $this->belongsTo(Room::class); }
    public function sessions()      { return $this->hasMany(TableSession::class); }
    public function activeSession() { return $this->hasOne(TableSession::class)->where('status', 'active'); }

    public function qrUrl(): string
    {
        return route('customer.scan', $this->qr_token);
    }
}
