<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokLog extends Model
{
    protected $fillable = [
        'gadget_id',
        'perubahan',
        'stok_sebelum',
        'stok_sesudah',
        'keterangan',
        'user_id',
    ];

    public function gadget()
    {
        return $this->belongsTo(Gadget::class, 'gadget_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}