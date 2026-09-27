<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->decimal('diskon', 14, 2)->default(0)->after('total');
            $table->decimal('pajak', 5, 2)->default(0)->after('diskon');
        });

        Schema::table('pembelians', function (Blueprint $table) {
            $table->decimal('diskon', 14, 2)->default(0)->after('total');
            $table->decimal('pajak', 5, 2)->default(0)->after('diskon');
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['diskon', 'pajak']);
        });

        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn(['diskon', 'pajak']);
        });
    }
};