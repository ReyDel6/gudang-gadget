<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('stock_opnames')) {
            Schema::create('stock_opnames', function (Blueprint $table) {
                $table->id();
                $table->string('no_invoice', 40)->unique();
                $table->foreignId('auditor_id')->constrained('users');
                $table->string('category_filter', 50)->nullable();
                $table->string('status', 20)->default('in_progress');
                $table->integer('total_system_items')->default(0);
                $table->integer('total_physical_items')->default(0);
                $table->integer('total_difference')->default(0);
                $table->text('notes')->nullable();
                $table->timestamp('started_at');
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'started_at']);
            });
        }

        if (! Schema::hasTable('stock_opname_items')) {
            Schema::create('stock_opname_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete();
                $table->foreignId('gadget_id')->constrained('products')->cascadeOnDelete();
                $table->integer('system_stock');
                $table->integer('physical_stock')->nullable();
                $table->integer('difference')->nullable();
                $table->string('notes', 255)->nullable();
                $table->timestamps();

                $table->index(['stock_opname_id', 'gadget_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
    }
};