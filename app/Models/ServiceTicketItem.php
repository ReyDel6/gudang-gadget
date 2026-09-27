<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTicketItem extends Model
{
    public const TIPE_JASA = 'service_fee';
    public const TIPE_SPAREPART = 'sparepart';

    protected $fillable = [
        'service_ticket_id', 'gadget_id', 'item_type', 'item_name',
        'cost_price', 'sell_price', 'quantity', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'sell_price' => 'decimal:2',
            'quantity' => 'integer',
            'subtotal' => 'decimal:2',
        ];
    }

    public function ticket()
    {
        return $this->belongsTo(ServiceTicket::class, 'service_ticket_id', 'id');
    }

    public function gadget()
    {
        return $this->belongsTo(Gadget::class, 'gadget_id', 'id');
    }

    public function getTipeLabelAttribute(): string
    {
        return $this->item_type === self::TIPE_SPAREPART ? 'Suku Cadang' : 'Jasa';
    }
}