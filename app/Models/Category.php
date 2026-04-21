<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['restaurant_id', 'name', 'sort_order'];

    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function menuItems()  { return $this->hasMany(MenuItem::class)->where('available', true)->orderBy('sort_order'); }
    public function allItems()   { return $this->hasMany(MenuItem::class)->orderBy('sort_order'); }
}
