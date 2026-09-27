<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_published')->default(true)->after('status');
            $table->boolean('is_featured')->default(false)->after('is_published');
            $table->string('condition', 20)->default('new')->after('is_featured');
            $table->json('specifications')->nullable()->after('condition');
            $table->string('warranty_info', 255)->nullable()->after('specifications');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_published', 'is_featured', 'condition', 'specifications', 'warranty_info']);
        });
    }
};