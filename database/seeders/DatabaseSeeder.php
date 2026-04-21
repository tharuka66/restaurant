<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\Room;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $admin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@restaurant.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Restaurant Owner
        $owner = User::create([
            'name'     => 'Demo Owner',
            'email'    => 'owner@demo.com',
            'password' => Hash::make('password'),
            'role'     => 'owner',
        ]);

        // Demo Restaurant
        $restaurant = Restaurant::create([
            'name'          => 'The Grand Bistro',
            'slug'          => 'the-grand-bistro',
            'owner_id'      => $owner->id,
            'email'         => 'info@grandbistro.com',
            'phone'         => '+1-555-0100',
            'address'       => '123 Main Street, Downtown',
            'status'        => 'active',
            'trial_ends_at' => now()->addDays(30),
        ]);

        $owner->update(['restaurant_id' => $restaurant->id]);

        // Staff
        $kitchen = User::create([
            'name'          => 'Kitchen Staff',
            'email'         => 'kitchen@demo.com',
            'password'      => Hash::make('password'),
            'role'          => 'kitchen',
            'restaurant_id' => $restaurant->id,
        ]);

        $cashier = User::create([
            'name'          => 'Cashier Staff',
            'email'         => 'cashier@demo.com',
            'password'      => Hash::make('password'),
            'role'          => 'cashier',
            'restaurant_id' => $restaurant->id,
        ]);

        // Rooms & Tables
        $mainRoom = Room::create(['restaurant_id' => $restaurant->id, 'name' => 'Main Hall']);
        $terrace  = Room::create(['restaurant_id' => $restaurant->id, 'name' => 'Terrace']);

        for ($i = 1; $i <= 8; $i++) {
            Table::create([
                'restaurant_id' => $restaurant->id,
                'room_id'       => $mainRoom->id,
                'number'        => (string)$i,
                'capacity'      => 4,
            ]);
        }
        for ($i = 1; $i <= 4; $i++) {
            Table::create([
                'restaurant_id' => $restaurant->id,
                'room_id'       => $terrace->id,
                'number'        => 'T' . $i,
                'capacity'      => 2,
            ]);
        }

        // Categories & Menu Items
        $starters = Category::create(['restaurant_id' => $restaurant->id, 'name' => 'Starters', 'sort_order' => 1]);
        $mains    = Category::create(['restaurant_id' => $restaurant->id, 'name' => 'Main Course', 'sort_order' => 2]);
        $desserts = Category::create(['restaurant_id' => $restaurant->id, 'name' => 'Desserts', 'sort_order' => 3]);
        $drinks   = Category::create(['restaurant_id' => $restaurant->id, 'name' => 'Drinks', 'sort_order' => 4]);

        $items = [
            [$starters->id, 'Garlic Bread',         'Toasted artisan bread with garlic butter',    4.99,  8],
            [$starters->id, 'Bruschetta',            'Fresh tomatoes on grilled sourdough',         6.99, 10],
            [$starters->id, 'Soup of the Day',       'Ask your server for today\'s special',        5.99, 12],
            [$mains->id,   'Grilled Salmon',         'Atlantic salmon with seasonal vegetables',   22.99, 20],
            [$mains->id,   'Beef Tenderloin',        '8oz tenderloin with red wine reduction',     34.99, 25],
            [$mains->id,   'Mushroom Risotto',       'Arborio rice with wild mushrooms',           16.99, 18],
            [$mains->id,   'Chicken Parmesan',       'Breaded chicken with marinara sauce',        18.99, 20],
            [$desserts->id,'Tiramisu',               'Classic Italian dessert',                     7.99, 10],
            [$desserts->id,'Crème Brûlée',           'French custard with caramel crust',           8.99, 12],
            [$drinks->id,  'Sparkling Water',        '500ml sparkling mineral water',               2.99,  2],
            [$drinks->id,  'Fresh Orange Juice',     'Freshly squeezed orange juice',               4.99,  3],
            [$drinks->id,  'House Wine (Glass)',     'Red or white, ask your server',               8.99,  2],
        ];

        foreach ($items as [$catId, $name, $desc, $price, $prep]) {
            MenuItem::create([
                'restaurant_id'     => $restaurant->id,
                'category_id'       => $catId,
                'name'              => $name,
                'description'       => $desc,
                'price'             => $price,
                'prep_time_minutes' => $prep,
                'available'         => true,
            ]);
        }
    }
}
