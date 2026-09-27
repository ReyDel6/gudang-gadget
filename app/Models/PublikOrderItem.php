<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublikOrderItem extends Model
{
    protected $table = 'publik_order_items';

    protected $fillable = ['order_id', 'gadget_id', 'nama_produk', 'harga_satuan', 'qty', 'subtotal'];

    protected $casts = [
        'harga_satuan' => 'float',
        'subtotal' => 'float',
        'qty' => 'int',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(PublikOrder::class, 'order_id');
    }

    public function gadget(): BelongsTo
    {
        return $this->belongsTo(Gadget::class, 'gadget_id');
    }
}