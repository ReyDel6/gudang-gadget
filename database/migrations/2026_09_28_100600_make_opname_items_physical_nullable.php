<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // physical_stock & difference dibuat nullable agar item yang belum
        // dihitung dapat disimpan (null = belum di-scan).
        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->integer('physical_stock')->nullable()->change();
            $table->integer('difference')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->integer('physical_stock')->default(0)->change();
            $table->integer('difference')->default(0)->change();
        });
    }
};