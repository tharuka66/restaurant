<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id', 'category_id', 'name', 'description',
        'price', 'prep_time_minutes', 'available', 'image', 'sort_order',
    ];

    protected $casts = ['price' => 'decimal:2', 'available' => 'boolean'];

    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function category()   { return $this->belongsTo(Category::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }

    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/placeholder.png');
    }
}
