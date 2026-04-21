<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['restaurant_id', 'name', 'description'];

    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function tables()     { return $this->hasMany(Table::class); }
}
