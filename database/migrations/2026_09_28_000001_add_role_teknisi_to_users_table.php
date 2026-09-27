<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->isMysql()) {
            DB::statement("ALTER TABLE users DROP CONSTRAINT users_role_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'staff', 'reseller', 'teknisi'))");
        }
    }

    public function down(): void
    {
        if ($this->isMysql()) {
            DB::statement("ALTER TABLE users DROP CONSTRAINT users_role_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'staff', 'reseller'))");
        }
    }

    protected function isMysql(): bool
    {
        return DB::connection()->getDriverName() === 'mysql';
    }
};