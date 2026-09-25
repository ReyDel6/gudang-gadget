<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StokLog extends Model
{
    public const TIPE_STOK_AWAL = 'Stok awal';
    public const TIPE_PENERIMAAN = 'Penerimaan';
    public const TIPE_PENGELUARAN = 'Pengeluaran';
    public const TIPE_RETUR = 'Retur';
    public const TIPE_PENYESUAIAN = 'Penyesuaian';
    public const TIPE_TRANSFER = 'Transfer';

    protected $fillable = [
        'gadget_id',
        'perubahan',
        'stok_sebelum',
        'stok_sesudah',
        'keterangan',
        'tipe',
        'alasan',
        'user_id',
        'user_name',
    ];

    protected function casts(): array
    {
        return [
            'perubahan' => 'integer',
            'stok_sebelum' => 'integer',
            'stok_sesudah' => 'integer',
        ];
    }

    public function gadget()
    {
        return $this->belongsTo(Gadget::class, 'gadget_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getPelakuAttribute(): string
    {
        return $this->user_name ?: ($this->user?->name ?: '-');
    }

    public function scopePeriode(Builder $q, ?string $dari, ?string $sampai): Builder
    {
        if ($dari) {
            $q->whereDate('created_at', '>=', $dari);
        }
        if ($sampai) {
            $q->whereDate('created_at', '<=', $sampai);
        }

        return $q;
    }
}