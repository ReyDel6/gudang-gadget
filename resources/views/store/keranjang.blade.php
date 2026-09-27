@extends('layouts.store')

@section('title', 'Keranjang Belanja')

@section('meta_desc', 'Keranjang belanja ' . $settings['store_name'] . ' — kelola item, cek subtotal, lalu lanjut ke checkout.')

@section('content')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">
        <div class="flex items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-black text-navy-900">Keranjang Belanja</h1>
                <p class="text-sm text-navy-500 mt-1">
                    {{ $baris->isEmpty() ? 'Keranjang masih kosong.' : $baris->count() . ' produk siap checkout.' }}
                    @if ($resellerMode)
                        <span class="font-bold text-gold-600">· Harga sudah memakai tier grosir/partai sesuai jumlah</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('shop.katalog') }}" class="shrink-0 text-sm font-bold text-gold-600 hover:text-gold-700">← Lanjut belanja</a>
        </div>

        @if (session('sukses_keranjang'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">{{ session('sukses_keranjang') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($baris->isEmpty())
            <div class="bg-white rounded-2xl border border-navy-100 p-10 text-center">
                <p class="text-4xl mb-3">🛒</p>
                <p class="text-navy-900 font-bold">Belum ada produk di keranjang</p>
                <p class="text-sm text-navy-500 mt-1">Jelajahi katalog dan tambahkan produk favorit Anda.</p>
                <a href="{{ route('shop.katalog') }}"
                   class="inline-block mt-5 bg-navy-900 hover:bg-navy-800 text-white font-black px-6 py-3 rounded-xl transition-colors">
                    Lihat Katalog
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($baris as $item)
                    <div class="bg-white rounded-2xl border border-navy-100 p-4 flex items-center gap-4">
                        <a href="{{ route('shop.produk', $item['gadget']->id) }}" class="shrink-0 w-20 h-20 rounded-xl overflow-hidden bg-navy-50">
                            <img src="{{ $item['gadget']->foto_url }}" alt="{{ $item['gadget']->nama_produk }}" loading="lazy" class="w-full h-full object-cover">
                        </a>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('shop.produk', $item['gadget']->id) }}" class="font-bold text-navy-900 leading-snug line-clamp-2 hover:text-gold-600 transition-colors">{{ $item['gadget']->nama_produk }}</a>
                            <p class="text-xs text-navy-400 mt-0.5">{{ $item['gadget']->kondisi_label }} · {{ $item['gadget']->kategori }}</p>
                            @if (!$item['stok_cukup'])
                                <p class="text-xs font-bold text-rose-600 mt-1">Stok tidak cukup (sisa {{ $item['gadget']->stock }}). Kurangi jumlahnya.</p>
                            @endif
                            <p class="text-sm font-black text-gold-600 mt-1">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}<span class="text-[10px] font-semibold text-navy-400">/unit</span></p>
                        </div>
                        <form method="POST" action="{{ route('shop.keranjang.update') }}" class="flex items-center gap-2 shrink-0">
                            @csrf
                            <input type="hidden" name="gadget_id" value="{{ $item['gadget']->id }}">
                            <button type="submit" name="aksi" value="kurang"
                                    class="grid place-items-center w-8 h-8 rounded-lg border border-navy-200 text-navy-700 hover:border-gold-500 transition-colors" aria-label="Kurangi">−</button>
                            <input type="number" name="qty" value="{{ $item['qty'] }}" min="1" max="99"
                                   class="w-14 text-center rounded-lg border border-navy-200 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                            <button type="submit" name="aksi" value="tambah"
                                    class="grid place-items-center w-8 h-8 rounded-lg border border-navy-200 text-navy-700 hover:border-gold-500 transition-colors" aria-label="Tambah">+</button>
                        </form>
                        <form method="POST" action="{{ route('shop.keranjang.hapus') }}" class="shrink-0">
                            @csrf
                            <input type="hidden" name="gadget_id" value="{{ $item['gadget']->id }}">
                            <button type="submit" class="grid place-items-center w-9 h-9 rounded-xl text-navy-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" aria-label="Hapus item">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                            </button>
                        </form>
                        <div class="shrink-0 w-24 text-right">
                            <p class="text-base font-black text-navy-900">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 bg-white rounded-2xl border border-navy-100 p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-navy-500">Subtotal</p>
                    <p class="text-xl font-black text-navy-900">Rp {{ number_format($baris->sum('subtotal'), 0, ',', '.') }}</p>
                    <p class="text-[11px] text-navy-400 mt-0.5">Belum termasuk ongkos kirim (dikonfirmasi admin setelah pesanan diterima).</p>
                </div>
                <a href="{{ route('shop.checkout') }}"
                   class="shrink-0 inline-flex items-center gap-2 bg-gold-500 hover:bg-gold-600 text-navy-900 font-black px-6 py-4 rounded-xl transition-colors">
                    Lanjut ke Checkout
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>
        @endif
    </div>

@endsection