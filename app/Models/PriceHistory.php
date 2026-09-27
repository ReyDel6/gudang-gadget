<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    public const ALASAN_CREATE = 'Harga awal saat produk dibuat';

    protected $fillable = [
        'gadget_id',
        'harga_beli_lama',
        'harga_beli_baru',
        'perubah',
        'alasan',
    ];

    public static function catat(Gadget $gadget, float $lama, float $baru, string $alasan, string $perubah = ''): void
    {
        if ($lama == $baru) {
            return;
        }

        static::create([
            'gadget_id' => $gadget->id,
            'harga_beli_lama' => $lama,
            'harga_beli_baru' => $baru,
            'perubah' => $perubah ?: (auth()->user()?->name ?: 'Sistem'),
            'alasan' => $alasan,
        ]);
    }

    protected function casts(): array
    {
        return [
            'harga_beli_lama' => 'decimal:2',
            'harga_beli_baru' => 'decimal:2',
        ];
    }

    public function gadget()
    {
        return $this->belongsTo(Gadget::class, 'gadget_id', 'id');
    }
}