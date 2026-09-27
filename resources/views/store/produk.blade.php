@extends('layouts.store')

@section('title', $product->nama_produk . ' — ' . $product->kondisi_label)

@section('meta_desc', 'Harga ' . $product->nama_produk . ' Rp ' . number_format((float) $product->harga_jual, 0, ',', '.') . ' · ' . $product->ketersediaan . ' di ' . $settings['store_name'] . '.')

@push('head_meta')
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $product->nama_produk }} — {{ $settings['store_name'] }}">
    <meta property="og:description" content="Harga Rp {{ number_format((float) $product->harga_jual, 0, ',', '.') }} · {{ $product->ketersediaan }}">
    <meta property="og:url" content="{{ route('shop.produk', $product->id) }}">
    <meta property="og:image" content="{{ $product->foto_url }}">
    <meta property="og:site_name" content="{{ $settings['store_name'] }}">
    <meta name="twitter:card" content="summary_large_image">
@endpush

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <nav class="text-sm text-navy-400 mb-6">
            <a href="{{ route('shop.home') }}" class="hover:text-gold-600">Beranda</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop.katalog') }}" class="hover:text-gold-600">Katalog</a>
            @if ($product->kategori)
                <span class="mx-2">/</span>
                <a href="{{ route('shop.katalog', ['kategori' => $product->kategori]) }}" class="hover:text-gold-600">{{ $product->kategori }}</a>
            @endif
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            {{-- Galeri --}}
            <div class="bg-white rounded-2xl border border-navy-100 p-4">
                <div class="relative aspect-square rounded-xl overflow-hidden bg-navy-50">
                    <img src="{{ $product->foto_url }}" alt="{{ $product->nama_produk }}" loading="lazy"
                         class="w-full h-full object-cover">
                    <span class="absolute top-3 left-3 text-[11px] font-bold bg-navy-900/80 text-white px-2.5 py-1 rounded-full">{{ $product->kondisi_label }}</span>
                    @if ($product->tersedia)
                        <span class="absolute top-3 right-3 w-3 h-3 rounded-full bg-emerald-500"></span>
                    @else
                        <span class="absolute top-3 right-3 text-[11px] font-bold bg-rose-600 text-white px-2.5 py-1 rounded-full">Stok Habis</span>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div>
                <p class="text-sm font-semibold text-gold-600 uppercase tracking-wide">{{ $product->kategori }} · {{ $product->kondisi_label }}</p>
                <h1 class="text-2xl md:text-3xl font-black text-navy-900 mt-1">{{ $product->nama_produk }}</h1>

                <div class="flex items-center gap-3 mt-3">
                    @if ($product->tersedia)
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Ready Stock ({{ $product->stock }} {{ $product->satuan }})
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-rose-100 text-rose-700 px-3 py-1 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span> Stok Habis
                        </span>
                    @endif
                    @if ($product->is_featured)
                        <span class="text-xs font-bold bg-gold-100 text-gold-700 px-3 py-1 rounded-full">★ Unggulan</span>
                    @endif
                </div>

                <p class="mt-4 text-3xl font-black text-gold-600">Rp {{ number_format((float) $product->harga_jual, 0, ',', '.') }}</p>

                @if ($product->deskripsi)
                    <div class="prose-sm text-navy-600 mt-5 leading-relaxed whitespace-pre-line">{{ $product->deskripsi }}</div>
                @endif

                <div class="mt-6 space-y-3">
                    <a href="{{ \App\Support\WhatsApp::link($product) }}" target="_blank" rel="noopener"
                       class="flex items-center justify-center gap-2 w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black px-6 py-4 rounded-xl transition-colors text-lg">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.57 15.07L2 22l5.06-1.37A10 10 0 1 0 12 2zm5.5 14.1c-.23.65-1.34 1.24-1.86 1.29-.5.05-1.09.22-3.64-.76-3.06-1.17-5-4.27-5.16-4.47-.15-.2-1.24-1.65-1.24-3.14 0-1.5.79-2.24 1.07-2.54.28-.3.6-.38.8-.38h.58c.18 0 .44-.07.68.52.25.6.84 2.06.91 2.21.08.15.13.33.02.53-.1.2-.15.32-.3.5-.15.17-.32.38-.45.5-.15.13-.31.27-.13.54.17.27.78 1.28 1.67 2.08 1.15 1.02 2.12 1.34 2.42 1.49.3.15.47.13.65-.08.17-.2.75-.88.95-1.18.2-.3.4-.25.67-.15.28.1 1.76.83 2.06.98.3.15.5.23.58.35.07.13.07.73-.17 1.43z"/></svg>
                        {{ $product->tersedia ? 'Tanya Stok / Pesan via WhatsApp' : 'Pre-Order / Hubungi Admin' }}
                    </a>
                    @if ($product->warranty_info)
                        <p class="text-xs text-navy-400 text-center">🛡 {{ $product->warranty_info }}</p>
                    @endif
                </div>

                <div class="mt-6 bg-navy-50 rounded-xl px-4 py-3 text-sm text-navy-600 flex items-start gap-2">
                    <span>🏪</span>
                    <span>{{ $settings['store_address'] }}<br>🕘 {{ $settings['jam_operasional'] }}</span>
                </div>
            </div>
        </div>

        {{-- Spesifikasi --}}
        @if (!empty($product->specifications))
            <div class="mt-10 bg-white rounded-2xl border border-navy-100 p-6">
                <h2 class="font-black text-navy-900 text-lg mb-4">Spesifikasi Teknis</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-navy-100 rounded-xl overflow-hidden">
                    @foreach ($product->specifications as $label => $value)
                        <div class="bg-white px-4 py-3 flex justify-between gap-4">
                            <span class="text-sm text-navy-400 font-medium">{{ $label }}</span>
                            <span class="text-sm font-semibold text-navy-800 text-right">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Rekomendasi --}}
        @if ($related->isNotEmpty())
            <section class="mt-10">
                <h2 class="font-black text-navy-900 text-lg mb-4">Produk Terkait</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($related as $rel)
                        <div class="group bg-white rounded-2xl border border-navy-100 overflow-hidden hover:shadow-xl transition-all">
                            <a href="{{ route('shop.produk', $rel->id) }}" class="block relative aspect-square bg-navy-50">
                                <img src="{{ $rel->foto_url }}" alt="{{ $rel->nama_produk }}" loading="lazy" class="w-full h-full object-cover">
                            </a>
                            <div class="p-4">
                                <p class="text-xs text-navy-400">{{ $rel->kategori }}</p>
                                <a href="{{ route('shop.produk', $rel->id) }}" class="block font-bold text-navy-900 leading-snug group-hover:text-gold-600 transition-colors">{{ $rel->nama_produk }}</a>
                                <p class="text-base font-black text-gold-600 mt-1">Rp {{ number_format((float) $rel->harga_jual, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

@endsection