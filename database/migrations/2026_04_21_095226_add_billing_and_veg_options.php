<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->boolean('is_veg')->default(false)->after('prep_time_minutes');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->decimal('service_charge_percent', 5, 2)->default(0)->after('status');
            $table->decimal('tax_percent', 5, 2)->default(0)->after('service_charge_percent');
            $table->decimal('discount_percent', 5, 2)->default(0)->after('tax_percent');
            $table->decimal('vat_percent', 5, 2)->default(0)->after('discount_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('is_veg');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['service_charge_percent', 'tax_percent', 'discount_percent', 'vat_percent']);
        });
    }
};
