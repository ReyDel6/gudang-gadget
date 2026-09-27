<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianItem extends Model
{
    protected $fillable = [
        'pembelian_id',
        'gadget_id',
        'nama_produk',
        'harga_beli',
        'qty',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'qty' => 'integer',
            'subtotal' => 'decimal:2',
        ];
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id', 'id');
    }

    public function gadget()
    {
        return $this->belongsTo(Gadget::class, 'gadget_id', 'id');
    }
}