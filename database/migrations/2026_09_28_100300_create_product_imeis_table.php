<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_imeis')) {
            Schema::create('product_imeis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('gadget_id')->constrained('products')->cascadeOnDelete();
                $table->string('imei', 50)->unique();
                $table->string('serial_number', 50)->nullable();
                $table->string('color', 50)->nullable();
                $table->string('status', 20)->default('available');
                $table->string('masuk_via', 40)->nullable();
                $table->timestamp('masuk_at')->nullable();
                $table->foreignId('penjualan_item_id')->nullable()->constrained('penjualan_items')->nullOnDelete();
                $table->timestamp('sold_at')->nullable();
                $table->date('warranty_expired_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['gadget_id', 'status']);
                $table->index('penjualan_item_id');
            });
        }

        if (! Schema::hasColumn('penjualan_items', 'imei_id')) {
            Schema::table('penjualan_items', function (Blueprint $table) {
                $table->foreignId('imei_id')->nullable()->after('qty')->constrained('product_imeis')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('service_tickets', 'imei')) {
            Schema::table('service_tickets', function (Blueprint $table) {
                $table->string('imei', 50)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('service_tickets', 'imei')) {
            Schema::table('service_tickets', function (Blueprint $table) {
                $table->dropColumn('imei');
            });
        }
        if (Schema::hasColumn('penjualan_items', 'imei_id')) {
            Schema::table('penjualan_items', function (Blueprint $table) {
                $table->dropConstrainedForeignId('imei_id');
            });
        }
        Schema::dropIfExists('product_imeis');
    }
};