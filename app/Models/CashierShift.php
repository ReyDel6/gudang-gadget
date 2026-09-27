<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'start_cash',
        'end_cash',
        'actual_cash',
        'difference',
        'opened_at',
        'closed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_cash' => 'decimal:2',
            'end_cash' => 'decimal:2',
            'actual_cash' => 'decimal:2',
            'difference' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeAktif(Builder $q): Builder
    {
        return $q->whereNull('closed_at');
    }

    public function getIsOpenAttribute(): bool
    {
        return $this->closed_at === null;
    }

    public function getOmzetAttribute(): float
    {
        return round((float) Penjualan::aktif()
            ->where('cashier_shift_id', $this->id)
            ->sum('total'), 2);
    }

    public function getJumlahTransaksiAttribute(): int
    {
        return (int) Penjualan::aktif()
            ->where('cashier_shift_id', $this->id)
            ->count();
    }
}