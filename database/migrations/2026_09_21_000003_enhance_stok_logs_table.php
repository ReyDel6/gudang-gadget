<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_logs', function (Blueprint $table) {
            $table->string('tipe', 30)->nullable()->after('keterangan');
            $table->string('alasan', 255)->nullable()->after('tipe');
            $table->string('user_name', 255)->nullable()->after('user_id');

            $table->index('created_at', 'stok_logs_created_at_index');
            $table->index(['gadget_id', 'created_at'], 'stok_logs_gadget_created_index');
            $table->index('tipe', 'stok_logs_tipe_index');

            $table->foreign('user_id', 'stok_logs_user_id_foreign')
                ->references('id')->on('users')
                ->nullOnDelete();
        });

        // Snapshot identitas pelaku untuk log yang sudah ada.
        // (update per-baris agar kompatibel dengan SQLite & MySQL)
        $rows = \Illuminate\Support\Facades\DB::table('stok_logs')
            ->join('users', 'users.id', '=', 'stok_logs.user_id')
            ->whereNull('stok_logs.user_name')
            ->select('stok_logs.id', 'users.name')
            ->get();

        foreach ($rows as $row) {
            \Illuminate\Support\Facades\DB::table('stok_logs')
                ->where('id', $row->id)
                ->update(['user_name' => $row->name]);
        }
    }

    public function down(): void
    {
        Schema::table('stok_logs', function (Blueprint $table) {
            $table->dropForeign('stok_logs_user_id_foreign');
            $table->dropIndex('stok_logs_tipe_index');
            $table->dropIndex('stok_logs_gadget_created_index');
            $table->dropIndex('stok_logs_created_at_index');
            $table->dropColumn(['tipe', 'alasan', 'user_name']);
        });
    }
};