<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'restaurant_id'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function ownedRestaurant()
    {
        return $this->hasOne(Restaurant::class, 'owner_id');
    }

    public function isAdmin(): bool       { return $this->role === 'admin'; }
    public function isOwner(): bool       { return $this->role === 'owner'; }
    public function isKitchen(): bool     { return $this->role === 'kitchen'; }
    public function isCashier(): bool     { return $this->role === 'cashier'; }
    public function isCustomer(): bool    { return $this->role === 'customer'; }

    public function isStaff(): bool
    {
        return in_array($this->role, ['kitchen', 'cashier']);
    }
}
