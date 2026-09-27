@extends('layouts.store')

@section('title', $product->nama_produk . ' — ' . $product->kondisi_label)

@section('meta_desc', 'Harga ' . $product->nama_produk . ' Rp ' . number_format($product->harga_aktif, 0, ',', '.') . ' · ' . $product->ketersediaan . ' di ' . $settings['store_name'] . '.')

@push('head_meta')
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $product->nama_produk }} — {{ $settings['store_name'] }}">
    <meta property="og:description" content="Harga Rp {{ number_format($product->harga_aktif, 0, ',', '.') }} · {{ $product->ketersediaan }}">
    <meta property="og:url" content="{{ route('shop.produk', $product->id) }}">
    <meta property="og:image" content="{{ $product->foto_url }}">
    <meta property="og:site_name" content="{{ $settings['store_name'] }}">
    <meta name="twitter:card" content="summary_large_image">
@endpush

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 pb-28 md:pb-8">
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
                    <div class="absolute top-3 right-3 flex flex-col items-end gap-1.5">
                        @if ($product->harga_promo_aktif)
                            <span class="text-[11px] font-black bg-gold-500 text-navy-900 px-2.5 py-1 rounded-full">-{{ $product->diskon_persen }}%</span>
                        @endif
                        @if (!$product->tersedia)
                            <span class="text-[11px] font-bold bg-rose-600 text-white px-2.5 py-1 rounded-full">Stok Habis</span>
                        @else
                            <span class="text-[11px] font-bold bg-emerald-500 text-white px-2.5 py-1 rounded-full">● Ready Stock</span>
                        @endif
                    </div>
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

                <div class="mt-4 flex items-end gap-3">
                    <p class="text-3xl font-black {{ $product->harga_promo_aktif ? 'text-rose-600' : 'text-gold-600' }}">Rp {{ number_format($product->harga_aktif, 0, ',', '.') }}</p>
                    @if ($product->harga_promo_aktif)
                        <p class="text-lg text-navy-400 line-through mb-1">Rp {{ number_format((float) $product->harga_jual, 0, ',', '.') }}</p>
                    @endif
                </div>

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

                {{-- Tombol Bagikan --}}
                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <button type="button" onclick="copyProductLink()"
                            class="inline-flex items-center gap-2 border border-navy-100 bg-white hover:border-gold-500 text-navy-700 font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Bagikan
                    </button>
                    <a href="{{ $product->url_wa_share }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors"
                       aria-label="Bagikan ke WhatsApp">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.57 15.07L2 22l5.06-1.37A10 10 0 1 0 12 2zm5.5 14.1c-.23.65-1.34 1.24-1.86 1.29-.5.05-1.09.22-3.64-.76-3.06-1.17-5-4.27-5.16-4.47-.15-.2-1.24-1.65-1.24-3.14 0-1.5.79-2.24 1.07-2.54.28-.3.6-.38.8-.38h.58c.18 0 .44-.07.68.52.25.6.84 2.06.91 2.21.08.15.13.33.02.53-.1.2-.15.32-.3.5-.15.17-.32.38-.45.5-.15.13-.31.27-.13.54.17.27.78 1.28 1.67 2.08 1.15 1.02 2.12 1.34 2.42 1.49.3.15.47.13.65-.08.17-.2.75-.88.95-1.18.2-.3.4-.25.67-.15.28.1 1.76.83 2.06.98.3.15.5.23.58.35.07.13.07.73-.17 1.43z"/></svg>
                        WhatsApp
                    </a>
                    <a href="{{ $product->url_teleg_share }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 border border-sky-200 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors"
                       aria-label="Bagikan ke Telegram">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M21.6 3.6l-3.2 15.1c-.2.9-.7 1.1-1.5.7l-4.1-3-2 1.9c-.2.2-.4.4-.9.4l.3-4.2 7.6-6.9c.3-.3-.1-.4-.5-.2L7.2 13.4l-4-1.2c-.9-.3-.9-.9.2-1.3L20.2 2.3c.7-.3 1.4.1 1.4.9z" transform="translate(0 -1)"/></svg>
                        Telegram
                    </a>
                </div>

                {{-- Pertanyaan Cepat WhatsApp --}}
                <div class="mt-5 bg-navy-50 rounded-2xl p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-navy-500 mb-3">💬 Pertanyaan Cepat</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <a href="{{ \App\Support\WhatsApp::questionLink($product, 'saya mau tanya kondisi bodi & battery health untuk produk ini:') }}" target="_blank" rel="noopener"
                           class="flex items-center gap-2 border border-navy-100 bg-white hover:border-emerald-400 text-navy-700 text-xs font-semibold px-3 py-2.5 rounded-xl transition-colors">
                            🔋 Tanya Kondisi & Battery Health
                        </a>
                        <a href="{{ \App\Support\WhatsApp::questionLink($product, 'saya mau tanya estimasi tukar tambah (trade-in) untuk produk ini:') }}" target="_blank" rel="noopener"
                           class="flex items-center gap-2 border border-navy-100 bg-white hover:border-emerald-400 text-navy-700 text-xs font-semibold px-3 py-2.5 rounded-xl transition-colors">
                            🔄 Tanya Estimasi Tukar Tambah
                        </a>
                        <a href="{{ \App\Support\WhatsApp::questionLink($product, 'saya mau tanya jadwal kunjungan ke toko untuk cek unit ini:') }}" target="_blank" rel="noopener"
                           class="flex items-center gap-2 border border-navy-100 bg-white hover:border-emerald-400 text-navy-700 text-xs font-semibold px-3 py-2.5 rounded-xl transition-colors">
                            📍 Tanya Jadwal Kunjungan
                        </a>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-navy-100 bg-white p-5">
                    <p class="text-sm font-black text-navy-900 mb-3">💳 Metode Pembayaran</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-navy-50 text-navy-700 px-3 py-1.5 rounded-full">💵 Tunai di Toko</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-navy-50 text-navy-700 px-3 py-1.5 rounded-full">🏦 Transfer BCA · Mandiri · BRI · BNI</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-navy-50 text-navy-700 px-3 py-1.5 rounded-full">📱 QRIS · GoPay · OVO · DANA</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-navy-50 text-navy-700 px-3 py-1.5 rounded-full">💳 EDC Debit / Kredit</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-navy-50 text-navy-700 px-3 py-1.5 rounded-full">🪙 Cicilan / PayLater</span>
                    </div>
                </div>

                <div class="mt-5 bg-white rounded-xl border border-navy-100 px-4 py-3 text-sm text-navy-600 flex items-start gap-2">
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
                        @include('store.partials.produk-card', ['produk' => $rel])
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    {{-- Sticky Quick Action Bar (mobile) --}}
    <div class="fixed bottom-0 inset-x-0 z-40 md:hidden bg-white/95 backdrop-blur border-t border-navy-100 px-4 py-3 flex items-center gap-3"
         aria-label="Aksi cepat">
        <a href="{{ \App\Support\WhatsApp::link($product) }}" target="_blank" rel="noopener"
           class="flex-1 flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm px-4 py-3 rounded-xl transition-colors">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.57 15.07L2 22l5.06-1.37A10 10 0 1 0 12 2zm5.5 14.1c-.23.65-1.34 1.24-1.86 1.29-.5.05-1.09.22-3.64-.76-3.06-1.17-5-4.27-5.16-4.47-.15-.2-1.24-1.65-1.24-3.14 0-1.5.79-2.24 1.07-2.54.28-.3.6-.38.8-.38h.58c.18 0 .44-.07.68.52.25.6.84 2.06.91 2.21.08.15.13.33.02.53-.1.2-.15.32-.3.5-.15.17-.32.38-.45.5-.15.13-.31.27-.13.54.17.27.78 1.28 1.67 2.08 1.15 1.02 2.12 1.34 2.42 1.49.3.15.47.13.65-.08.17-.2.75-.88.95-1.18.2-.3.4-.25.67-.15.28.1 1.76.83 2.06.98.3.15.5.23.58.35.07.13.07.73-.17 1.43z"/></svg>
            Pesan via WhatsApp
        </a>
        <button type="button" onclick="copyProductLink()"
                class="grid place-items-center w-12 h-12 rounded-xl border border-navy-100 text-navy-700 hover:border-gold-500 transition-colors"
                aria-label="Salin tautan produk">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
        </button>
    </div>

@endsection

@push('page_scripts')
    <script>
        function copyProductLink() {
            const url = window.location.href;
            const done = () => showToast('Link berhasil disalin!');
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(done).catch(() => fallbackCopy(url, done));
            } else {
                fallbackCopy(url, done);
            }
        }
        function fallbackCopy(text, done) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); done(); } catch (e) { showToast('Gagal menyalin link'); }
            document.body.removeChild(ta);
        }
    </script>
@endpush