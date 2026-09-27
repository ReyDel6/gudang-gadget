<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ResellerProfile extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_SUSPENDED = 'suspended';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Menunggu',
        self::STATUS_APPROVED => 'Aktif',
        self::STATUS_REJECTED => 'Ditolak',
        self::STATUS_SUSPENDED => 'Ditangguhkan',
    ];

    protected $fillable = [
        'user_id',
        'store_name',
        'owner_name',
        'phone',
        'whatsapp',
        'address',
        'ktp_path',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getKtpUrlAttribute(): ?string
    {
        if ($this->ktp_path && file_exists(public_path('storage/' . $this->ktp_path))) {
            return asset('storage/' . $this->ktp_path);
        }

        return null;
    }

    public function scopeStatus(Builder $q, ?string $status): Builder
    {
        if ($status) {
            $q->where('status', $status);
        }

        return $q;
    }
}