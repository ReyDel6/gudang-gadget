<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public static function buat(string $prefix, string $tabel): string
    {
        return DB::transaction(function () use ($prefix, $tabel) {
            $bas = $prefix . '-' . date('Ymd');
            do {
                $jumlah = DB::table($tabel)->lockForUpdate()
                    ->where('no_invoice', 'like', "{$bas}%")->count();
                $no = $bas . '-' . str_pad((string) ($jumlah + 1), 3, '0', STR_PAD_LEFT);
            } while (DB::table($tabel)->where('no_invoice', $no)->exists());

            return $no;
        });
    }
}