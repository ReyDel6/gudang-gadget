<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('customer_phone', 30)->nullable()->after('customer');
            $table->string('payment_method', 20)->default('cash')->after('keterangan');
            $table->decimal('paid_amount', 15, 2)->nullable()->after('total');
            $table->decimal('change_amount', 15, 2)->nullable()->after('paid_amount');
            $table->string('payment_ref', 100)->nullable()->after('change_amount');
            $table->string('payment_status', 20)->default('paid')->after('payment_ref');
            $table->timestamp('voided_at')->nullable()->after('payment_status');
            $table->string('voided_by', 150)->nullable()->after('voided_at');
            $table->string('void_reason', 255)->nullable()->after('voided_by');
        });

        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name', 150)->nullable();
            $table->decimal('start_cash', 15, 2)->default(0);
            $table->decimal('end_cash', 15, 2)->nullable();
            $table->decimal('actual_cash', 15, 2)->nullable();
            $table->decimal('difference', 15, 2)->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'opened_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');

        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn([
                'customer_phone',
                'payment_method',
                'paid_amount',
                'change_amount',
                'payment_ref',
                'payment_status',
                'voided_at',
                'voided_by',
                'void_reason',
            ]);
        });
    }
};