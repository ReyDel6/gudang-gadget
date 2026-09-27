@php
    $produk = $produk ?? null;
    $tokoh = $produk->harga_promo_aktif;
    $resellerMode = $resellerMode ?? false;
    $hargaTampil = $resellerMode ? $produk->harga_mitra : $produk->harga_aktif;
@endphp
@if ($produk)
    <div class="group bg-white rounded-2xl border border-navy-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all">
        <a href="{{ route('shop.produk', $produk->id) }}" class="block relative aspect-square bg-navy-50">
            <img src="{{ $produk->foto_url }}" alt="{{ $produk->nama_produk }}" loading="lazy" class="w-full h-full object-cover">
            <span class="absolute top-3 left-3 text-[10px] font-bold bg-navy-900/80 text-white px-2 py-1 rounded-full">{{ $produk->kondisi_label }}</span>
            <span class="absolute top-3 right-3 flex flex-col items-end gap-1.5">
                @if (!$produk->tersedia)
                    <span class="text-[10px] font-bold bg-rose-600 text-white px-2 py-1 rounded-full">Stok Habis</span>
                @endif
                @if ($tokoh)
                    <span class="text-[10px] font-black bg-gold-500 text-navy-900 px-2 py-1 rounded-full">-{{ $produk->diskon_persen }}%</span>
                @endif
            </span>
        </a>
        <div class="p-4">
            <p class="text-xs text-navy-400 font-medium truncate" title="{{ $produk->kategori ?? '' }}">{{ $produk->kategori ?? '' }}</p>
            <a href="{{ route('shop.produk', $produk->id) }}" class="block font-bold text-navy-900 mt-0.5 leading-snug line-clamp-2 group-hover:text-gold-600 transition-colors min-h-[2.6rem]">{{ $produk->nama_produk }}</a>
            @if ($produk->tierPrices->isNotEmpty())
                <span class="inline-flex items-center gap-1 mt-1.5 text-[9px] font-black bg-navy-800 text-gold-300 px-2 py-1 rounded-full uppercase tracking-wide">Tersedia Harga Grosir / Partai</span>
            @endif
            <div class="mt-2">
                @if ($tokoh && !$resellerMode)
                    <p class="text-xs text-navy-400 line-through">Rp {{ number_format((float) $produk->harga_jual, 0, ',', '.') }}</p>
                @endif
                <p class="text-lg font-black {{ $tokoh && !$resellerMode ? 'text-rose-600' : 'text-gold-600' }}">Rp {{ number_format((float) $hargaTampil, 0, ',', '.') }}</p>
                @if ($resellerMode)
                    <p class="text-[10px] font-black uppercase tracking-wide text-navy-500 mt-0.5">Harga Mitra Reseller</p>
                @endif
            </div>
            <div class="mt-3 flex items-center gap-2">
                @if ($produk->tersedia)
                    <form method="POST" action="{{ route('shop.keranjang.tambah') }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="gadget_id" value="{{ $produk->id }}">
                        <input type="hidden" name="qty" value="1">
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-1.5 bg-gold-500 hover:bg-gold-600 text-navy-900 text-xs font-bold px-3 py-2.5 rounded-lg transition-colors">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                            Keranjang
                        </button>
                    </form>
                @else
                    <button disabled
                            class="flex-1 flex items-center justify-center gap-1.5 bg-navy-100 text-navy-400 text-xs font-bold px-3 py-2.5 rounded-lg cursor-not-allowed">
                        Stok Habis
                    </button>
                @endif
                <a href="{{ route('shop.produk', $produk->id) }}"
                   class="flex-1 text-center border border-navy-100 hover:border-gold-500 text-navy-700 hover:text-gold-600 font-bold px-3 py-2.5 rounded-lg transition-colors">Detail</a>
            </div>
        </div>
    </div>
@endif