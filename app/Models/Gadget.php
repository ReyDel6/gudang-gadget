<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gadget extends Model
{
    use SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_produk',
        'sku',
        'kategori',
        'supplier',
        'lokasi_rak',
        'deskripsi',
        'harga_beli',
        'satuan',
        'tanggal_pembelian',
        'stock',
        'stok_minimum',
        'serial_number',
        'status',
    ];

    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'tanggal_pembelian' => 'date',
            'stock' => 'integer',
            'stok_minimum' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Gadget $gadget) {
            // Jaga konsistensi stok <-> status (satu sumber kebenaran = stok).
            if ($gadget->status !== 'Tidak Dijual') {
                $gadget->status = ($gadget->stock > 0) ? 'Tersedia' : 'Habis';
            }
            if (empty($gadget->satuan)) {
                $gadget->satuan = 'pcs';
            }
        });
    }

    public function thumbnail()
    {
        return $this->hasOne(GadgetFoto::class, 'id', 'id');
    }

    public function stokLogs()
    {
        return $this->hasMany(StokLog::class, 'gadget_id', 'id');
    }

    public function getFotoUrlAttribute()
    {
        $foto = $this->thumbnail;
        if ($foto && $foto->url && file_exists(public_path('storage/' . $foto->url))) {
            return asset('storage/' . $foto->url);
        }
        return asset('storage/thumbnails/no-image.jpg');
    }

    public function getNilaiAsetAttribute(): float
    {
        return round((float) $this->stock * (float) $this->harga_beli, 2);
    }

    public function getHabisAttribute(): bool
    {
        return (int) $this->stock <= 0 && $this->status !== 'Tidak Dijual';
    }

    public function getMenipisAttribute(): bool
    {
        return $this->stok_minimum > 0
            && $this->stock > 0
            && $this->stock <= $this->stok_minimum;
    }

    public function scopeAktif(Builder $q): Builder
    {
        return $q->whereNull('deleted_at');
    }

    public function scopeMenipis(Builder $q): Builder
    {
        return $q->whereNull('deleted_at')
            ->where('stock', '>', 0)
            ->whereNotNull('stok_minimum')
            ->whereColumn('stock', '<=', 'stok_minimum');
    }

    public function scopeHabis(Builder $q): Builder
    {
        return $q->whereNull('deleted_at')
            ->where('stock', '<=', 0)
            ->where('status', '!=', 'Tidak Dijual');
    }
}