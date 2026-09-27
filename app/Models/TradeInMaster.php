<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TradeInMaster extends Model
{
    protected $table = 'trade_in_masters';

    protected $fillable = [
        'brand',
        'model_name',
        'capacity',
        'base_price_grade_a',
        'price_grade_b',
        'price_grade_c',
        'price_grade_d',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_price_grade_a' => 'decimal:2',
            'price_grade_b' => 'decimal:2',
            'price_grade_c' => 'decimal:2',
            'price_grade_d' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function nilaiUntuk(string $grade): float
    {
        return (float) match ($grade) {
            'A' => $this->base_price_grade_a,
            'B' => $this->price_grade_b,
            'C' => $this->price_grade_c,
            'D' => $this->price_grade_d,
            default => 0,
        };
    }
}