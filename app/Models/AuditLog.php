<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'aksi',
        'model_type',
        'model_id',
        'perubahan',
        'ip',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'perubahan' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function catat(?User $user, string $aksi, ?Model $model = null, array $perubahan = [], ?string $ip = null): void
    {
        static::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?: 'Sistem',
            'aksi' => $aksi,
            'model_type' => $model ? $model->getMorphClass() : null,
            'model_id' => $model?->getKey(),
            'perubahan' => $perubahan,
            'ip' => $ip,
            'created_at' => now(),
        ]);
    }
}