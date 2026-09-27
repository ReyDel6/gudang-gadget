@extends('layouts.store')

@section('title', 'Checkout')

@section('meta_desc', 'Selesaikan pesanan di ' . $settings['store_name'] . ' — isi data pengiriman dan pilih metode pembayaran.')

@section('content')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">
        <h1 class="text-2xl font-black text-navy-900 mb-1">Checkout</h1>
        <p class="text-sm text-navy-500 mb-6">Lengkapi data berikut untuk memproses pesanan Anda.</p>

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($resellerMode)
            <div class="mb-5 rounded-xl border border-gold-200 bg-gold-50 px-4 py-3 text-sm text-gold-800">
                ✓ Anda login sebagai <b>Mitra Reseller terverifikasi</b> — harga otomatis memakai tier grosir/partai sesuai jumlah.
            </div>
        @endif

        <form method="POST" action="{{ route('shop.checkout.store') }}" class="space-y-6">
            @csrf

            <div class="bg-white rounded-2xl border border-navy-100 p-5 md:p-6">
                <p class="text-sm font-black text-navy-900 mb-4">🛍 Ringkasan Pesanan</p>
                <div class="space-y-3">
                    @foreach ($baris as $item)
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <div class="min-w-0">
                                <a href="{{ route('shop.produk', $item['gadget']->id) }}" class="font-semibold text-navy-800 hover:text-gold-600 transition-colors">{{ $item['gadget']->nama_produk }}</a>
                                <p class="text-xs text-navy-400">{{ $item['qty'] }} × Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</p>
                            </div>
                            <p class="font-bold text-navy-900 shrink-0">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-navy-100 flex items-center justify-between">
                    <span class="font-semibold text-navy-700">Subtotal</span>
                    <span class="font-black text-navy-900 text-lg">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <p class="text-[11px] text-navy-400 mt-2">Ongkos kirim ditentukan admin saat mengonfirmasi pesanan (kecuali bayar di toko).</p>
            </div>

            <div class="bg-white rounded-2xl border border-navy-100 p-5 md:p-6 space-y-4">
                <p class="text-sm font-black text-navy-900">👤 Data Penerima</p>
                <div>
                    <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}" required
                           class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    @error('nama_pelanggan') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">No. WhatsApp / Telepon</label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}" required placeholder="08xxxxxxxxxx"
                           class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    @error('telepon') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Alamat Pengiriman</label>
                    <textarea name="alamat" rows="2" maxlength="500"
                              class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">{{ old('alamat') }}</textarea>
                    @error('alamat') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Catatan (opsional)</label>
                    <input type="text" name="catatan" value="{{ old('catatan') }}" maxlength="500" placeholder="Contoh: warna favorit, jam kirim, dll."
                           class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    @error('catatan') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-navy-100 p-5 md:p-6">
                <p class="text-sm font-black text-navy-900 mb-4">💳 Metode Pembayaran</p>
                <div class="grid gap-3">
                    @foreach ($metode as $m)
                        <label class="flex items-center gap-3 border-2 rounded-xl px-4 py-3.5 cursor-pointer transition-colors
                                      {{ old('payment_method', 'transfer') === $m ? 'border-gold-500 bg-gold-50' : 'border-navy-100 hover:border-gold-300' }}">
                            <input type="radio" name="payment_method" value="{{ $m }}" required
                                   {{ old('payment_method', 'transfer') === $m ? 'checked' : '' }}
                                   class="accent-gold-500">
                            <span class="flex-1 text-sm font-bold text-navy-800">{{ \App\Models\PublikOrder::METODE_PEMBAYARAN[$m] ?? $m }}</span>
                            @if ($m === 'transfer' && !empty($settings['payment_transfer_info']))
                                <span class="text-[11px] text-navy-400">{{ \Illuminate\Support\Str::limit($settings['payment_transfer_info'], 80) }}</span>
                            @endif
                        </label>
                    @endforeach
                    @if (count($metode) === 0)
                        <p class="text-sm text-rose-600 bg-rose-50 border border-rose-200 rounded-xl px-4 py-3">
                            Metode pembayaran sedang dinonaktifkan sementara. Silakan hubungi toko atau coba lagi nanti.
                        </p>
                    @endif
                </div>
                @if (in_array('transfer', $metode, true) && !empty($settings['payment_transfer_info']))
                    <p class="text-xs text-navy-500 mt-3 bg-navy-50 rounded-lg px-3 py-2.5">
                        <b class="text-navy-700">Rekening tujuan:</b> {{ $settings['payment_transfer_info'] }} — unggah bukti transfer di halaman status order setelah checkout.
                    </p>
                @endif
                @error('payment_method') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="w-full bg-navy-900 hover:bg-navy-800 text-white font-black py-4 rounded-xl transition-colors text-lg"
                    {{ count($metode) === 0 ? 'disabled' : '' }}>
                Buat Pesanan — Rp {{ number_format($subtotal, 0, ',', '.') }}
            </button>
            <p class="text-center text-xs text-navy-400">
                Dengan membuat pesanan, admin akan mengonfirmasi ketersediaan & ongkir sebelum barang dikirim.
            </p>
        </form>
    </div>

@endsection