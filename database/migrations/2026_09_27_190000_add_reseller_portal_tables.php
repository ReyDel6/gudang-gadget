<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->isMysql()) {
            DB::statement("ALTER TABLE users DROP CONSTRAINT users_role_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'staff', 'reseller'))");
        }

        Schema::create('reseller_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('store_name', 150);
            $table->string('owner_name', 150);
            $table->string('phone', 30);
            $table->string('whatsapp', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('ktp_path', 255)->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('customer_type', 20)->default('retail')->after('customer_phone');
            $table->boolean('is_dropship')->default(false)->after('customer_type');
            $table->string('sender_name', 150)->nullable()->after('is_dropship');
            $table->string('sender_phone', 30)->nullable()->after('sender_name');
            $table->string('recipient_name', 150)->nullable()->after('sender_phone');
            $table->text('recipient_address')->nullable()->after('recipient_name');
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn([
                'customer_type',
                'is_dropship',
                'sender_name',
                'sender_phone',
                'recipient_name',
                'recipient_address',
            ]);
        });

        Schema::dropIfExists('reseller_profiles');

        if ($this->isMysql()) {
            DB::statement("ALTER TABLE users DROP CONSTRAINT users_role_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'staff'))");
        }
    }

    protected function isMysql(): bool
    {
        return DB::connection()->getDriverName() === 'mysql';
    }
};