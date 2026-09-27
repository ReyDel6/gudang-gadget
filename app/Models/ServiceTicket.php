<?php

namespace App\Models;

use App\Support\Pembayaran;
use Illuminate\Database\Eloquent\Model;

class ServiceTicket extends Model
{
    public const STATUS_BARU = 'pending';
    public const STATUS_DIAGNOSA = 'diagnosing';
    public const STATUS_TUNGGU_PART = 'waiting_part';
    public const STATUS_DIKERJAKAN = 'working';
    public const STATUS_SIAP = 'ready';
    public const STATUS_DIAMBIL = 'delivered';
    public const STATUS_BATAL = 'cancelled';

    public const STATUS = [
        self::STATUS_BARU => 'Baru Diterima',
        self::STATUS_DIAGNOSA => 'Pengecekan / Diagnosis',
        self::STATUS_TUNGGU_PART => 'Menunggu Suku Cadang',
        self::STATUS_DIKERJAKAN => 'Sedang Dikerjakan',
        self::STATUS_SIAP => 'Selesai / Siap Diambil',
        self::STATUS_DIAMBIL => 'Diambil / Lunas',
        self::STATUS_BATAL => 'Dibatalkan',
    ];

    public const WARNA = [
        self::STATUS_BARU => 'bg-navy-50 text-navy-700',
        self::STATUS_DIAGNOSA => 'bg-blue-50 text-blue-700',
        self::STATUS_TUNGGU_PART => 'bg-amber-50 text-amber-700',
        self::STATUS_DIKERJAKAN => 'bg-indigo-50 text-indigo-700',
        self::STATUS_SIAP => 'bg-emerald-50 text-emerald-700',
        self::STATUS_DIAMBIL => 'bg-green-100 text-green-800',
        self::STATUS_BATAL => 'bg-rose-50 text-rose-700',
    ];

    public const QC_ITEMS = [
        'layar_sentuh' => 'Layar Sentuh',
        'speaker' => 'Speaker',
        'kamera_depan' => 'Kamera Depan',
        'kamera_belakang' => 'Kamera Belakang',
        'sensor_proximity' => 'Sensor Proximity',
        'charging' => 'Charging / Port',
        'jaringan' => 'Sinyal / WiFi',
    ];

    public const METODE_PELUNASAN = [Pembayaran::CASH, Pembayaran::QRIS, Pembayaran::TRANSFER, Pembayaran::DEBIT];

    protected $fillable = [
        'no_tiket', 'parent_id',
        'customer_name', 'customer_phone', 'customer_address',
        'device_brand', 'device_model', 'imei_or_serial', 'passcode',
        'completeness', 'initial_condition', 'problem_description',
        'technician_notes', 'technician_id', 'cashier_id',
        'status',
        'down_payment', 'service_fee', 'sparepart_fee', 'total_cost', 'remaining_cost',
        'warranty_days', 'warranty_until', 'qc_checklist',
        'payment_method', 'paid_amount', 'payment_ref', 'paid_at',
        'received_at', 'completed_at', 'delivered_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'down_payment' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'sparepart_fee' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'remaining_cost' => 'decimal:2',
            'warranty_days' => 'integer',
            'warranty_until' => 'date',
            'qc_checklist' => 'array',
            'paid_amount' => 'decimal:2',
            'received_at' => 'datetime',
            'completed_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function items()
    {
        return $this->hasMany(ServiceTicketItem::class, 'service_ticket_id', 'id')->orderBy('id');
    }

    public function sparepartItems()
    {
        return $this->items()->where('item_type', 'sparepart');
    }

    public function jasaItems()
    {
        return $this->items()->where('item_type', 'service_fee');
    }

    public function logs()
    {
        return $this->hasMany(ServiceStatusLog::class, 'service_ticket_id', 'id')->latest('id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id', 'id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(ServiceTicket::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(ServiceTicket::class, 'parent_id', 'id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusWarnaAttribute(): string
    {
        return self::WARNA[$this->status] ?? 'bg-navy-50 text-navy-700';
    }

    public function getIsGaransiAttribute(): bool
    {
        return $this->parent_id !== null;
    }

    public function isSelesai(): bool
    {
        return in_array($this->status, [self::STATUS_DIAMBIL, self::STATUS_BATAL]);
    }

    public function getWarnaBadgeAttribute(): string
    {
        return $this->status_warna;
    }
}