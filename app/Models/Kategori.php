<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'categories';

    protected $fillable = ['nama', 'deskripsi'];

    public function produk()
    {
        return $this->hasMany(Gadget::class, 'kategori', 'nama');
    }

    public function scopeSearch(Builder $q, ?string $kata): Builder
    {
        if ($kata) {
            $q->where('nama', 'like', "%{$kata}%")
                ->orWhere('deskripsi', 'like', "%{$kata}%");
        }

        return $q;
    }
}