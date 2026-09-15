<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('registrasis');
    }

    public function down(): void
    {
        // Tidak dikembalikan: sisa tabel proyek lama sudah dihapus permanen.
    }
};