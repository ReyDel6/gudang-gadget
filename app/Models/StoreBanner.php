<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StoreBanner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'cta_link',
        'order_position',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order_position' => 'integer',
        ];
    }

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('order_position');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path && file_exists(public_path('storage/' . $this->image_path))) {
            return asset('storage/' . $this->image_path);
        }
        return asset('storage/thumbnails/no-image.jpg');
    }
}