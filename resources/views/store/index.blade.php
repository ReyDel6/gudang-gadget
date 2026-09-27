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
            <button type="button" class="hero-prev absolute left-2 md:left-4 top-1/2 -translate-y-1/2 z-10 grid place-items-center w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/15 hover:bg-white/30 text-white backdrop-blur transition-colors"
                    aria-label="Banner sebelumnya">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
            </button>
            <button type="button" class="hero-next absolute right-2 md:right-4 top-1/2 -translate-y-1/2 z-10 grid place-items-center w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/15 hover:bg-white/30 text-white backdrop-blur transition-colors"
                    aria-label="Banner berikutnya">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
            </button>
            <div class="relative flex justify-center gap-2 pb-6">
                @foreach ($banners as $banner)
                    <button type="button" data-hero-dot="{{ $loop->index }}" aria-label="Banner ke-{{ $loop->iteration }}"
                            class="w-2 h-2 rounded-full {{ $loop->first ? 'bg-gold-500' : 'bg-navy-200' }} transition-colors hover:scale-125"></button>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Keunggulan Toko (Trust Badges) --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 -mt-1 py-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5">
            <div class="flex items-start gap-3 bg-white rounded-2xl border border-navy-100 p-4">
                <span class="text-2xl leading-none">🛡️</span>
                <div>
                    <p class="text-sm font-black text-navy-900">100% Original & Bergaransi</p>
                    <p class="text-xs text-navy-500 mt-0.5 leading-relaxed">Garansi toko & resmi terjamin.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 bg-white rounded-2xl border border-navy-100 p-4">
                <span class="text-2xl leading-none">🏬</span>
                <div>
                    <p class="text-sm font-black text-navy-900">Toko Fisik Jelas</p>
                    <p class="text-xs text-navy-500 mt-0.5 leading-relaxed">Cek unit langsung & bayar di tempat / COD.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 bg-white rounded-2xl border border-navy-100 p-4">
                <span class="text-2xl leading-none">🔄</span>
                <div>
                    <p class="text-sm font-black text-navy-900">Layanan Tukar Tambah</p>
                    <p class="text-xs text-navy-500 mt-0.5 leading-relaxed">Terima trade-in gadget lama ke baru.</p>
                </div>
            </div>
            <div class="flex items-start gap-3 bg-white rounded-2xl border border-navy-100 p-4">
                <span class="text-2xl leading-none">⚡</span>
                <div>
                    <p class="text-sm font-black text-navy-900">Pengiriman Cepat & Aman</p>
                    <p class="text-xs text-navy-500 mt-0.5 leading-relaxed">Packing kayu, bubble wrap tebal, asuransi penuh.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Kategori cepat --}}
    @if ($categories->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
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
                @include('store.partials.produk-card', ['produk' => $p])
            @empty
                <div class="col-span-full text-center py-12 text-navy-400">Belum ada produk unggulan. Atur di Dashboard → Produk (centang Unggulan).</div>
            @endforelse
        </div>
    </section>

    {{-- Kunjungi Toko --}}
    <section id="lokasi" class="bg-navy-900 text-navy-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
            <div class="mb-8">
                <p class="text-xs font-bold uppercase tracking-widest text-gold-400">Kunjungi Toko Kami</p>
                <h2 class="text-2xl md:text-3xl font-black text-white mt-1">Datang Langsung, Cek Unit Sebelum Beli</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
                <div class="space-y-5">
                    @if ($settings['store_photo'])
                        <img src="{{ $settings['store_photo'] }}" alt="Foto etalase {{ $settings['store_name'] }}" loading="lazy"
                             class="w-full h-48 md:h-56 object-cover rounded-2xl border border-white/10">
                    @endif
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                        <p class="font-black text-white">📍 Alamat</p>
                        <p class="text-sm text-navy-200/90 mt-1 leading-relaxed">{{ $settings['store_address'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                        <p class="font-black text-white">🕘 Jam Operasional</p>
                        <p class="text-sm text-navy-200/90 mt-1 leading-relaxed">{{ $settings['jam_operasional'] }}</p>
                    </div>
                </div>
                <div class="rounded-2xl overflow-hidden border border-white/10 min-h-[280px]">
                    @if ($settings['maps_embed'])
                        <iframe src="{{ $settings['maps_embed'] }}" width="100%" height="100%" class="min-h-[280px] w-full" style="border:0" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Peta lokasi {{ $settings['store_name'] }}"></iframe>
                    @else
                        <div class="grid place-items-center h-full min-h-[280px] text-navy-200/70 text-sm p-6 text-center">Peta belum diatur di Pengaturan Toko.</div>
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection