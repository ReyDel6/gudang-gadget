<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrasis', function (Blueprint $table) {
            $table->id('noreg');
            $table->string('nama');
            $table->enum('jk', ['Laki-laki', 'Perempuan']);
            $table->string('alamat');
            $table->string('agama');
            $table->string('asal_sekolah');
            $table->string('jurusan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrasis');
    }
};
