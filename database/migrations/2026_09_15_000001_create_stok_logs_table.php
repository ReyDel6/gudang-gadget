<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gadget_id');
            $table->integer('perubahan');
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->string('keterangan')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('gadget_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_logs');
    }
};