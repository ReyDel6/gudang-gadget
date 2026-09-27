<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    public const ST_IN_PROGRESS = 'in_progress';
    public const ST_COMPLETED = 'completed';
    public const ST_CANCELLED = 'cancelled';

    public const STATUS = [
        self::ST_IN_PROGRESS => 'Berjalan',
        self::ST_COMPLETED => 'Selesai',
        self::ST_CANCELLED => 'Dibatalkan',
    ];

    protected $table = 'stock_opnames';

    protected $fillable = [
        'no_invoice',
        'auditor_id',
        'category_filter',
        'status',
        'total_system_items',
        'total_physical_items',
        'total_difference',
        'notes',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(StokOpnameItem::class, 'stock_opname_id', 'id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? ucfirst($this->status);
    }

    public function scopeStatus(Builder $q, ?string $status): Builder
    {
        if ($status) {
            $q->where('status', $status);
        }

        return $q;
    }
}