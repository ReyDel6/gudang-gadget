<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gadget_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('harga_beli_lama', 15, 2)->default(0);
            $table->decimal('harga_beli_baru', 15, 2)->default(0);
            $table->string('perubah', 150)->nullable();
            $table->string('alasan', 255)->nullable();
            $table->timestamps();

            $table->index('gadget_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_histories');
    }
};