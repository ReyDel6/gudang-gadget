@extends('layouts.store')

@section('title', 'Portal Mitra')

@section('content')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gold-600">Portal Mitra</p>
                <h1 class="text-2xl font-black text-navy-900 mt-1">Halo, {{ $user->name }} 👋</h1>
            </div>
            <form method="POST" action="{{ route('shop.mitra.keluar') }}">
                @csrf
                <button type="submit"
                        class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    Keluar Portal
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Status verifikasi --}}
        <div class="rounded-2xl border p-5 mb-6 {{ $profil->isApproved() ? 'border-emerald-200 bg-emerald-50/50' : 'border-gold-300 bg-gold-500/10' }}">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-navy-500">Status Keanggotaan</p>
                    <p class="font-black text-navy-900 mt-0.5">
                        <span class="inline-block rounded-full px-3 py-1 text-xs font-black uppercase
                            {{ $profil->isApproved() ? 'bg-emerald-100 text-emerald-700' : 'bg-gold-100 text-gold-700' }}">
                            {{ $profil->status_label }}
                        </span>
                        <span class="text-sm font-semibold text-navy-600 ml-2">{{ $profil->store_name }}</span>
                    </p>
                </div>
                @if (!$profil->isApproved())
                    <p class="text-sm text-navy-600 max-w-sm">Akun {{ $profil->status_label }}. Harga khusus &amp; price list aktif setelah diverifikasi admin. Terima kasih telah mendaftar!</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('shop.mitra.price-list') }}"
                           class="bg-navy-900 hover:bg-navy-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
                            Lihat Price List
                        </a>
                        <a href="{{ route('shop.keranjang') }}"
                           class="bg-gold-500 hover:bg-gold-600 text-navy-900 text-sm font-black px-4 py-2.5 rounded-xl transition-colors relative">
                            🛒 Checkout
                            @if ($totalKeranjang > 0)
                                <span class="absolute -top-2 -right-2 min-w-5 h-5 px-1 grid place-items-center rounded-full bg-navy-900 text-gold-300 text-[10px] font-black">{{ $totalKeranjang }}</span>
                            @endif
                        </a>
                        <a href="{{ route('shop.mitra.price-list.csv') }}"
                           class="border border-navy-100 bg-white hover:border-gold-500 text-navy-700 text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
                            ⬇ CSV
                        </a>
                        <a href="{{ route('shop.mitra.price-list.cetak') }}" target="_blank"
                           class="border border-navy-100 bg-white hover:border-gold-500 text-navy-700 text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
                            🖨 Cetak / PDF
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-navy-100 p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-navy-400">Produk Ready Stock</p>
                <p class="text-3xl font-black text-navy-900 mt-1">{{ $produkReady }} <span class="text-sm font-semibold text-navy-400">produk</span></p>
            </div>
            <div class="bg-white rounded-2xl border border-navy-100 p-5">
                <p class="text-xs font-bold uppercase tracking-wide text-navy-400">Dengan Harga Grosir/Partai</p>
                <p class="text-3xl font-black text-gold-600 mt-1">{{ $produkTier }} <span class="text-sm font-semibold text-navy-400">produk</span></p>
            </div>
        </div>

        {{-- Kontak profil --}}
        <div class="bg-white rounded-2xl border border-navy-100 p-5">
            <p class="text-sm font-black text-navy-900 mb-3">📇 Data Kontak MITRA</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-navy-600">
                <div><span class="text-navy-400 font-semibold">Pemilik:</span> {{ $profil->owner_name }}</div>
                <div><span class="text-navy-400 font-semibold">Konter:</span> {{ $profil->store_name }}</div>
                <div><span class="text-navy-400 font-semibold">No. HP:</span> {{ $profil->phone }}</div>
                <div><span class="text-navy-400 font-semibold">WhatsApp:</span> {{ $profil->whatsapp ?: '—' }}</div>
                <div class="sm:col-span-2"><span class="text-navy-400 font-semibold">Alamat:</span> {{ $profil->address ?: '—' }}</div>
            </div>
        </div>

        @if ($profil->isApproved())
            <div class="mt-6 rounded-2xl border border-gold-200 bg-gold-500/10 p-5">
            <p class="text-sm font-black text-navy-900 mb-2">🛒 Checkout Online untuk Mitra</p>
            <p class="text-sm text-navy-500 leading-relaxed mb-4">
                Tambahkan produk dari price list ke keranjang — harga mitra otomatis berlaku. Checkout lalu admin
                konfirmasi ketersediaan &amp; ongkir sebelum barang dikirim. Untuk dropship, tulis alamat pengiriman
                pelanggan Anda di form checkout.
            </p>
            <a href="{{ route('shop.mitra.price-list') }}"
               class="inline-block bg-navy-900 hover:bg-navy-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
                Pesan via Price List →
            </a>
            <a href="{{ route('shop.keranjang') }}"
               class="inline-block bg-white border border-navy-200 hover:border-gold-500 text-navy-800 text-sm font-bold px-4 py-2.5 rounded-xl transition-colors ml-2">
                🛒 Keranjang ({{ $totalKeranjang }})
            </a>
        </div>

        <div class="mt-6 rounded-2xl border border-navy-100 bg-white p-5">
                <p class="text-sm font-black text-navy-900 mb-2">🚚 Layanan Dropship</p>
                <p class="text-sm text-navy-500 leading-relaxed">
                    Lewat checkout online, isi alamat pengiriman dengan alamat pelanggan Anda — pesanan tercatat sebagai
                    dropship dan dikirim langsung dengan <b>label resi tanpa nama toko kami</b>, sehingga pelanggan tetap
                    melihat identitas toko Anda.
                </p>
            </div>
        @endif
    </div>

@endsection