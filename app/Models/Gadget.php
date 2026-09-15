<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Gadget extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_produk',
        'kategori',
        'deskripsi',
        'tanggal_pembelian',
        'stock',
        'status',
    ];

    public $timestamps = false;

    public function thumbnail() {
        return $this->hasOne(GadgetFoto::class, 'id', 'id');
    }

    public function stokLogs()
    {
        return $this->hasMany(StokLog::class, 'gadget_id', 'id');
    }

    public function getFotoUrlAttribute() {
        $foto = $this->thumbnail;
        if ($foto && $foto->url && file_exists(public_path('storage/' . $foto->url))) {
            return asset('storage/' . $foto->url);
        }
        return asset('storage/thumbnails/no-image.jpg');
    }
}
