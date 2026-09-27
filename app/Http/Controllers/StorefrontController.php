<?php

namespace App\Http\Controllers;

use App\Models\Gadget;
use App\Models\StoreBanner;
use App\Models\StoreSetting;
use App\Support\WhatsApp;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class StorefrontController extends Controller
{
    public static array $publicColumns = [
        'id', 'nama_produk', 'sku', 'kategori', 'deskripsi', 'harga_jual',
        'harga_promo', 'stock', 'satuan', 'status', 'is_published', 'is_featured',
        'condition', 'specifications', 'warranty_info', 'created_at', 'updated_at',
    ];

    public function index()
    {
        $banners = StoreBanner::aktif()->get();
        $featured = Gadget::unggulan()->with(['thumbnail', 'galeri', 'tierPrices'])->limit(8)->get(self::$publicColumns);
        $categories = $this->categories();
        $settings = $this->settings();
        $resellerMode = $this->resellerMode();

        return view('store.index', compact('banners', 'featured', 'categories', 'settings', 'resellerMode'));
    }

    public function katalog(Request $request)
    {
        $query = Gadget::publik()->with(['thumbnail', 'galeri', 'tierPrices'])->select(self::$publicColumns);

        if ($keyword = trim((string) $request->input('q'))) {
            $query->where(fn ($q) => $q->where('nama_produk', 'like', "%{$keyword}%")
                ->orWhere('sku', 'like', "%{$keyword}%")
                ->orWhere('deskripsi', 'like', "%{$keyword}%"));
        }
        if ($kategori = $request->input('kategori')) {
            $query->where('kategori', $kategori);
        }
        if ($brand = $request->input('brand')) {
            $query->where('nama_produk', 'like', $brand . '%');
        }
        if ($min = $request->input('min')) {
            $query->whereRaw('COALESCE(NULLIF(harga_promo, 0), harga_jual) >= ?', [(float) $min]);
        }
        if ($max = $request->input('max')) {
            $query->whereRaw('COALESCE(NULLIF(harga_promo, 0), harga_jual) <= ?', [(float) $max]);
        }
        if ($request->boolean('ready')) {
            $query->where('stock', '>', 0);
        }
        if ($request->boolean('grosir')) {
            $query->whereHas('tierPrices', fn ($q) => $q->where('price', '>', 0));
        }
        if ($kondisi = $request->input('condition')) {
            $query->where('condition', $kondisi);
        }

        switch ($request->input('sort')) {
            case 'termurah':
                $query->orderByRaw('COALESCE(NULLIF(harga_promo, 0), harga_jual) ASC');
                break;
            case 'termahal':
                $query->orderByRaw('COALESCE(NULLIF(harga_promo, 0), harga_jual) DESC');
                break;
            case 'populer':
                $query->withCount('terjual')->orderByDesc('terjual_count');
                break;
            case 'terbaru':
                $query->orderByDesc('id');
                break;
            default:
                $query->orderByDesc('is_featured')->orderBy('nama_produk');
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = $this->categories();
        $brands = $this->brands();
        $settings = $this->settings();
        $resellerMode = $this->resellerMode();

        return view('store.katalog', compact('products', 'categories', 'brands', 'settings', 'resellerMode'));
    }

    public function produk($id)
    {
        $product = Gadget::publik()->with(['thumbnail', 'galeri', 'tierPrices'])
            ->select(self::$publicColumns)
            ->findOrFail($id);
        $settings = $this->settings();
        $resellerMode = $this->resellerMode();

        $related = Gadget::publik()->with(['thumbnail', 'galeri', 'tierPrices'])
            ->where('kategori', $product->kategori)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get(self::$publicColumns);

        return view('store.produk', compact('product', 'related', 'settings', 'resellerMode'));
    }

    public function waLink(Gadget $product): string
    {
        return WhatsApp::link($product);
    }

    protected function categories(): Collection
    {
        return Gadget::publik()->whereNotNull('kategori')->distinct()->orderBy('kategori')->pluck('kategori');
    }

    protected function brands(): Collection
    {
        return Gadget::publik()->get(['nama_produk'])->map->brand->unique()->sort()->values();
    }

    public function settings(): array
    {
        return [
            'store_name' => StoreSetting::get('store_name', 'Gudang Gadget'),
            'whatsapp_number' => StoreSetting::get('whatsapp_number', '6281234567890'),
            'store_address' => StoreSetting::get('store_address', ''),
            'jam_operasional' => StoreSetting::get('jam_operasional', ''),
            'maps_embed' => StoreSetting::get('maps_embed', ''),
            'store_photo' => StoreSetting::get('store_photo', ''),
            'tags_line' => StoreSetting::get('tags_line', ''),
            'instagram_url' => StoreSetting::get('instagram_url', '#'),
            'facebook_url' => StoreSetting::get('facebook_url', '#'),
            'tiktok_url' => StoreSetting::get('tiktok_url', '#'),
            'payment_transfer_enabled' => StoreSetting::get('payment_transfer_enabled', '1'),
            'payment_transfer_info' => StoreSetting::get('payment_transfer_info', ''),
            'payment_cod_enabled' => StoreSetting::get('payment_cod_enabled', '1'),
            'payment_store_enabled' => StoreSetting::get('payment_store_enabled', '1'),
        ];
    }

    protected function resellerMode(): bool
    {
        $user = Auth::user();

        return $user && $user->isReseller() && $user->reseller?->isApproved();
    }
}