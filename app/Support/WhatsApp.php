<?php

namespace App\Support;

use App\Models\Gadget;
use App\Models\StoreSetting;

class WhatsApp
{
    public static function link(Gadget $product): string
    {
        $number = self::number();
        $store = StoreSetting::get('store_name', 'Gudang Gadget');

        $variant = array_filter([
            $product->specifications['RAM/Storage'] ?? null,
            $product->kondisi_label,
        ]);
        $lines = [
            "Halo Admin {$store}, saya tertarik membeli produk ini:",
            "• Produk: {$product->nama_produk}",
            ...$variant,
            "• Harga: Rp " . number_format($product->harga_aktif, 0, ',', '.'),
        ];
        if ($product->harga_promo_aktif) {
            $lines[] = "• Promo: hemat {$product->diskon_persen}% (harga coret Rp " . number_format((float) $product->harga_jual, 0, ',', '.') . ')';
        }
        $lines[] = "• Link: " . route('shop.produk', $product->id);
        $lines[] = "Apakah unit ini masih tersedia?";

        return self::build($number, implode("\n", $lines));
    }

    public static function questionLink(Gadget $product, string $question): string
    {
        $number = self::number();
        $store = StoreSetting::get('store_name', 'Gudang Gadget');

        $lines = [
            "Halo Admin {$store}, {$question}",
            "• Produk: {$product->nama_produk}",
            "• Harga: Rp " . number_format($product->harga_aktif, 0, ',', '.'),
            "• Link: " . route('shop.produk', $product->id),
        ];

        return self::build($number, implode("\n", $lines));
    }

    public static function grosirLink(Gadget $product): string
    {
        $number = self::number();
        $store = StoreSetting::get('store_name', 'Gudang Gadget');

        $lines = [
            "Halo Admin {$store}, saya mau tanya harga grosir/partai:",
            "• Produk: {$product->nama_produk}",
        ];
        foreach ($product->tierPrices->sortBy('min_qty') as $tier) {
            $sampai = $tier->max_qty ? " sampai {$tier->max_qty} unit" : ' atau lebih';
            $lines[] = "• Harga {$tier->tier_name} (min {$tier->min_qty}{$sampai}): Rp " . number_format((float) $tier->price, 0, ',', '.') . '/unit';
        }
        $lines[] = "Stok sedia berapa unit?";
        $lines[] = "• Link: " . route('shop.produk', $product->id);

        return self::build($number, implode("\n", $lines));
    }

    protected static function number(): string
    {
        return preg_replace('/[^0-9]/', '', (string) StoreSetting::get('whatsapp_number', '6281234567890'));
    }

    protected static function build(string $number, string $text): string
    {
        return 'https://wa.me/' . $number . '/?text=' . rawurlencode($text);
    }
}