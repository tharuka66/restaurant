<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('table_session_id')->constrained()->onDelete('cascade');
            $table->enum('status', [
                'pending_cancel',
                'pending_approval',
                'approved',
                'preparing',
                'ready',
                'completed',
                'rejected',
                'cancelled',
            ])->default('pending_cancel');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('cancel_deadline')->nullable();
            $table->integer('eta_minutes')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
