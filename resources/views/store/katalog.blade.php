@extends('layouts.store')

@section('title', 'Katalog')

@section('meta_desc', 'Katalog produk ' . $settings['store_name'] . ' — cari gadget, bandingkan harga, dan pesan langsung via WhatsApp.')

@section('content')

    @if ($resellerMode)
        <div class="bg-gold-500/15 border-b border-gold-500/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-3">
                <p class="text-sm font-bold text-navy-800">
                    Mode Reseller aktif — diskon <span class="text-gold-700">tier grosir/partai</span> berlaku otomatis sesuai jumlah.
                </p>
                <a href="{{ route('shop.mitra.beranda') }}"
                   class="shrink-0 text-xs font-black bg-navy-900 text-white px-4 py-2 rounded-full hover:bg-navy-800 transition-colors">
                    Portal Mitra →
                </a>
            </div>
        </div>
    @endif

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

        @php
            $tabUrl = function (?string $kondisi): string {
                $q = collect(request()->query())->except(['page', 'condition'])
                    ->filter(fn ($v) => $v !== '' && $v !== null)->toArray();
                if ($kondisi) {
                    $q['condition'] = $kondisi;
                }
                return route('shop.katalog') . ($q ? '?' . http_build_query($q) : '');
            };
            $tabKondisiAktif = request('condition') ?: '';
            $tabs = [
                ['kode' => '',      'label' => 'Semua'],
                ['kode' => 'new',   'label' => 'Baru / Segel'],
                ['kode' => 'like-new', 'label' => 'Bekas Mulus'],
                ['kode' => 'used',  'label' => 'Second'],
            ];
        @endphp

        <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1 -mx-1 px-1">
            <span class="shrink-0 text-xs font-black uppercase tracking-wide text-navy-400 mr-1">Kondisi:</span>
            @foreach ($tabs as $tab)
                <a href="{{ $tabUrl($tab['kode'] ?: null) }}"
                   class="shrink-0 inline-flex items-center gap-1.5 px-5 py-2.5 rounded-full text-sm font-black transition-colors
                          {{ $tabKondisiAktif === $tab['kode']
                              ? 'bg-navy-900 text-white shadow-lg'
                              : 'bg-white border border-navy-100 text-navy-600 hover:border-gold-500 hover:text-gold-600' }}">
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

        @php
            $chips = [];
            $base = route('shop.katalog');
            $remove = fn (string $except) => $base . (function () use ($except) {
                $q = collect(request()->query())->except([$except, 'page'])->filter(fn ($v) => $v !== '' && $v !== null)->toArray();
                return $q ? '?' . http_build_query($q) : '';
            })();

            if (request('q')) {
                $chips[] = ['label' => 'Cari: ' . request('q'), 'url' => $remove('q')];
            }
            if (request('kategori')) {
                $chips[] = ['label' => 'Kategori: ' . request('kategori'), 'url' => $remove('kategori')];
            }
            if (request('brand')) {
                $chips[] = ['label' => 'Brand: ' . request('brand'), 'url' => $remove('brand')];
            }
            if (request('condition')) {
                $kondisiLabel = match (request('condition')) {
                    'like-new' => 'Bekas Mulus',
                    'used' => 'Second',
                    default => 'Baru / Segel',
                };
                $chips[] = ['label' => 'Kondisi: ' . $kondisiLabel, 'url' => $remove('condition')];
            }
            if (request('ready')) {
                $chips[] = ['label' => 'Ready Stock', 'url' => $remove('ready')];
            }
            if (request('grosir')) {
                $chips[] = ['label' => 'Grosir / Partai', 'url' => $remove('grosir')];
            }
            if (request('min') || request('max')) {
                $chips[] = ['label' => 'Harga: Rp ' . (request('min') ?: '0') . ' – ' . (request('max') ?: '∞'), 'url' => $remove('min') . (request('max') ? '' : '')];
                $q2 = collect(request()->query())->except(['min', 'page'])->filter(fn ($v) => $v !== '' && $v !== null)->toArray();
                $chips[count($chips) - 1]['url'] = $base . ($q2 ? '?' . http_build_query($q2) : '');
            }
            if (request('sort')) {
                $sortLabel = match (request('sort')) {
                    'termurah' => 'Harga Terendah',
                    'termahal' => 'Harga Tertinggi',
                    'terbaru' => 'Produk Terbaru',
                    'populer' => 'Paling Populer',
                    default => request('sort'),
                };
                $chips[] = ['label' => 'Urut: ' . $sortLabel, 'url' => $remove('sort')];
            }
        @endphp

        @if ($chips)
            <div class="flex flex-wrap items-center gap-2 mb-5">
                <span class="text-xs font-semibold text-navy-400">Filter aktif:</span>
                @foreach ($chips as $chip)
                    <a href="{{ $chip['url'] }}" title="Hapus filter ini"
                       class="inline-flex items-center gap-1.5 text-xs font-bold bg-navy-900 text-white px-3 py-1.5 rounded-full hover:bg-rose-600 transition-colors">
                        {{ $chip['label'] }}
                        <span class="text-white/80" aria-hidden="true">✕</span>
                    </a>
                @endforeach
                <a href="{{ route('shop.katalog') }}" class="text-xs font-semibold text-navy-500 hover:text-gold-600 underline transition-colors">Reset semua</a>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Filter sidebar (desktop) --}}
            <aside class="hidden lg:block lg:col-span-1">
                <div class="bg-white rounded-2xl border border-navy-100 p-5 sticky top-20">
                    @include('store.partials.filter-form', ['categories' => $categories, 'brands' => $brands, 'includeSort' => false])
                </div>
            </aside>

            {{-- Grid produk --}}
            <div class="lg:col-span-3">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <p class="text-sm text-navy-500">Urutkan:</p>
                    <div class="flex items-center gap-2">
                        <div class="md:hidden">
                            <button type="button" onclick="toggleFilterSheet(true)"
                                    class="flex items-center gap-2 bg-navy-900 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.5V19l4 2v-8.5L22 3z"/></svg>
                                Filter & Urutkan
                                @if ($chips)
                                    <span class="grid place-items-center w-5 h-5 rounded-full bg-gold-500 text-navy-900 text-[10px] font-black">{{ count($chips) }}</span>
                                @endif
                            </button>
                        </div>
                        <form method="GET" action="{{ route('shop.katalog') }}" class="hidden md:block">
                            @foreach (['q', 'kategori', 'brand', 'min', 'max', 'ready', 'grosir', 'condition'] as $f)
                                @if (request($f))
                                    <input type="hidden" name="{{ $f }}" value="{{ request($f) }}">
                                @endif
                            @endforeach
                            <select name="sort" onchange="this.form.submit()" aria-label="Urutkan produk"
                                    class="rounded-lg border border-navy-100 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                                <option value="">Rekomendasi</option>
                                <option value="termurah" @selected(request('sort') === 'termurah')>Harga Terendah</option>
                                <option value="termahal" @selected(request('sort') === 'termahal')>Harga Tertinggi</option>
                                <option value="terbaru" @selected(request('sort') === 'terbaru')>Produk Terbaru</option>
                                <option value="populer" @selected(request('sort') === 'populer')>Paling Populer</option>
                            </select>
                        </form>
                    </div>
                </div>

                @if ($products->isEmpty())
                    <div class="bg-white rounded-2xl border border-navy-100 p-12 text-center text-navy-400">
                        Tidak ada produk yang cocok dengan filter ini.
                    </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-5">
                    @foreach ($products as $p)
                        @include('store.partials.produk-card', ['produk' => $p])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Filter Bottom Sheet --}}
    <div id="filterSheet" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-label="Filter dan urutkan produk">
        <div class="absolute inset-0 bg-navy-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-300" onclick="toggleFilterSheet(false)"></div>
        <div class="absolute inset-x-0 bottom-0 bg-white rounded-t-3xl translate-y-full transition-transform duration-300 flex flex-col max-h-[85vh]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-navy-100">
                <p class="font-black text-navy-900">Filter & Urutkan</p>
                <button type="button" onclick="toggleFilterSheet(false)" class="grid place-items-center w-9 h-9 rounded-xl text-navy-500 hover:bg-navy-50 transition-colors" aria-label="Tutup filter">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
            <div class="p-5 overflow-y-auto">
                @include('store.partials.filter-form', ['categories' => $categories, 'brands' => $brands, 'includeSort' => true])
                @if ($chips)
                    <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-navy-100">
                        @foreach ($chips as $chip)
                            <a href="{{ $chip['url'] }}" class="inline-flex items-center gap-1.5 text-xs font-bold bg-navy-900 text-white px-3 py-1.5 rounded-full hover:bg-rose-600 transition-colors">
                                {{ $chip['label'] }} <span aria-hidden="true">✕</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@push('page_scripts')
    <script>
        function toggleFilterSheet(open) {
            const sheet = document.getElementById('filterSheet');
            if (!sheet) return;
            const panel = sheet.querySelector('.translate-y-full');
            const overlay = sheet.querySelector('.bg-navy-900\\/60');
            if (open) {
                sheet.classList.remove('hidden');
                requestAnimationFrame(() => {
                    panel.classList.remove('translate-y-full');
                    overlay.classList.add('opacity-100');
                });
                document.body.classList.add('overflow-hidden');
            } else {
                panel.classList.add('translate-y-full');
                overlay.classList.remove('opacity-100');
                document.body.classList.remove('overflow-hidden');
                setTimeout(() => sheet.classList.add('hidden'), 300);
            }
        }
    </script>
@endpush