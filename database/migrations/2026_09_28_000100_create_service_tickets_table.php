<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket', 40)->unique();
            $table->foreignId('parent_id')->nullable()->constrained('service_tickets')->cascadeOnDelete();
            $table->string('customer_name', 100);
            $table->string('customer_phone', 30);
            $table->text('customer_address')->nullable();
            $table->string('device_brand', 50);
            $table->string('device_model', 100);
            $table->string('imei_or_serial', 100)->nullable();
            $table->string('passcode', 50)->nullable();
            $table->text('completeness')->nullable();
            $table->text('initial_condition')->nullable();
            $table->text('problem_description');
            $table->text('technician_notes')->nullable();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cashier_id')->constrained('users');
            $table->string('status', 20)->default('pending');
            $table->decimal('down_payment', 15, 2)->default(0);
            $table->decimal('service_fee', 15, 2)->default(0);
            $table->decimal('sparepart_fee', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->decimal('remaining_cost', 15, 2)->default(0);
            $table->integer('warranty_days')->default(30);
            $table->date('warranty_until')->nullable();
            $table->json('qc_checklist')->nullable();
            $table->string('payment_method', 20)->nullable();
            $table->decimal('paid_amount', 15, 2)->nullable();
            $table->string('payment_ref', 100)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('customer_phone');
            $table->index('imei_or_serial');
            $table->index(['created_at', 'status']);
        });

        Schema::create('service_ticket_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_ticket_id')->constrained('service_tickets')->cascadeOnDelete();
            $table->foreignId('gadget_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('item_type', 20);
            $table->string('item_name', 150);
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('sell_price', 15, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            $table->index('service_ticket_id');
        });

        Schema::create('service_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_ticket_id')->constrained('service_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['service_ticket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_status_logs');
        Schema::dropIfExists('service_ticket_items');
        Schema::dropIfExists('service_tickets');
    }
};