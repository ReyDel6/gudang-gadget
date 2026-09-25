<?php

namespace App\Services;

use App\Models\Gadget;
use App\Models\StokLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StokService
{
    /**
     * Mengubah stok secara atomik (transaction + row lock) dan mencatat log.
     * Melempar ValidationException bila stok menjadi negatif.
     */
    public static function adjust(Gadget $gadget, string $tipe, int $delta, ?string $alasan = null): Gadget
    {
        return DB::transaction(function () use ($gadget, $tipe, $delta, $alasan) {
            $locked = Gadget::query()->whereKey($gadget->id)->lockForUpdate()->firstOrFail();

            $sebelum = (int) $locked->stock;
            $sesudah = $sebelum + $delta;

            if ($sesudah < 0) {
                throw ValidationException::withMessages([
                    'stok' => "Stok \"{$locked->nama_produk}\" tidak mencukupi (tersisa {$sebelum} {$locked->satuan}).",
                ]);
            }

            // Transfer dan mutasi delta 0 tetap dicatat sebagai log.
            if ($sesudah !== $sebelum || in_array($tipe, [
                StokLog::TIPE_TRANSFER,
                StokLog::TIPE_STOK_AWAL,
                StokLog::TIPE_PENYESUAIAN,
            ], true)) {
                $locked->stock = $sesudah;
                $locked->save(); // status menyesuaikan lewat model event

                static::log($locked, $delta, $sebelum, $sesudah, $tipe, $alasan);
            }

            return $locked;
        });
    }

    public static function log(Gadget $gadget, int $delta, int $sebelum, int $sesudah, string $tipe, ?string $alasan = null, ?\App\Models\User $user = null): StokLog
    {
        $user ??= Auth::user();

        return StokLog::create([
            'gadget_id' => $gadget->id,
            'perubahan' => $delta,
            'stok_sebelum' => $sebelum,
            'stok_sesudah' => $sesudah,
            'tipe' => $tipe,
            'alasan' => $alasan,
            'keterangan' => $alasan ? $tipe . ' — ' . $alasan : $tipe,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
        ]);
    }
}