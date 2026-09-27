<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice', 40)->unique();
            $table->date('tanggal');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name', 150)->nullable();
            $table->string('supplier', 150)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();

            $table->index('tanggal');
        });

        Schema::create('pembelian_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelians')->cascadeOnDelete();
            $table->foreignId('gadget_id')->constrained('products');
            $table->string('nama_produk', 255);
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->integer('qty')->unsigned();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();

            $table->index('gadget_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_items');
        Schema::dropIfExists('pembelians');
    }
};