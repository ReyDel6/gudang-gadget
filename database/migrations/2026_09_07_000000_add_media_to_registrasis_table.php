<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrasis', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('jurusan');
            $table->string('video')->nullable()->after('foto');
        });
    }

    public function down(): void
    {
        Schema::table('registrasis', function (Blueprint $table) {
            $table->dropColumn(['foto', 'video']);
        });
    }
};
