<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Gudang Gadget')</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        @vite('resources/css/app.css')
    </head>
    <body class="bg-navy-100/60 text-navy-900 min-h-screen">

        @php
            $routeName = request()->route() ? request()->route()->getName() : '';
        @endphp

        <div class="min-h-screen flex">

            {{-- SIDEBAR --}}
            <aside id="sidebar"
                   class="w-64 bg-navy-900 text-slate-300 flex flex-col shadow-xl transition-all duration-300 overflow-hidden print-hidden">

                {{-- Brand / Logo --}}
                <div class="p-6 border-b border-white/10 flex items-center gap-3 w-64">
                    <div class="p-2.5 bg-gold-500 rounded-xl text-navy-900 shadow-md">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                            <path d="M12 12v3M9 13.5h6"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-black text-white text-lg tracking-tight">
                            Gudang <span class="text-gold-400">Gadget</span>
                        </h1>
                        <span class="text-xs text-slate-400 font-medium">Panel Admin</span>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav id="sidebarNav" class="flex-1 p-4 space-y-2 w-64 overflow-y-auto">
                    {{-- Link: Dashboard --}}
                    <a href="{{ route('landing') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all
                              {{ $routeName === 'landing'
                                  ? 'bg-gold-500 text-white shadow-lg shadow-gold-500/30'
                                  : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10"/>
                        </svg>
                        <span class="truncate">Dashboard</span>
                    </a>

                    {{-- Link: Layar Kasir (POS) --}}
                    <a href="{{ route('pos.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-sm transition-all bg-gradient-to-r from-gold-500 to-gold-600 text-navy-900 shadow-lg shadow-gold-500/30
                              {{ str_starts_with($routeName, 'pos.') ? 'ring-2 ring-white/60' : 'hover:from-gold-400 hover:to-gold-500' }}">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
                        </svg>
                        <span class="truncate">Layar Kasir</span>
                    </a>

                    {{-- Link: Servis & Reparasi --}}
                    <a href="{{ route('servis.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all
                              {{ str_starts_with($routeName, 'servis.')
                                  ? 'bg-gold-500 text-white shadow-lg shadow-gold-500/30'
                                  : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                        </svg>
                        <span class="truncate">Servis &amp; Reparasi</span>
                    </a>

                    {{-- Dropdown: Manajemen Produk --}}
                    @php
                        $produkActive = in_array($routeName, [
                            'gadget.index', 'gadget.create', 'gadget.import',
                            'gadget.archive', 'gadget.show', 'gadget.edit', 'gadget.barcode', 'gadget.kartu-stok',
                        ]);
                    @endphp
                    <div class="space-y-1">
                        <button type="button" onclick="toggleDropdown('produk')"
                                class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all
                                       {{ $produkActive
                                           ? 'bg-gold-500 text-white shadow-lg shadow-gold-500/30'
                                           : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 8l-9-5-9 5v8l9 5 9-5V8zM3 8l9 5 9-5M12 13v8"/>
                                </svg>
                                <span class="truncate">Manajemen Produk</span>
                            </div>
                            <svg id="chevron-produk" class="w-4 h-4 shrink-0 transition-transform duration-200 {{ $produkActive ? 'text-white' : 'text-slate-400' }}"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>

                        <div id="submenu-produk" class="pl-6 space-y-1 pt-1 hidden">
                            <a href="{{ route('gadget.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      {{ in_array($routeName, ['gadget.index', 'gadget.show', 'gadget.edit', 'gadget.barcode', 'gadget.kartu-stok'])
                                          ? 'bg-gold-500 text-white shadow-md'
                                          : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 6h16v12H4zM4 10h16M4 14h16M8 6v12"/>
                                </svg>
                                Semua Produk
                            </a>
                            <a href="{{ route('gadget.create') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      {{ $routeName === 'gadget.create'
                                          ? 'bg-gold-500 text-white shadow-md'
                                          : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 5v14M5 12h14"/>
                                </svg>
                                Tambah Produk
                            </a>
                            <a href="{{ route('gadget.import') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      {{ $routeName === 'gadget.import'
                                          ? 'bg-gold-500 text-white shadow-md'
                                          : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 3h-5M21 3v5M21 3l-8 8M10 4H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h13a2 2 0 0 0 2-2v-5"/>
                                </svg>
                                Import CSV
                            </a>
                            @if (Auth::user()->isAdmin())
                                <a href="{{ route('gadget.archive') }}"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                          {{ $routeName === 'gadget.archive'
                                              ? 'bg-gold-500 text-white shadow-md'
                                              : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 7L4 7M10 11h4M6 21V7l2-4h8l2 4v14M6 21h12"/>
                                    </svg>
                                    Arsip Produk
                                </a>
                            @endif
                        </div>
                    </div>

                {{-- Dropdown: Transaksi --}}
                    @php
                        $transaksiActive = str_starts_with($routeName, 'penjualan.') || str_starts_with($routeName, 'pembelian.') || str_starts_with($routeName, 'shift.') || str_starts_with($routeName, 'order.');
                    @endphp
                    <div class="space-y-1">
                        <button type="button" onclick="toggleDropdown('transaksi')"
                                class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all
                                       {{ $transaksiActive
                                           ? 'bg-gold-500 text-white shadow-lg shadow-gold-500/30'
                                           : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 1v22M17 5.5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                                <span class="truncate">Transaksi</span>
                            </div>
                            <svg id="chevron-transaksi" class="w-4 h-4 shrink-0 transition-transform duration-200 {{ $transaksiActive ? 'text-white' : 'text-slate-400' }}"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                        <div id="submenu-transaksi" class="pl-6 space-y-1 pt-1 hidden">
                            <a href="{{ route('penjualan.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      {{ str_starts_with($routeName, 'penjualan.')
                                          ? 'bg-gold-500 text-white shadow-md'
                                          : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                                Penjualan
                            </a>
                            <a href="{{ route('pembelian.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      {{ str_starts_with($routeName, 'pembelian.')
                                          ? 'bg-gold-500 text-white shadow-md'
                                          : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14M12 5l-7 7 7 7"/>
                                </svg>
                                Pembelian
                            </a>
                            <a href="{{ route('order.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      {{ str_starts_with($routeName, 'order.')
                                          ? 'bg-gold-500 text-white shadow-md'
                                          : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                </svg>
                                Order Publik
                            </a>
                            <a href="{{ route('shift.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      {{ str_starts_with($routeName, 'shift.')
                                          ? 'bg-gold-500 text-white shadow-md'
                                          : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3l8 4.5v5C20 18 16.5 21 12 22.5 7.5 21 4 18 4 12.5v-5L12 3z"/>
                                </svg>
                                Shift Kasir
                            </a>
                        </div>
                    </div>

                {{-- Link: Mutasi Stok --}}
                    <a href="{{ route('mutasi.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all
                              {{ str_starts_with($routeName, 'mutasi.')
                                  ? 'bg-gold-500 text-white shadow-lg shadow-gold-500/30'
                                  : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                            <circle cx="12" cy="12" r="2"/>
                            <path d="M12 2v3m0 14v3M2 12h3m14 0h3"/>
                        </svg>
                        <span class="truncate">Mutasi Stok</span>
                    </a>

                {{-- Dropdown: Laporan --}}
                    @php
                        $laporanActive = str_starts_with($routeName, 'laporan.');
                    @endphp
                    <div class="space-y-1">
                        <button type="button" onclick="toggleDropdown('laporan')"
                                class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all
                                       {{ $laporanActive
                                           ? 'bg-gold-500 text-white shadow-lg shadow-gold-500/30'
                                           : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 3H3v18h18V3zM7 17V9m5 8V5m5 12v-6"/>
                                </svg>
                                <span class="truncate">Laporan</span>
                            </div>
                            <svg id="chevron-laporan" class="w-4 h-4 shrink-0 transition-transform duration-200 {{ $laporanActive ? 'text-white' : 'text-slate-400' }}"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                        <div id="submenu-laporan" class="pl-6 space-y-1 pt-1 hidden">
                            <a href="{{ route('laporan.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      {{ in_array($routeName, ['laporan.index']) ? 'bg-gold-500 text-white shadow-md' : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
                                </svg>
                                Laporan Umum
                            </a>
                            <a href="{{ route('laporan.laba-rugi') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      {{ in_array($routeName, ['laporan.laba-rugi']) ? 'bg-gold-500 text-white shadow-md' : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3l8 4.5v5C20 18 16.5 21 12 22.5 7.5 21 4 18 4 12.5v-5L12 3z"/><path d="M9 12l2 2 4-5"/>
                                </svg>
                                Laba Rugi
                            </a>
                            <a href="{{ route('laporan.per-bulan', date('Y')) }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      {{ in_array($routeName, ['laporan.per-bulan']) ? 'bg-gold-500 text-white shadow-md' : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M8 2v4M16 2v4M3 9h18"/>
                                </svg>
                                Per Bulan
                            </a>
                        </div>
                    </div>

                {{-- Dropdown: Toko Online (Storefront) --}}
                    @php
                        $storeActive = str_starts_with($routeName, 'store.') || str_starts_with($routeName, 'shop.');
                    @endphp
                    <div class="space-y-1">
                        <button type="button" onclick="toggleDropdown('store')"
                                class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all
                                       {{ $storeActive
                                           ? 'bg-gold-500 text-white shadow-lg shadow-gold-500/30'
                                           : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l1.5-5h15L21 9v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9zM3 9h18M9 21v-6h6v6"/>
                                </svg>
                                <span class="truncate">Toko Online</span>
                            </div>
                            <svg id="chevron-store" class="w-4 h-4 shrink-0 transition-transform duration-200 {{ $storeActive ? 'text-white' : 'text-slate-400' }}"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                        <div id="submenu-store" class="pl-6 space-y-1 pt-1 hidden">
                            <a href="{{ route('shop.home') }}" target="_blank"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-white/10 text-slate-400 hover:text-white">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/>
                                </svg>
                                Lihat Toko Publik ↗
                            </a>
                            <a href="{{ route('shop.intake') }}" target="_blank"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-white/10 text-slate-400 hover:text-white">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                                </svg>
                                Ajukan Servis Publik ↗
                            </a>
                            <a href="{{ route('store.banner.index') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      {{ $routeName === 'store.banner.index' || $routeName === 'store.banner.create'
                                          ? 'bg-gold-500 text-white shadow-md'
                                          : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
                                </svg>
                                Banner Promo
                            </a>
                            <a href="{{ route('store.settings') }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                      {{ $routeName === 'store.settings'
                                          ? 'bg-gold-500 text-white shadow-md'
                                          : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 6h16M4 12h16M4 18h16" transform="rotate(90 12 12)"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                Pengaturan Toko
                            </a>
                        </div>
                    </div>

                {{-- Link: Manajemen Pengguna --}}
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('user.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all
                                  {{ str_starts_with($routeName, 'user.')
                                      ? 'bg-gold-500 text-white shadow-lg shadow-gold-500/30'
                                      : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            Manajemen Pengguna
                        </a>
                        <a href="{{ route('mitra.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all
                                  {{ str_starts_with($routeName, 'mitra.')
                                      ? 'bg-gold-500 text-white shadow-lg shadow-gold-500/30'
                                      : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6M9 10h.01M15 10h.01"/>
                            </svg>
                            Mitra Reseller
                        </a>
                    @endif

                {{-- Dropdown: Master Data (admin) --}}
                    @if (Auth::user()->isAdmin())
                        @php
                            $masterActive = str_starts_with($routeName, 'master.');
                        @endphp
                        <div class="space-y-1">
                            <button type="button" onclick="toggleDropdown('master')"
                                    class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all
                                           {{ $masterActive
                                               ? 'bg-gold-500 text-white shadow-lg shadow-gold-500/30'
                                               : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 3l8 4v5c0 4.5-3.2 7.6-8 9-4.8-1.4-8-4.5-8-9V7l8-4zM12 3v18"/>
                                    </svg>
                                    <span class="truncate">Master Data</span>
                                </div>
                                <svg id="chevron-master" class="w-4 h-4 shrink-0 transition-transform duration-200 {{ $masterActive ? 'text-white' : 'text-slate-400' }}"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </button>
                            <div id="submenu-master" class="pl-6 space-y-1 pt-1 hidden">
                                <a href="{{ route('master.kategori') }}"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                          {{ $routeName === 'master.kategori'
                                              ? 'bg-gold-500 text-white shadow-md'
                                              : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 6h16v12H4zM4 10h16M4 14h16M8 6v12"/>
                                    </svg>
                                    Kategori
                                </a>
                                <a href="{{ route('master.supplier') }}"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all
                                          {{ $routeName === 'master.supplier'
                                              ? 'bg-gold-500 text-white shadow-md'
                                              : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 6l-10 7L2 6M4 6h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z"/>
                                    </svg>
                                    Supplier
                                </a>
                            </div>
                        </div>
                    @endif

                {{-- Link: Audit Log (admin) --}}
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('audit.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all
                                  {{ str_starts_with($routeName, 'audit.')
                                      ? 'bg-gold-500 text-white shadow-lg shadow-gold-500/30'
                                      : 'hover:bg-white/10 text-slate-400 hover:text-white' }}">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <path d="M7 12h2l1-4 3 8 1-4h3"/>
                            </svg>
                            Audit Log
                        </a>
                    @endif
                </nav>

                {{-- Footer --}}
                <div class="p-4 border-t border-white/10 w-64">
                    <a href="{{ route('landing') }}"
                       class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-white/10 hover:bg-white/20 text-slate-300 rounded-xl text-sm font-semibold transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10"/>
                        </svg>
                        Buka Halaman Dashboard
                    </a>
                    <a href="{{ route('profil.show') }}"
                       class="mt-3 w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-white/10 hover:bg-white/20 text-slate-300 rounded-xl text-sm font-semibold transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/>
                        </svg>
                        Profil & Password
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-transparent hover:bg-white/10 text-slate-400 hover:text-rose-300 rounded-xl text-sm font-semibold transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                            </svg>
                            Keluar Akun
                        </button>
                    </form>
                </div>
            </aside>

            {{-- MAIN CONTENT WRAPPER --}}
            <div class="flex-1 flex flex-col min-w-0">

                {{-- TOPBAR --}}
                <header class="bg-white h-20 border-b border-navy-100 px-4 sm:px-8 flex items-center justify-between shadow-sm print-hidden z-20">
                    <div class="flex items-center gap-3">
                        <button onclick="toggleSidebar()"
                                class="p-2 hover:bg-navy-50 rounded-lg text-navy-500 transition-colors"
                                aria-label="Buka/Tutup menu">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18M3 12h18M3 18h18"/>
                            </svg>
                        </button>
                        <h2 class="text-lg sm:text-xl font-black text-navy-800 tracking-tight">@yield('title', 'Dashboard')</h2>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex items-center gap-3 pl-4 border-l border-navy-100">
                            <div class="w-10 h-10 rounded-full bg-gold-500/20 text-gold-600 font-bold flex items-center justify-center text-sm">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="text-left">
                                <h4 class="text-sm font-bold text-navy-800">{{ Auth::user()->name ?? 'Admin' }}</h4>
                                <span class="text-xs text-gold-600 font-semibold">{{ Auth::user()->email ?? '' }}</span>
                            </div>
                        </div>

                        <button onclick="event.stopPropagation(); document.getElementById('logoutForm').submit()"
                                title="Keluar Akun"
                                class="p-2.5 bg-gold-500/10 hover:bg-gold-500/20 text-gold-600 rounded-xl transition-colors cursor-pointer">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                            </svg>
                        </button>
                        <form id="logoutForm" method="POST" action="{{ route('logout') }}" class="hidden">
                            @csrf
                        </form>
                    </div>
                </header>

                {{-- PAGE CONTENT --}}
                <main class="flex-1 p-4 sm:p-8 overflow-y-auto max-w-7xl w-full mx-auto">
                    @if (session('success'))
                        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('w-64');
                sidebar.classList.toggle('w-0');
            }

            function toggleDropdown(id) {
                const sub = document.getElementById('submenu-' + id);
                const chevron = document.getElementById('chevron-' + id);
                if (sub) {
                    sub.classList.toggle('hidden');
                    chevron.classList.toggle('rotate-180');
                }
            }

            @if (isset($produkActive) && $produkActive)
                toggleDropdown('produk');
            @endif
            @if (isset($storeActive) && $storeActive)
                toggleDropdown('store');
            @endif
        </script>

        @stack('scripts')
    </body>
</html>