<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanItem extends Model
{
    protected $fillable = [
        'penjualan_id',
        'gadget_id',
        'nama_produk',
        'harga_beli',
        'harga_jual',
        'qty',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'qty' => 'integer',
            'subtotal' => 'decimal:2',
        ];
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id', 'id');
    }

    public function gadget()
    {
        return $this->belongsTo(Gadget::class, 'gadget_id', 'id');
    }
}