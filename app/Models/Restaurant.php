<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'owner_id', 'email', 'phone',
        'address', 'logo', 'status', 'rejection_reason', 'trial_ends_at',
    ];

    protected $casts = ['trial_ends_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($restaurant) {
            if (empty($restaurant->slug)) {
                $restaurant->slug = Str::slug($restaurant->name);
            }
        });
    }

    public function owner()       { return $this->belongsTo(User::class, 'owner_id'); }
    public function staff()       { return $this->hasMany(User::class)->whereIn('role', ['kitchen', 'cashier']); }
    public function rooms()       { return $this->hasMany(Room::class); }
    public function tables()      { return $this->hasMany(Table::class); }
    public function categories()  { return $this->hasMany(Category::class)->orderBy('sort_order'); }
    public function menuItems()   { return $this->hasMany(MenuItem::class); }
    public function sessions()    { return $this->hasMany(TableSession::class); }
    public function orders()      { return $this->hasMany(Order::class); }
    public function bills()       { return $this->hasMany(Bill::class); }

    public function isActive(): bool    { return $this->status === 'active'; }
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isSuspended(): bool { return $this->status === 'suspended'; }
}
