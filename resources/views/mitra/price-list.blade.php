@extends('layouts.store')

@section('title', 'Price List Mitra')

@section('content')

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Portal Mitra</p>
                <h1 class="text-2xl font-black text-navy-900 mt-1">Price List Harian</h1>
                <p class="text-sm text-navy-500 mt-1">
                    {{ $products->count() }} produk ready stock · berlaku {{ now()->format('d M Y') }}.
                    Harga mitra = tarif terendah menyesuaikan jumlah pembelian.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('shop.mitra.beranda') }}"
                   class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    ← Beranda
                </a>
                <a href="{{ route('shop.mitra.price-list.cetak') }}" target="_blank"
                   class="border border-navy-100 bg-white hover:border-gold-500 text-navy-700 text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
                    🖨 Cetak / Simpan PDF
                </a>
                <a href="{{ route('shop.mitra.price-list.csv') }}"
                   class="bg-navy-900 hover:bg-navy-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
                    ⬇ Unduh CSV
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead>
                        <tr class="bg-navy-50 text-navy-500 text-left">
                            <th class="px-4 py-3 font-medium">SKU</th>
                            <th class="px-4 py-3 font-medium">Produk</th>
                            <th class="px-4 py-3 font-medium text-right">Harga Retail</th>
                            <th class="px-4 py-3 font-medium text-right">Harga Mitra</th>
                            <th class="px-4 py-3 font-medium text-center">Stok</th>
                            <th class="px-4 py-3 font-medium">Keterangan Tier</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-50">
                        @foreach ($products as $p)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs text-navy-500">{{ $p->sku }}</td>
                                <td class="px-4 py-3 font-semibold text-navy-800">
                                    {{ $p->nama_produk }}
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($products->isEmpty())
                <div class="p-12 text-center text-navy-400">Belum ada produk ready stock.</div>
            @endif
        </div>

        <p class="text-[11px] text-navy-400 mt-4">
            * Harga mitra adalah tarif partai/terendah. Harga dapat berubah sewaktu-waktu mengikuti ketersediaan stok.
            Untuk pesanan partai besar atau dropship, silakan hubungi admin via WhatsApp.
        </p>
    </div>

@endsection