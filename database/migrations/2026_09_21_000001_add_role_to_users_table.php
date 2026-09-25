<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('staff')->after('email');
        });

        if ($this->isMysql()) {
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'staff'))");
        }

        // Promosi akun pertama yang sudah ada menjadi admin agar tidak ada kuncian akses.
        $oldest = DB::table('users')->min('id');
        if ($oldest !== null) {
            DB::table('users')->where('id', $oldest)->update(['role' => 'admin']);
        }
    }

    public function down(): void
    {
        if ($this->isMysql()) {
            DB::statement("ALTER TABLE users DROP CONSTRAINT users_role_check");
        }
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    protected function isMysql(): bool
    {
        return DB::connection()->getDriverName() === 'mysql';
    }
};