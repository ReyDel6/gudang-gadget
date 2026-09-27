@extends('layouts.store')

@section('title', 'Katalog')

@section('meta_desc', 'Katalog produk ' . $settings['store_name'] . ' — cari gadget, bandingkan harga, dan pesan langsung via WhatsApp.')

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-navy-900">Katalog Produk</h1>
                <p class="text-sm text-navy-500 mt-1">{{ $products->total() }} produk tersedia · klik Pesan untuk order via WhatsApp.</p>
            </div>
            <form method="GET" action="{{ route('shop.katalog') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari iPhone, iPad, dll..."
                       class="w-56 rounded-lg border border-navy-100 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                <button type="submit" class="bg-navy-900 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">Cari</button>
            </form>
        </div>

        @if (request()->hasAny(['q', 'kategori', 'brand', 'min', 'max', 'ready', 'condition', 'sort']))
            <a href="{{ route('shop.katalog') }}" class="inline-block text-xs font-semibold text-navy-500 hover:text-gold-600 mb-4">✕ Reset semua filter</a>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Filter sidebar --}}
            <aside class="lg:col-span-1">
                <form method="GET" action="{{ route('shop.katalog') }}" class="space-y-4 bg-white rounded-2xl border border-navy-100 p-5 sticky top-20">
                    @if (request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    @if (request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    <div>
                        <label class="text-xs font-bold uppercase tracking-wide text-navy-500">Kategori</label>
                        <select name="kategori" onchange="this.form.submit()"
                                class="mt-1.5 w-full rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $kategori)
                                <option value="{{ $kategori }}" @selected(request('kategori') === $kategori)>{{ $kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold uppercase tracking-wide text-navy-500">Brand</label>
                        <select name="brand" onchange="this.form.submit()"
                                class="mt-1.5 w-full rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                            <option value="">Semua Brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand }}" @selected(request('brand') === $brand)>{{ $brand }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold uppercase tracking-wide text-navy-500">Rentang Harga (Rp)</label>
                        <div class="mt-1.5 flex items-center gap-2">
                            <input type="number" name="min" value="{{ request('min') }}" placeholder="Min"
                                   class="w-full rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                            <input type="number" name="max" value="{{ request('max') }}" placeholder="Maks"
                                   class="w-full rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold uppercase tracking-wide text-navy-500">Kondisi</label>
                        <select name="condition" onchange="this.form.submit()"
                                class="mt-1.5 w-full rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                            <option value="">Semua Kondisi</option>
                            <option value="new" @selected(request('condition') === 'new')>Baru / Segel</option>
                            <option value="like-new" @selected(request('condition') === 'like-new')>Bekas Mulus</option>
                            <option value="used" @selected(request('condition') === 'used')>Second</option>
                        </select>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-navy-700 cursor-pointer">
                        <input type="checkbox" name="ready" value="1" @checked(request('ready'))
                               onchange="this.form.submit()" class="h-4 w-4 rounded border-navy-200 text-gold-500 focus:ring-gold-500">
                        Hanya Ready Stock
                    </label>

                    <button type="submit" class="w-full bg-navy-900 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">Terapkan Filter</button>
                </form>
            </aside>

            {{-- Grid produk --}}
            <div class="lg:col-span-3">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-navy-500">Urutkan:</p>
                    <form method="GET" action="{{ route('shop.katalog') }}" class="flex gap-2 flex-wrap">
                        @foreach (['q', 'kategori', 'brand', 'min', 'max', 'ready', 'condition'] as $f)
                            @if (request($f))
                                <input type="hidden" name="{{ $f }}" value="{{ request($f) }}">
                            @endif
                        @endforeach
                        <select name="sort" onchange="this.form.submit()"
                                class="rounded-lg border border-navy-100 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                            <option value="">Rekomendasi</option>
                            <option value="termurah" @selected(request('sort') === 'termurah')>Harga Terendah</option>
                            <option value="termahal" @selected(request('sort') === 'termahal')>Harga Tertinggi</option>
                            <option value="terbaru" @selected(request('sort') === 'terbaru')>Produk Terbaru</option>
                            <option value="populer" @selected(request('sort') === 'populer')>Paling Populer</option>
                        </select>
                    </form>
                </div>

                @if ($products->isEmpty())
                    <div class="bg-white rounded-2xl border border-navy-100 p-12 text-center text-navy-400">
                        Tidak ada produk yang cocok dengan filter ini.
                    </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-5">
                    @foreach ($products as $p)
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
                                        Pesan via WA
                                    </a>
                                    <a href="{{ route('shop.produk', $p->id) }}"
                                       class="flex-1 text-center border border-navy-100 hover:border-gold-500 text-navy-700 hover:text-gold-600 font-bold px-3 py-2.5 rounded-lg transition-colors">Detail</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection