@extends('layouts.store')

@section('title', 'Toko Online')

@section('meta_desc', 'Beli gadget original dengan harga terbaik: iPhone, iPad, MacBook, dan aksesori lainnya. Ready stock, garansi toko & resmi.')

@section('content')

    {{-- Hero slider --}}
    <section class="hero-slider relative overflow-hidden bg-navy-900">
        @forelse ($banners as $banner)
            <div class="hero-slide {{ $loop->first ? '' : 'hidden' }} relative"
                 data-hero-index="{{ $loop->index }}">
                <div class="absolute inset-0" style="background:
                    radial-gradient(700px 380px at 85% -10%, rgba(232,163,61,0.45), transparent 60%),
                    radial-gradient(600px 480px at -10% 120%, rgba(61,100,145,0.6), transparent 60%);"></div>
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-20 md:py-28 text-white">
                    <span class="inline-block text-xs font-bold uppercase tracking-widest text-gold-400 mb-4 px-3 py-1 rounded-full bg-white/10">
                        {{ $settings['tags_line'] }}
                    </span>
                    <h1 class="text-3xl md:text-5xl font-black leading-tight max-w-2xl">{{ $banner->title }}</h1>
                    @if ($banner->subtitle)
                        <p class="mt-4 text-navy-100/90 max-w-xl text-sm md:text-base">{{ $banner->subtitle }}</p>
                    @endif
                    @if ($banner->cta_link)
                        <a href="{{ $banner->cta_link }}"
                           class="inline-flex items-center gap-2 mt-7 bg-gold-500 hover:bg-gold-400 text-navy-900 font-bold px-6 py-3 rounded-full transition-colors">
                            Lihat Katalog
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-20 md:py-28 text-white">
                <span class="inline-block text-xs font-bold uppercase tracking-widest text-gold-400 mb-4 px-3 py-1 rounded-full bg-white/10">{{ $settings['tags_line'] }}</span>
                <h1 class="text-3xl md:text-5xl font-black leading-tight max-w-2xl">Gadget Original,<br>Harga Terbaik.</h1>
                <p class="mt-4 text-navy-100/90 max-w-xl text-sm md:text-base">Ready stock dengan garansi toko & resmi. Pesan mudah via WhatsApp.</p>
                <a href="{{ route('shop.katalog') }}" class="inline-flex items-center gap-2 mt-7 bg-gold-500 hover:bg-gold-400 text-navy-900 font-bold px-6 py-3 rounded-full transition-colors">Lihat Katalog</a>
            </div>
        @endforelse

        @if (count($banners) > 1)
            <div class="relative flex justify-center gap-2 pb-6">
                @foreach ($banners as $banner)
                    <span data-hero-dot="{{ $loop->index }}" class="w-2 h-2 rounded-full {{ $loop->first ? 'bg-gold-500' : 'bg-navy-200' }} transition-colors"></span>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Kategori cepat --}}
    @if ($categories->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm font-semibold text-navy-500 mr-1">Kategori:</span>
                <a href="{{ route('shop.katalog') }}" class="px-4 py-2 text-sm font-semibold rounded-full border border-navy-100 bg-white text-navy-800 hover:border-gold-500 hover:text-gold-600 transition-colors">Semua</a>
                @foreach ($categories as $kategori)
                    <a href="{{ route('shop.katalog', ['kategori' => $kategori]) }}"
                       class="px-4 py-2 text-sm font-semibold rounded-full border border-navy-100 bg-white text-navy-800 hover:border-gold-500 hover:text-gold-600 transition-colors">
                        {{ $kategori }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Produk unggulan --}}
    <section id="unggulan" class="max-w-7xl mx-auto px-4 sm:px-6 py-6 pb-14">
        <div class="flex items-end justify-between mb-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Pilihan Kami</p>
                <h2 class="text-2xl font-black text-navy-900 mt-1">Produk Unggulan</h2>
            </div>
            <a href="{{ route('shop.katalog') }}" class="text-sm font-semibold text-navy-600 hover:text-gold-600 transition-colors">Lihat semua →</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            @forelse ($featured as $p)
                <div class="group bg-white rounded-2xl border border-navy-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all">
                    <a href="{{ route('shop.produk', $p->id) }}" class="block relative aspect-square bg-navy-50">
                        <img src="{{ $p->foto_url }}" alt="{{ $p->nama_produk }}" loading="lazy" class="w-full h-full object-cover">
                        <span class="absolute top-3 left-3 text-[10px] font-bold bg-navy-900/80 text-white px-2 py-1 rounded-full">{{ $p->kondisi_label }}</span>
                        @if (!$p->tersedia)
                            <span class="absolute top-3 right-3 text-[10px] font-bold bg-rose-600 text-white px-2 py-1 rounded-full">Stok Habis</span>
                        @endif
                    </a>
                    <div class="p-4">
                        <p class="text-xs text-navy-400 font-medium">{{ $p->kategori }}</p>
                        <a href="{{ route('shop.produk', $p->id) }}" class="block font-bold text-navy-900 mt-0.5 leading-snug group-hover:text-gold-600 transition-colors">{{ $p->nama_produk }}</a>
                        <p class="text-lg font-black text-gold-600 mt-2">Rp {{ number_format((float) $p->harga_jual, 0, ',', '.') }}</p>
                        <div class="mt-3 flex items-center gap-2">
                            <a href="{{ \App\Support\WhatsApp::link($p) }}" target="_blank" rel="noopener"
                               class="flex-1 flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-2.5 rounded-lg transition-colors">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.57 15.07L2 22l5.06-1.37A10 10 0 1 0 12 2zm5.5 14.1c-.23.65-1.34 1.24-1.86 1.29-.5.05-1.09.22-3.64-.76-3.06-1.17-5-4.27-5.16-4.47-.15-.2-1.24-1.65-1.24-3.14 0-1.5.79-2.24 1.07-2.54.28-.3.6-.38.8-.38h.58c.18 0 .44-.07.68.52.25.6.84 2.06.91 2.21.08.15.13.33.02.53-.1.2-.15.32-.3.5-.15.17-.32.38-.45.5-.15.13-.31.27-.13.54.17.27.78 1.28 1.67 2.08 1.15 1.02 2.12 1.34 2.42 1.49.3.15.47.13.65-.08.17-.2.75-.88.95-1.18.2-.3.4-.25.67-.15.28.1 1.76.83 2.06.98.3.15.5.23.58.35.07.13.07.73-.17 1.43z"/></svg>
                                Pesan
                            </a>
                            <a href="{{ route('shop.produk', $p->id) }}"
                               class="flex-1 text-center border border-navy-100 hover:border-gold-500 text-navy-700 hover:text-gold-600 font-bold px-3 py-2.5 rounded-lg transition-colors">Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-navy-400">Belum ada produk unggulan. Atur di Dashboard → Produk (centang Unggulan).</div>
            @endforelse
        </div>
    </section>

@endsection