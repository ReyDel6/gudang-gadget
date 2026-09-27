<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $fillable = [
        'no_invoice',
        'tanggal',
        'user_id',
        'user_name',
        'supplier',
        'keterangan',
        'total',
        'diskon',
        'pajak',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'total' => 'decimal:2',
            'diskon' => 'decimal:2',
            'pajak' => 'decimal:2',
        ];
    }

    public function items()
    {
        return $this->hasMany(PembelianItem::class, 'pembelian_id', 'id');
    }

    public function scopePeriode(Builder $q, ?string $dari, ?string $sampai): Builder
    {
        if ($dari) {
            $q->whereDate('tanggal', '>=', $dari);
        }
        if ($sampai) {
            $q->whereDate('tanggal', '<=', $sampai);
        }

        return $q;
    }

    public function getSubtotalAttribute(): float
    {
        return round((float) $this->items->sum('subtotal'), 2);
    }

    public function getPajakNominalAttribute(): float
    {
        return round(((float) $this->subtotal - (float) $this->diskon) * ((float) $this->pajak / 100), 2);
    }
}