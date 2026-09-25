<?php

namespace Database\Seeders;

use App\Models\Gadget;
use App\Models\StokLog;
use App\Models\User;
use App\Services\StokService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (App::environment('production')) {
            $this->command?->warn('Seeder dilewati di lingkungan production.');
            return;
        }

        $this->seedAccount();
        $this->seedSampleStock();
    }

    protected function seedAccount(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@gudang.test');
        $password = env('ADMIN_PASSWORD', Str::random(12));

        if (! User::where('email', $adminEmail)->exists()) {
            User::create([
                'name' => 'Administrator',
                'email' => $adminEmail,
                'role' => 'admin',
                'password' => $password,
            ]);

            $this->command?->info("Akun admin dibuat: {$adminEmail}");
            $this->command?->info("Sandi admin: {$password}");
        }

        if (! User::where('email', 'staff@gudang.test')->exists()) {
            User::create([
                'name' => 'Staf Gudang',
                'email' => 'staff@gudang.test',
                'role' => 'staff',
                'password' => Hash::make('staffpassword'),
            ]);
        }
    }

    protected function seedSampleStock(): void
    {
        if (Gadget::count() > 0) {
            return;
        }

        $samples = [
            ['nama_produk' => 'Samsung Galaxy A15', 'sku' => 'GDG-SMA15', 'kategori' => 'SmartPhone', 'harga_beli' => 1850000, 'stock' => 12, 'stok_minimum' => 3, 'satuan' => 'pcs', 'supplier' => 'PT Sinar Jaya', 'lokasi_rak' => 'RAK-A1', 'serial_number' => 'SN-A15-0001'],
            ['nama_produk' => 'Xiaomi Redmi Note 12', 'sku' => 'GDG-RN12', 'kategori' => 'SmartPhone', 'harga_beli' => 1650000, 'stock' => 2, 'stok_minimum' => 4, 'satuan' => 'pcs', 'supplier' => 'PT Sinar Jaya', 'lokasi_rak' => 'RAK-A2', 'serial_number' => 'SN-RN12-0001'],
            ['nama_produk' => 'Asus VivoBook 14', 'sku' => 'GDG-VB14', 'kategori' => 'Laptop', 'harga_beli' => 5200000, 'stock' => 0, 'stok_minimum' => 2, 'satuan' => 'unit', 'supplier' => 'PT Komputer Mandiri', 'lokasi_rak' => 'RAK-B1'],
            ['nama_produk' => 'iPad 9 64GB', 'sku' => 'GDG-IP9', 'kategori' => 'Tablet', 'harga_beli' => 4300000, 'stock' => 5, 'stok_minimum' => 2, 'satuan' => 'unit', 'supplier' => 'PT Gadget Nusantara', 'lokasi_rak' => 'RAK-C1'],
            ['nama_produk' => 'Poco Watch X', 'sku' => 'GDG-PWX', 'kategori' => 'SmartWatch', 'harga_beli' => 750000, 'stock' => 3, 'stok_minimum' => 2, 'satuan' => 'pcs', 'supplier' => 'PT Gadget Nusantara', 'lokasi_rak' => 'RAK-D1'],
        ];

        $admin = User::where('role', 'admin')->first();

        foreach ($samples as $idx => $sample) {
            $stock = $sample['stock'];
            $status = ($stock > 0) ? 'Tersedia' : 'Habis';

            $gadget = Gadget::create(array_merge($sample, ['status' => $status, 'deskripsi' => 'Data contoh dari seeder.']));

            StokService::log($gadget, $stock, 0, $stock, StokLog::TIPE_STOK_AWAL, 'Stok awal (seeder)', $admin);
        }
    }
}