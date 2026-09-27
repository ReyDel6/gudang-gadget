<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('trade_in_masters')) {
            Schema::create('trade_in_masters', function (Blueprint $table) {
                $table->id();
                $table->string('brand', 50);
                $table->string('model_name', 100);
                $table->string('capacity', 50)->nullable();
                $table->decimal('base_price_grade_a', 15, 2);
                $table->decimal('price_grade_b', 15, 2);
                $table->decimal('price_grade_c', 15, 2);
                $table->decimal('price_grade_d', 15, 2);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['brand', 'model_name', 'capacity']);
            });
        }

        if (! Schema::hasColumn('penjualans', 'trade_in_value')) {
            Schema::table('penjualans', function (Blueprint $table) {
                $table->decimal('trade_in_value', 15, 2)->default(0)->after('payment_ref');
                $table->string('trade_in_desc', 200)->nullable()->after('trade_in_value');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('penjualans', 'trade_in_desc')) {
            Schema::table('penjualans', function (Blueprint $table) {
                $table->dropColumn('trade_in_desc');
            });
        }
        if (Schema::hasColumn('penjualans', 'trade_in_value')) {
            Schema::table('penjualans', function (Blueprint $table) {
                $table->dropColumn('trade_in_value');
            });
        }
        Schema::dropIfExists('trade_in_masters');
    }
};