<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $fillable = [
        'no_invoice',
        'tanggal',
        'user_id',
        'user_name',
        'customer',
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
        return $this->hasMany(PenjualanItem::class, 'penjualan_id', 'id');
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

    public function getLabaAttribute(): float
    {
        $margin = (float) $this->items->sum(fn ($i) => ((float) $i->harga_jual - (float) $i->harga_beli) * $i->qty);

        return round($margin - (float) $this->diskon, 2);
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