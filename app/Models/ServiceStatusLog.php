<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceStatusLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'service_ticket_id', 'user_id', 'status', 'notes', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function ticket()
    {
        return $this->belongsTo(ServiceTicket::class, 'service_ticket_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getStatusLabelAttribute(): string
    {
        return ServiceTicket::STATUS[$this->status] ?? match ($this->status) {
            'item' => 'Pemakaian Suku Cadang',
            'jasa' => 'Penambahan Jasa',
            'qc' => 'Quality Check',
            'lunas' => 'Pelunasan',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}