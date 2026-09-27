<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GadgetImei extends Model
{
    public const STA_AVAILABLE = 'available';
    public const STA_SOLD = 'sold';
    public const STA_IN_SERVICE = 'in_service';
    public const STA_DEFECTIVE = 'defective';
    public const STA_RETURNED = 'returned';

    public const STATUS = [
        self::STA_AVAILABLE => 'Tersedia di Gudang',
        self::STA_SOLD => 'Terjual',
        self::STA_IN_SERVICE => 'Dalam Servis',
        self::STA_DEFECTIVE => 'Cacat / Retur Supplier',
        self::STA_RETURNED => 'Dikembalikan',
    ];

    protected $table = 'product_imeis';

    protected $fillable = [
        'gadget_id',
        'imei',
        'serial_number',
        'color',
        'status',
        'masuk_via',
        'masuk_at',
        'penjualan_item_id',
        'sold_at',
        'warranty_expired_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'masuk_at' => 'datetime',
            'sold_at' => 'datetime',
            'warranty_expired_at' => 'date',
        ];
    }

    public function gadget()
    {
        return $this->belongsTo(Gadget::class, 'gadget_id', 'id');
    }

    public function penjualanItem()
    {
        return $this->belongsTo(PenjualanItem::class, 'penjualan_item_id', 'id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? ucfirst($this->status);
    }

    public function getSisaGaransiAttribute(): ?int
    {
        return $this->warranty_expired_at?->diffInDays(now()->startOfDay());
    }

    public function scopeStatus(Builder $q, ?string $status): Builder
    {
        if ($status) {
            $q->where('status', $status);
        }

        return $q;
    }

    public function scopeAktif(Builder $q): Builder
    {
        return $q->with('gadget');
    }
}