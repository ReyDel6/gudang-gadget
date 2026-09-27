<?php

namespace Database\Seeders;

use App\Models\Gadget;
use App\Models\StoreBanner;
use App\Models\StoreSetting;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'store_name' => 'Gudang Gadget',
            'whatsapp_number' => '6281234567890',
            'store_address' => 'Jl. Melati No. 12, Kelapa Gading, Jakarta Utara',
            'jam_operasional' => 'Senin – Sabtu 09.00–21.00 WIB, Minggu 10.00–18.00 WIB',
            'maps_embed' => 'https://maps.google.com/maps?q=Kelapa%20Gading%20Jakarta&t=&z=13&ie=UTF8&iwloc=&output=embed',
            'store_photo' => '',
            'tags_line' => 'Gadget Original, Ready Stock, Garansi Toko & Resmi',
            'instagram_url' => '#',
            'facebook_url' => '#',
            'tiktok_url' => '#',
        ];

        foreach ($defaults as $key => $value) {
            StoreSetting::set($key, $value);
        }

        if (StoreBanner::count() === 0) {
            StoreBanner::create([
                'title' => 'Trade-In Gadget Lama',
                'subtitle' => 'Tukar tambah iPhone, iPad, dan MacBook Anda di Gudang Gadget.',
                'image_path' => 'thumbnails/no-image.jpg',
                'cta_link' => route('shop.katalog'),
                'order_position' => 1,
                'is_active' => true,
            ]);
            StoreBanner::create([
                'title' => 'Homecare Gratis 3 Bulan',
                'subtitle' => 'Setiap pembelian iPhone dapat layanan service gratis untuk 3 bulan pertama.',
                'image_path' => 'thumbnails/no-image.jpg',
                'cta_link' => route('shop.katalog'),
                'order_position' => 2,
                'is_active' => true,
            ]);
        }

        $spek = [
            'Iphone 12' => ['Layar' => '6.1" Super Retina XDR OLED', 'Chipset' => 'A14 Bionic', 'RAM/Storage' => '4GB / 64GB', 'Kamera' => 'Dual 12MP', 'Baterai' => '2815 mAh', 'Kondisi Garansi' => 'Segel resmi import'],
            'Iphone 13' => ['Layar' => '6.1" Super Retina XDR OLED', 'Chipset' => 'A15 Bionic', 'RAM/Storage' => '4GB / 128GB', 'Kamera' => 'Dual 12MP', 'Baterai' => '3240 mAh', 'Kondisi Garansi' => 'Segel resmi import'],
            'Iphone 14' => ['Layar' => '6.1" Super Retina XDR OLED', 'Chipset' => 'A15 Bionic', 'RAM/Storage' => '6GB / 128GB', 'Kamera' => 'Dual 12MP', 'Baterai' => '3279 mAh', 'Kondisi Garansi' => 'Segel resmi import'],
            'Iphone 15' => ['Layar' => '6.1" Super Retina XDR OLED — Dynamic Island', 'Chipset' => 'A16 Bionic', 'RAM/Storage' => '6GB / 128GB', 'Kamera' => 'Dual 48MP', 'Baterai' => '3349 mAh', 'Kondisi Garansi' => 'Segel resmi IMEI'],
            'Iphone 16' => ['Layar' => '6.1" Super Retina XDR OLED — Dynamic Island', 'Chipset' => 'A18', 'RAM/Storage' => '8GB / 128GB', 'Kamera' => 'Dual 48MP', 'Baterai' => '3561 mAh', 'Kondisi Garansi' => 'Segel resmi IMEI'],
            'Ipad Air M1' => ['Layar' => '10.9" Liquid Retina', 'Chipset' => 'Apple M1', 'RAM/Storage' => '8GB / 64GB', 'Kamera' => '12MP Wide', 'Baterai' => 'Up to 10 jam', 'Kondisi Garansi' => 'Garansi resmi 1 tahun'],
            'Macbook Air M1 2020' => ['Layar' => '13.3" Retina IPS', 'Chipset' => 'Apple M1', 'RAM/Storage' => '8GB / 256GB SSD', 'Port' => '2x Thunderbolt / USB 4', 'Baterai' => 'Up to 18 jam', 'Kondisi Garansi' => 'Garansi resmi 1 tahun'],
        ];

        $featured = ['Iphone 13', 'Iphone 16', 'Macbook Air M1 2020'];

        foreach ($spek as $nama => $spec) {
            $gadget = Gadget::publik()->where('nama_produk', $nama)->first();
            if (! $gadget) {
                continue;
            }
            $gadget->update([
                'condition' => 'new',
                'specifications' => $spec,
                'warranty_info' => $spec['Kondisi Garansi'] ?? null,
            ]);
            $gadget->forceFill(['is_published' => true])->saveQuietly();
            $gadget->forceFill(['is_featured' => in_array($nama, $featured, true)])->saveQuietly();
        }

        // Contoh harga promo / harga coret untuk demo vitrin.
        $gadgetPromo = Gadget::publik()->where('nama_produk', 'Iphone 14')->first();
        if ($gadgetPromo) {
            $gadgetPromo->forceFill(['harga_promo' => 10200000])->saveQuietly();
        }

        // Contoh harga tier Grosir & Partai untuk demo modul reseller.
        $tiersDemo = [
            'Iphone 14' => [
                ['tier_name' => 'Grosir', 'min_qty' => 3, 'max_qty' => null, 'price' => 10400000],
                ['tier_name' => 'Partai', 'min_qty' => 10, 'max_qty' => null, 'price' => 10000000],
            ],
            'Macbook Air M1 2020' => [
                ['tier_name' => 'Grosir', 'min_qty' => 3, 'max_qty' => null, 'price' => 9900000],
                ['tier_name' => 'Partai', 'min_qty' => 10, 'max_qty' => null, 'price' => 9600000],
            ],
        ];
        foreach ($tiersDemo as $nama => $tiers) {
            $gadget = Gadget::publik()->where('nama_produk', $nama)->first();
            if (! $gadget) {
                continue;
            }
            foreach ($tiers as $t) {
                $gadget->tierPrices()->updateOrCreate(
                    ['tier_name' => $t['tier_name']],
                    ['min_qty' => $t['min_qty'], 'max_qty' => $t['max_qty'], 'price' => $t['price']]
                );
            }
        }
    }
}