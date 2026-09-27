<?php

namespace App\Models;

use App\Support\Pembayaran;
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
        'customer_phone',
        'customer_type',
        'is_dropship',
        'sender_name',
        'sender_phone',
        'recipient_name',
        'recipient_address',
        'keterangan',
        'total',
        'diskon',
        'pajak',
        'payment_method',
        'paid_amount',
        'change_amount',
        'payment_ref',
        'payment_status',
        'cashier_shift_id',
        'voided_at',
        'voided_by',
        'void_reason',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'total' => 'decimal:2',
            'diskon' => 'decimal:2',
            'pajak' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'voided_at' => 'datetime',
            'is_dropship' => 'boolean',
        ];
    }

    public function items()
    {
        return $this->hasMany(PenjualanItem::class, 'penjualan_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(CashierShift::class, 'cashier_shift_id', 'id');
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

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('payment_status', '!=', 'void');
    }

    public function getCustomerTypeLabelAttribute(): string
    {
        return $this->customer_type === 'reseller' ? 'Mitra Reseller / Toko Grosir' : 'Pelanggan Umum (Retail)';
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

    public function getPaymentLabelAttribute(): string
    {
        return Pembayaran::labelMetode($this->payment_method);
    }

    public function getStatusLabelAttribute(): string
    {
        return Pembayaran::labelStatus($this->payment_status);
    }

    public function getDibayarAttribute(): float
    {
        return $this->paid_amount !== null ? (float) $this->paid_amount : (float) $this->total;
    }

    public function getKembalianAttribute(): float
    {
        return $this->change_amount !== null ? (float) $this->change_amount : 0.0;
    }

    public function getIsVoidAttribute(): bool
    {
        return $this->payment_status === 'void';
    }
}