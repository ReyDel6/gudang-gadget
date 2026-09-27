<?php

namespace App\Services;

use App\Http\Controllers\StorefrontController;
use App\Models\Gadget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class KeranjangService
{
    public const SESSION_KEY = 'keranjang_publik';

    public static function ambil(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public static function tambah(int $id, int $qty = 1): void
    {
        $keranjang = self::ambil();
        $keranjang[$id] = ((int) ($keranjang[$id] ?? 0)) + max(1, $qty);
        Session::put(self::SESSION_KEY, $keranjang);
    }

    public static function set(int $id, int $qty): void
    {
        $keranjang = self::ambil();
        if ($qty < 1) {
            unset($keranjang[$id]);
        } else {
            $keranjang[$id] = $qty;
        }
        Session::put(self::SESSION_KEY, $keranjang);
    }

    public static function hapus(int $id): void
    {
        $keranjang = self::ambil();
        unset($keranjang[$id]);
        Session::put(self::SESSION_KEY, $keranjang);
    }

    public static function hapusSemua(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public static function jumlahItem(): int
    {
        return (int) array_sum(self::ambil());
    }

    public static function kosong(): bool
    {
        return self::jumlahItem() < 1;
    }

    /**
     * Baris keranjang lengkap dengan harga (mitra/tier/retail) dan subtotal.
     *
     * @return Collection<array{
     *     gadget: Gadget,
     *     qty: int,
     *     harga_satuan: float,
     *     subtotal: float,
     *     stok_cukup: bool,
     * }>
     */
    public static function baris(): Collection
    {
        $keranjang = self::ambil();
        if (empty($keranjang)) {
            return collect();
        }

        $reseller = Auth::user() && Auth::user()->isReseller() && Auth::user()->reseller?->isApproved();

        $gadgets = Gadget::publik()
            ->with(['thumbnail', 'tierPrices' => fn ($q) => $q->orderBy('min_qty')])
            ->whereIn('id', array_keys($keranjang))
            ->get(StorefrontController::$publicColumns);

        return $gadgets->map(function (Gadget $g) use ($keranjang, $reseller) {
            $qty = (int) ($keranjang[$g->id] ?? 0);
            if ($qty < 1) {
                return null;
            }

            if ($reseller) {
                $harga = $g->harga_mitra;
            } else {
                $harga = (float) ($g->harga_promo_aktif ? $g->harga_promo : $g->harga_jual);
                if ($harga > 0) {
                    foreach ($g->tierPrices as $tier) {
                        if ($qty >= (int) $tier->min_qty && (float) $tier->price > 0) {
                            $harga = round((float) $tier->price, 2);
                            break;
                        }
                    }
                }
            }

            return [
                'gadget' => $g,
                'qty' => $qty,
                'harga_satuan' => round((float) $harga, 2),
                'subtotal' => round((float) $harga * $qty, 2),
                'stok_cukup' => $qty <= (int) $g->stock,
            ];
        })->filter()->values();
    }

    public static function subtotal(): float
    {
        return round(self::baris()->sum('subtotal'), 2);
    }

    public static function sedangReseller(): bool
    {
        return Auth::user() && Auth::user()->isReseller() && Auth::user()->reseller?->isApproved();
    }
}