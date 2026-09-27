@php
    $produk = $produk ?? null;
    $tokoh = $produk->harga_promo_aktif;
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
                @if ($tokoh)
                    <p class="text-xs text-navy-400 line-through">Rp {{ number_format((float) $produk->harga_jual, 0, ',', '.') }}</p>
                @endif
                <p class="text-lg font-black {{ $tokoh ? 'text-rose-600' : 'text-gold-600' }}">Rp {{ number_format($produk->harga_aktif, 0, ',', '.') }}</p>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <a href="{{ \App\Support\WhatsApp::link($produk) }}" target="_blank" rel="noopener"
                   class="flex-1 flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-2.5 rounded-lg transition-colors">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.57 15.07L2 22l5.06-1.37A10 10 0 1 0 12 2zm5.5 14.1c-.23.65-1.34 1.24-1.86 1.29-.5.05-1.09.22-3.64-.76-3.06-1.17-5-4.27-5.16-4.47-.15-.2-1.24-1.65-1.24-3.14 0-1.5.79-2.24 1.07-2.54.28-.3.6-.38.8-.38h.58c.18 0 .44-.07.68.52.25.6.84 2.06.91 2.21.08.15.13.33.02.53-.1.2-.15.32-.3.5-.15.17-.32.38-.45.5-.15.13-.31.27-.13.54.17.27.78 1.28 1.67 2.08 1.15 1.02 2.12 1.34 2.42 1.49.3.15.47.13.65-.08.17-.2.75-.88.95-1.18.2-.3.4-.25.67-.15.28.1 1.76.83 2.06.98.3.15.5.23.58.35.07.13.07.73-.17 1.43z"/></svg>
                    Pesan
                </a>
                <a href="{{ route('shop.produk', $produk->id) }}"
                   class="flex-1 text-center border border-navy-100 hover:border-gold-500 text-navy-700 hover:text-gold-600 font-bold px-3 py-2.5 rounded-lg transition-colors">Detail</a>
            </div>
        </div>
    </div>
@endif