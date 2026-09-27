@extends('layouts.store')

@section('title', 'Status Pesanan ' . $order->kode)

@section('meta_desc', 'Pantau status pesanan ' . $order->kode . ' di ' . $settings['store_name'] . '.')

@section('content')

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">

        @if (session('sukses'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 text-sm">
                ✓ {{ session('sukses') }}
            </div>
        @endif

        <div class="text-center mb-8">
            <div class="text-5xl mb-3">
                @if ($order->status === 'cancelled')
                    🚫
                @elseif ($order->status === 'confirmed')
                    ✅
                @else
                    ⏳
                @endif
            </div>
            <h1 class="text-2xl font-black text-navy-900">Pesanan {{ $order->kode }}</h1>
            <p class="text-sm text-navy-500 mt-1">Status: <span class="font-bold text-gold-600">{{ $order->status_label }}</span></p>
            @if ($order->status === 'pending')
                <p class="text-sm text-navy-500 mt-2 max-w-md mx-auto">
                    Terima kasih! Admin akan segera memverifikasi pesanan Anda.
                    @if ($order->payment_method === 'transfer')
                        Jangan lupa unggah bukti transfer di bawah.
                    @endif
                </p>
            @elseif ($order->status === 'confirmed')
                <p class="text-sm text-navy-500 mt-2 max-w-md mx-auto">Pesanan Anda telah dikonfirmasi dan sedang diproses.</p>
            @else
                <p class="text-sm text-navy-500 mt-2 max-w-md mx-auto">Pesanan ini telah dibatalkan.</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-navy-100 p-5 md:p-6 mb-6">
            <p class="text-sm font-black text-navy-900 mb-4">📦 Ringkasan Pesanan</p>
            <div class="space-y-3">
                @foreach ($order->items as $item)
                    <div class="flex items-center justify-between gap-4 text-sm">
                        <div class="min-w-0">
                            <p class="font-semibold text-navy-800">{{ $item->nama_produk }}</p>
                            <p class="text-xs text-navy-400">{{ $item->qty }} × Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-bold text-navy-900 shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-navy-100 space-y-1.5 text-sm">
                <div class="flex justify-between"><span class="text-navy-500">Subtotal</span><span class="font-bold text-navy-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-navy-500">Ongkos kirim</span>
                    <span class="font-bold text-navy-900">{{ $order->ongkos_kirim === null ? '—' : 'Rp ' . number_format($order->ongkos_kirim, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-base"><span class="font-semibold text-navy-700">Total</span>
                    <span class="font-black text-gold-600">Rp {{ number_format($order->total ?? $order->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-navy-500 bg-navy-50 rounded-xl p-4">
                <div><b class="text-navy-700">Pembayaran:</b> {{ $order->payment_label }}</div>
                <div><b class="text-navy-700">Penerima:</b> {{ $order->nama_pelanggan }} ({{ $order->telepon }})</div>
                @if ($order->alamat)
                    <div class="sm:col-span-2"><b class="text-navy-700">Alamat:</b> {{ $order->alamat }}</div>
                @endif
                @if ($order->catatan)
                    <div class="sm:col-span-2"><b class="text-navy-700">Catatan:</b> {{ $order->catatan }}</div>
                @endif
            </div>
        </div>

        @if ($order->status === 'pending' && $order->payment_method === 'transfer')
            <div class="bg-white rounded-2xl border border-navy-100 p-5 md:p-6 mb-6">
                <p class="text-sm font-black text-navy-900 mb-2">🏦 Transfer Pembayaran</p>
                @if (!empty($settings['payment_transfer_info']))
                    <p class="text-sm text-navy-600 mb-3">Transfer ke: <b class="text-navy-900">{{ $settings['payment_transfer_info'] }}</b></p>
                @endif

                @if ($order->bukti_path)
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        ✓ Bukti transfer sudah diunggah. Admin akan memverifikasinya.
                    </div>
                @else
                    @if ($errors->any())
                        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    <form method="POST" action="{{ route('shop.order.bukti', $order->kode) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <label class="block text-xs font-bold text-navy-500 uppercase tracking-wide">Unggah Bukti Transfer</label>
                        <input type="file" name="bukti" accept="image/*" required
                               class="w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        <button type="submit"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-3.5 rounded-xl transition-colors text-sm">
                            Unggah Bukti
                        </button>
                    </form>
                @endif
            </div>
        @endif

        <div class="text-center">
            <a href="{{ route('shop.home') }}" class="text-sm font-bold text-gold-600 hover:text-gold-700">← Kembali ke beranda toko</a>
        </div>
    </div>

@endsection