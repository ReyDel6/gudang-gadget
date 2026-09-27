<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publik_orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 40)->unique();
            $table->foreignId('reseller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_pelanggan', 150);
            $table->string('telepon', 30);
            $table->string('alamat', 500)->nullable();
            $table->string('catatan', 500)->nullable();
            $table->enum('payment_method', ['transfer', 'cod', 'store'])->default('transfer');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('ongkos_kirim', 12, 2)->nullable();
            $table->string('kurir', 100)->nullable();
            $table->decimal('total', 12, 2)->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->string('bukti_path', 255)->nullable();
            $table->foreignId('penjualan_id')->nullable()->constrained('penjualans')->nullOnDelete();
            $table->string('catatan_admin', 500)->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['created_at', 'status']);
        });

        Schema::create('publik_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('publik_orders')->cascadeOnDelete();
            $table->unsignedBigInteger('gadget_id')->nullable();
            $table->foreign('gadget_id')->references('id')->on('products')->nullOnDelete();
            $table->string('nama_produk', 150);
            $table->decimal('harga_satuan', 12, 2)->default(0);
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publik_order_items');
        Schema::dropIfExists('publik_orders');
    }
};