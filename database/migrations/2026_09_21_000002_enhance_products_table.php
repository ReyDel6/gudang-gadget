<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 50)->unique()->nullable()->after('id');
            $table->string('supplier', 255)->nullable()->after('kategori');
            $table->string('lokasi_rak', 100)->nullable()->after('supplier');
            $table->decimal('harga_beli', 15, 2)->default(0)->after('deskripsi');
            $table->string('satuan', 50)->nullable()->default('pcs')->after('harga_beli');
            $table->unsignedInteger('stok_minimum')->nullable()->after('stock');
            $table->string('serial_number', 150)->nullable()->after('stok_minimum');
            $table->softDeletes();

            $table->integer('stock')->unsigned()->change();
            $table->text('deskripsi')->nullable()->change();

            $table->index('kategori', 'products_kategori_index');
            $table->index('status', 'products_status_index');
            $table->index(['kategori', 'status'], 'products_kategori_status_index');
        });

        if ($this->isMysql()) {
            DB::statement("ALTER TABLE products ADD CONSTRAINT products_status_check CHECK (status IN ('Tersedia', 'Habis', 'Tidak Dijual'))");
        }

        DB::table('products')->whereNull('harga_beli')->update(['harga_beli' => 0]);
        DB::table('products')->whereNull('satuan')->update(['satuan' => 'pcs']);
    }

    public function down(): void
    {
        if ($this->isMysql()) {
            DB::statement("ALTER TABLE products DROP CONSTRAINT products_status_check");
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_kategori_status_index');
            $table->dropIndex('products_status_index');
            $table->dropIndex('products_kategori_index');

            $table->integer('stock')->change();

            $table->dropSoftDeletes();
            $table->dropColumn([
                'sku',
                'supplier',
                'lokasi_rak',
                'harga_beli',
                'satuan',
                'stok_minimum',
                'serial_number',
            ]);
        });
    }

    protected function isMysql(): bool
    {
        return \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'mysql';
    }
};