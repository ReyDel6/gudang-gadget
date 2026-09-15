<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gadget_fotos', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('url');
            $table->timestamps();

            $table->primary('id');
            $table->foreign('id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gadget_fotos');
    }
};