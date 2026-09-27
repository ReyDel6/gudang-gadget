<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpnameItem extends Model
{
    protected $table = 'stock_opname_items';

    protected $fillable = [
        'stock_opname_id',
        'gadget_id',
        'system_stock',
        'physical_stock',
        'difference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'system_stock' => 'integer',
            'physical_stock' => 'integer',
            'difference' => 'integer',
        ];
    }

    public function opname()
    {
        return $this->belongsTo(StokOpname::class, 'stock_opname_id', 'id');
    }

    public function gadget()
    {
        return $this->belongsTo(Gadget::class, 'gadget_id', 'id');
    }
}