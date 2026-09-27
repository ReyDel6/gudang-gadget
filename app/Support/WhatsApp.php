<?php

namespace App\Support;

use App\Models\Gadget;
use App\Models\StoreSetting;

class WhatsApp
{
    public static function link(Gadget $product): string
    {
        $number = preg_replace('/[^0-9]/', '', (string) StoreSetting::get('whatsapp_number', '6281234567890'));
        $store = StoreSetting::get('store_name', 'Gudang Gadget');

        $variant = array_filter([
            $product->specifications['RAM/Storage'] ?? null,
            $product->kondisi_label,
        ]);
        $lines = [
            "Halo Admin {$store}, saya tertarik membeli produk ini:",
            "• Produk: {$product->nama_produk}",
            ...$variant,
            "• Harga: Rp " . number_format((float) $product->harga_jual, 0, ',', '.'),
            "• Link: " . route('shop.produk', $product->id),
            "Apakah unit ini masih tersedia?",
        ];

        return 'https://wa.me/' . $number . '/?text=' . rawurlencode(implode("\n", $lines));
    }
}