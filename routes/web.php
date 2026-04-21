<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\RestaurantController as AdminRestaurant;
use App\Http\Controllers\Auth\RegisterRestaurantController;
use App\Http\Controllers\Customer\OrderingController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboard;
use App\Http\Controllers\Owner\MenuController;
use App\Http\Controllers\Owner\OrderController as OwnerOrder;
use App\Http\Controllers\Owner\StaffController;
use App\Http\Controllers\Owner\TableController;
use App\Http\Controllers\Staff\CashierController;
use App\Http\Controllers\Staff\KitchenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('welcome'));

// Restaurant Owner Registration
Route::get('/register/restaurant', [RegisterRestaurantController::class, 'create'])->name('register.restaurant');
Route::post('/register/restaurant', [RegisterRestaurantController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Customer Routes (QR-based, session-driven)
|--------------------------------------------------------------------------
*/
Route::prefix('table')->name('customer.')->group(function () {
    Route::post('/session/start', [OrderingController::class, 'startSession'])->name('session.start');
    Route::get('/menu', [OrderingController::class, 'menu'])->name('menu');
    Route::post('/order', [OrderingController::class, 'placeOrder'])->name('order.place');
    Route::post('/order/{order}/cancel', [OrderingController::class, 'cancelOrder'])->name('order.cancel');
    Route::get('/status', [OrderingController::class, 'orderStatus'])->name('order.status');
    Route::get('/{qrToken}', [OrderingController::class, 'scan'])->name('scan');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Redirect based on role after login
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return match($user->role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'owner'    => redirect()->route('owner.dashboard'),
            'kitchen'  => redirect()->route('kitchen.index'),
            'cashier'  => redirect()->route('cashier.index'),
            default    => redirect('/'),
        };
    })->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Polling for new orders
    |----------------------------------------------------------------------
    */
    Route::get('/api/new-orders', [\App\Http\Controllers\Api\OrderNotificationsController::class, 'poll'])->name('api.orders.poll');
    Route::post('/api/orders/{order}/accept', [\App\Http\Controllers\Api\OrderNotificationsController::class, 'accept'])->name('api.orders.accept');
    Route::post('/api/orders/{order}/reject', [\App\Http\Controllers\Api\OrderNotificationsController::class, 'reject'])->name('api.orders.reject');

    /*
    |----------------------------------------------------------------------
    | Super Admin
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        Route::get('/restaurants', [AdminRestaurant::class, 'index'])->name('restaurants.index');
        Route::get('/restaurants/{restaurant}', [AdminRestaurant::class, 'show'])->name('restaurants.show');
        Route::post('/restaurants/{restaurant}/approve', [AdminRestaurant::class, 'approve'])->name('restaurants.approve');
        Route::post('/restaurants/{restaurant}/reject', [AdminRestaurant::class, 'reject'])->name('restaurants.reject');
        Route::post('/restaurants/{restaurant}/suspend', [AdminRestaurant::class, 'suspend'])->name('restaurants.suspend');
        Route::post('/restaurants/{restaurant}/restore', [AdminRestaurant::class, 'restore'])->name('restaurants.restore');
        Route::post('/restaurants/{restaurant}/extend-trial', [AdminRestaurant::class, 'extendTrial'])->name('restaurants.extend-trial');
    });

    /*
    |----------------------------------------------------------------------
    | Restaurant Owner
    |----------------------------------------------------------------------
    */
    Route::prefix('owner')->name('owner.')->middleware('role:owner')->group(function () {
        Route::get('/pending', [OwnerDashboard::class, 'pending'])->name('pending');
        Route::get('/register-restaurant', [OwnerDashboard::class, 'registerRestaurant'])->name('register-restaurant');

        Route::middleware('restaurant.active')->group(function () {
            Route::get('/dashboard', [OwnerDashboard::class, 'index'])->name('dashboard');
            Route::post('/settings', [OwnerDashboard::class, 'updateSettings'])->name('settings.update');

            // Menu
            Route::prefix('menu')->name('menu.')->group(function () {
                Route::get('/', [MenuController::class, 'index'])->name('index');
                Route::post('/categories', [MenuController::class, 'storeCategory'])->name('category.store');
                Route::put('/categories/{category}', [MenuController::class, 'updateCategory'])->name('category.update');
                Route::delete('/categories/{category}', [MenuController::class, 'destroyCategory'])->name('category.destroy');
                Route::post('/items', [MenuController::class, 'storeItem'])->name('item.store');
                Route::put('/items/{item}', [MenuController::class, 'updateItem'])->name('item.update');
                Route::delete('/items/{item}', [MenuController::class, 'destroyItem'])->name('item.destroy');
                Route::post('/items/{item}/toggle', [MenuController::class, 'toggleAvailability'])->name('item.toggle');
            });

            // Tables & Rooms
            Route::prefix('tables')->name('tables.')->group(function () {
                Route::get('/', [TableController::class, 'index'])->name('index');
                Route::post('/rooms', [TableController::class, 'storeRoom'])->name('room.store');
                Route::put('/rooms/{room}', [TableController::class, 'updateRoom'])->name('room.update');
                Route::delete('/rooms/{room}', [TableController::class, 'destroyRoom'])->name('room.destroy');
                Route::post('/', [TableController::class, 'storeTable'])->name('store');
                Route::delete('/{table}', [TableController::class, 'destroyTable'])->name('destroy');
                Route::get('/{table}/qr', [TableController::class, 'showQr'])->name('qr');
                Route::get('/{table}/qr/download', [TableController::class, 'downloadQr'])->name('qr.download');
            });

            // Staff
            Route::prefix('staff')->name('staff.')->group(function () {
                Route::get('/', [StaffController::class, 'index'])->name('index');
                Route::post('/', [StaffController::class, 'store'])->name('store');
                Route::delete('/{user}', [StaffController::class, 'destroy'])->name('destroy');
            });

            // Orders
            Route::prefix('orders')->name('orders.')->group(function () {
                Route::get('/', [OwnerOrder::class, 'index'])->name('index');
                Route::post('/{order}/approve', [OwnerOrder::class, 'approve'])->name('approve');
                Route::post('/{order}/reject', [OwnerOrder::class, 'reject'])->name('reject');
            });
        });
    });

    /*
    |----------------------------------------------------------------------
    | Kitchen Staff
    |----------------------------------------------------------------------
    */
    Route::prefix('kitchen')->name('kitchen.')->middleware('role:kitchen')->group(function () {
        Route::get('/', [KitchenController::class, 'index'])->name('index');
        Route::post('/orders/{order}/status/{status}', [KitchenController::class, 'updateStatus'])->name('order.status');
    });

    /*
    |----------------------------------------------------------------------
    | Cashier Staff
    |----------------------------------------------------------------------
    */
    Route::prefix('cashier')->name('cashier.')->middleware('role:cashier')->group(function () {
        Route::get('/', [CashierController::class, 'index'])->name('index');
        Route::get('/session/{session}', [CashierController::class, 'session'])->name('session');
        Route::post('/session/{session}/pay', [CashierController::class, 'markPaid'])->name('pay');
        Route::get('/session/{session}/bill/print', [CashierController::class, 'printBill'])->name('bill.print');
    });
});

require __DIR__.'/auth.php';
