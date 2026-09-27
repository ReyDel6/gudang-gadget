<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gadget_tier_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gadget_id')->constrained('products')->cascadeOnDelete();
            $table->string('tier_name', 80);
            $table->integer('min_qty');
            $table->integer('max_qty')->nullable();
            $table->decimal('price', 12, 2);
            $table->timestamps();

            $table->index(['gadget_id', 'min_qty']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gadget_tier_prices');
    }
};