@extends('layouts.store')

@section('title', 'Price List Mitra')

@section('meta_desc', 'Price list mitra terbaru ' . $settings['store_name'] . ' dengan tarif khusus reseller.')

@section('content')

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Portal Mitra</p>
                <h1 class="text-2xl font-black text-navy-900 mt-1">Price List Harian</h1>
                <p class="text-sm text-navy-500 mt-1">
                    {{ $products->count() }} produk ready stock · berlaku {{ now()->format('d M Y') }}.
                    Diskon tier (grosir/partai) berlaku otomatis di keranjang sesuai jumlah yang dibeli.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('shop.mitra.beranda') }}"
                   class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    ← Beranda
                </a>
                <a href="{{ route('shop.keranjang') }}"
                   class="bg-navy-900 hover:bg-navy-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
                    🛒 Keranjang ({{ $totalKeranjang }})
                </a>
                <a href="{{ route('shop.mitra.price-list.cetak') }}" target="_blank"
                   class="border border-navy-100 bg-white hover:border-gold-500 text-navy-700 text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
                    🖨 Cetak / Simpan PDF
                </a>
                <a href="{{ route('shop.mitra.price-list.csv') }}"
                   class="border border-navy-100 bg-white hover:border-gold-500 text-navy-700 text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
                    ⬇ Unduh CSV
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('sukses_keranjang'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
                ✓ {{ session('sukses_keranjang') }}
                <a href="{{ route('shop.keranjang') }}" class="underline font-bold">Lihat keranjang →</a>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] text-sm">
                    <thead>
                        <tr class="bg-navy-50 text-navy-500 text-left">
                            <th class="px-4 py-3 font-medium">SKU</th>
                            <th class="px-4 py-3 font-medium">Produk</th>
                            <th class="px-4 py-3 font-medium text-right">Harga Retail</th>
                            <th class="px-4 py-3 font-medium text-right">Harga Mitra/Partai*</th>
                            <th class="px-4 py-3 font-medium text-center">Stok</th>
                            <th class="px-4 py-3 font-medium">Keterangan Tier</th>
                            <th class="px-4 py-3 font-medium text-center">Beli</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-50">
                        @foreach ($products as $p)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs text-navy-500">{{ $p->sku }}</td>
                                <td class="px-4 py-3 font-semibold text-navy-800">
                                    <a href="{{ route('shop.produk', $p->id) }}" class="hover:text-gold-600 transition-colors">
                                        {{ $p->nama_produk }}
                                    </a>
                                    <span class="block text-xs font-normal text-navy-400 mt-0.5">{{ $p->kategori }}</span>
                                </td>
                                <td class="px-4 py-3 text-right text-navy-600">
                                    @if ($p->harga_promo_aktif)
                                        <span class="line-through text-navy-400 block text-xs">Rp {{ number_format((float) $p->harga_jual, 0, ',', '.') }}</span>
                                        <span class="text-rose-600 font-bold">Rp {{ number_format($p->harga_aktif, 0, ',', '.') }}</span>
                                    @else
                                        Rp {{ number_format($p->harga_aktif, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-black text-gold-600">Rp {{ number_format($p->harga_mitra, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block text-xs font-bold rounded-full px-2.5 py-1 {{ (int) $p->stock > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $p->stock }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-navy-500">
                                    @if ($p->tierPrices->isNotEmpty())
                                        @foreach ($p->tierPrices->sortBy('min_qty') as $i => $t)
                                            <span>{{ $t->tier_name }}: <b class="text-navy-700">Rp {{ number_format((float) $t->price, 0, ',', '.') }}</b>/unit (min {{ $t->min_qty }})</span>{{ !$loop->last ? ' · ' : '' }}
                                        @endforeach
                                    @else
                                        <span class="text-navy-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ((int) $p->stock > 0)
                                        <form method="POST" action="{{ route('shop.keranjang.tambah') }}" class="inline-flex items-center gap-1.5">
                                            @csrf
                                            <input type="hidden" name="gadget_id" value="{{ $p->id }}">
                                            <input type="number" name="qty" value="1" min="1" max="{{ min(99, (int) $p->stock) }}"
                                                   class="w-16 rounded-lg border border-navy-200 px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-gold-500"
                                                   aria-label="Jumlah {{ $p->nama_produk }}">
                                            <button type="submit"
                                                    class="bg-gold-500 hover:bg-gold-600 text-navy-900 text-xs font-black px-3 py-1.5 rounded-lg transition-colors">
                                                + Keranjang
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs font-bold text-rose-500">Habis</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($products->isEmpty())
                <div class="p-12 text-center text-navy-400">Belum ada produk ready stock.</div>
            @endif
        </div>

        <div class="mt-4 rounded-2xl border border-navy-100 bg-navy-50/50 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <p class="text-xs text-navy-500">
                * Harga Mitra/Partai = tarif terendah (biasanya tier partai) sebagai acuan. Harga yang ditagih mengikuti
                jumlah pembelian: harga retail untuk pembelian kecil, lalu otomatis turun ke tier grosir/partai saat
                minimal jumlah terpenuhi. Setelah checkout, admin mengonfirmasi ketersediaan & ongkir sebelum barang dikirim.
            </p>
            <a href="{{ route('shop.keranjang') }}"
               class="shrink-0 bg-navy-900 hover:bg-navy-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition-colors text-center">
                Lanjut ke Keranjang & Checkout →
            </a>
        </div>
    </div>

@endsection