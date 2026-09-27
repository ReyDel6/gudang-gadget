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
        'harga_jual',
        'harga_promo',
        'satuan',
        'tanggal_pembelian',
        'stock',
        'stok_minimum',
        'serial_number',
        'status',
        'is_published',
        'is_featured',
        'condition',
        'specifications',
        'warranty_info',
    ];

    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'harga_promo' => 'decimal:2',
            'tanggal_pembelian' => 'date',
            'stock' => 'integer',
            'stok_minimum' => 'integer',
            'deleted_at' => 'datetime',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'specifications' => 'array',
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

        static::created(function (Gadget $gadget) {
            AuditLog::catat(
                auth()->user(),
                'tambah produk',
                $gadget,
                ['nama_produk' => $gadget->nama_produk, 'sku' => $gadget->sku]
            );
        });

        static::updated(function (Gadget $gadget) {
            $diff = $gadget->getChanges();
            unset($diff['updated_at']);
            if ($diff) {
                AuditLog::catat(
                    auth()->user(),
                    'ubah produk',
                    $gadget,
                    $diff
                );
            }
        });

        static::deleted(function (Gadget $gadget) {
            AuditLog::catat(
                auth()->user(),
                'arsip produk',
                $gadget,
                ['nama_produk' => $gadget->nama_produk, 'sku' => $gadget->sku]
            );
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

    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class, 'gadget_id', 'id');
    }

    public function tierPrices()
    {
        return $this->hasMany(GadgetTierPrice::class, 'gadget_id', 'id');
    }

    public function tierPricesTerurut()
    {
        return $this->hasMany(GadgetTierPrice::class, 'gadget_id', 'id')
            ->orderBy('min_qty');
    }

    public function terjual()
    {
        return $this->hasMany(PenjualanItem::class, 'gadget_id', 'id');
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

    public function scopePublik(Builder $q): Builder
    {
        return $q->whereNull('deleted_at')
            ->where('is_published', true)
            ->where('status', '!=', 'Tidak Dijual');
    }

    public function scopeUnggulan(Builder $q): Builder
    {
        return $q->publik()->where('is_featured', true);
    }

    public function getTersediaAttribute(): bool
    {
        return (int) $this->stock > 0;
    }

    public function getKetersediaanAttribute(): string
    {
        return $this->tersedia ? 'Ready Stock' : 'Stok Habis';
    }

    public function getKondisiLabelAttribute(): string
    {
        return match ($this->condition) {
            'like-new' => 'Bekas Mulus',
            'used' => 'Second',
            default => 'Baru / Segel',
        };
    }

    public function getBrandAttribute(): string
    {
        return (string) preg_replace('/[^A-Za-z0-9 ]/', '', ucfirst(explode(' ', trim($this->nama_produk))[0] ?? ''));
    }

    public function getHargaPromoAktifAttribute(): bool
    {
        $promo = (float) ($this->harga_promo ?? 0);
        $jual = (float) ($this->harga_jual ?? 0);

        return $promo > 0 && $jual > 0 && $promo < $jual;
    }

    public function getHargaAktifAttribute(): float
    {
        return round($this->harga_promo_aktif ? (float) $this->harga_promo : (float) $this->harga_jual, 2);
    }

    public function getHargaMitraAttribute(): float
    {
        $tier = $this->tierPrices->sortByDesc('min_qty')->first();

        if ($tier && (float) $tier->price > 0) {
            return round((float) $tier->price, 2);
        }

        return $this->harga_aktif;
    }

    public function getDiskonPersenAttribute(): ?int
    {
        if (! $this->harga_promo_aktif) {
            return null;
        }
        $jual = (float) $this->harga_jual;
        $promo = (float) $this->harga_promo;
        $persen = (($jual - $promo) / $jual) * 100;

        return max(1, (int) round($persen));
    }

    public function getUrlWaShareAttribute(): string
    {
        $url = route('shop.produk', $this->id);
        $text = "{$this->nama_produk} — Rp " . number_format($this->harga_aktif, 0, ',', '.') . "\n{$url}";

        return 'https://wa.me/?text=' . rawurlencode($text);
    }

    public function getUrlTelegShareAttribute(): string
    {
        $url = route('shop.produk', $this->id);
        $text = "{$this->nama_produk} — Rp " . number_format($this->harga_aktif, 0, ',', '.') . "\n{$url}";

        return 'https://t.me/share/url?url=' . rawurlencode($url) . '&text=' . rawurlencode($text);
    }
}