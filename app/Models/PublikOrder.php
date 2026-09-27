<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PublikOrder extends Model
{
    protected $table = 'publik_orders';

    protected $fillable = [
        'kode', 'reseller_id', 'nama_pelanggan', 'telepon', 'alamat', 'catatan',
        'payment_method', 'subtotal', 'ongkos_kirim', 'kurir', 'total',
        'status', 'bukti_path', 'penjualan_id', 'catatan_admin',
        'confirmed_at', 'cancelled_at',
    ];

    protected $casts = [
        'ongkos_kirim' => 'float',
        'subtotal' => 'float',
        'total' => 'float',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    public const METODE_PEMBAYARAN = [
        'transfer' => 'Transfer Bank',
        'cod' => 'COD (Bayar di Tempat)',
        'store' => 'Bayar di Toko',
    ];

    public const STATUS_LABEL = [
        self::STATUS_PENDING => 'Menunggu Konfirmasi',
        self::STATUS_CONFIRMED => 'Terkonfirmasi',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PublikOrderItem::class, 'order_id');
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reseller_id');
    }

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABEL[$this->status] ?? $this->status;
    }

    public function getPaymentLabelAttribute(): string
    {
        return self::METODE_PEMBAYARAN[$this->payment_method] ?? $this->payment_method;
    }

    public function getTotalAkhirAttribute(): float
    {
        return round($this->subtotal + (float) $this->ongkos_kirim, 2);
    }

    public function scopeHanyaMenunggu($q)
    {
        return $q->where('status', self::STATUS_PENDING);
    }
}