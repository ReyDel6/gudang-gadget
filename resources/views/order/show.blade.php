@extends('layouts.app')

@section('title', 'Order ' . $order->kode)

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('order.index') }}" class="text-navy-500 hover:text-navy-700">← Kembali</a>
            <h1 class="text-xl font-bold text-navy-800">Order {{ $order->kode }}</h1>
        </div>
        @php
            $warna = match ($order->status) {
                'pending' => 'bg-amber-100 text-amber-700',
                'confirmed' => 'bg-emerald-100 text-emerald-700',
                default => 'bg-rose-100 text-rose-600',
            };
        @endphp
        <span class="inline-flex text-xs font-black px-3 py-1.5 rounded-full {{ $warna }}">{{ $order->status_label }}</span>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm">{{ session('error') }}</div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-navy-100 p-5">
                <p class="text-sm font-black text-navy-900 mb-4">📦 Item Pesanan</p>
                <div class="space-y-3">
                    @foreach ($order->items as $item)
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <div class="min-w-0">
                                <p class="font-semibold text-navy-800">{{ $item->nama_produk }}</p>
                                <p class="text-xs text-navy-400">
                                    {{ $item->qty }} × Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                    <a href="{{ route('gadget.show', $item->gadget_id) }}" class="text-gold-600 hover:text-gold-700">· lihat produk</a>
                                </p>
                            </div>
                            <p class="font-bold text-navy-900 shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-navy-100 space-y-1.5 text-sm">
                    <div class="flex justify-between"><span class="text-navy-500">Subtotal</span><span class="font-bold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between">
                        <span class="text-navy-500">Ongkos kirim</span>
                        <span class="font-bold">{{ $order->ongkos_kirim === null ? '—' : 'Rp ' . number_format($order->ongkos_kirim, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-base"><span class="font-semibold text-navy-700">Total</span>
                        <span class="font-black text-gold-600">Rp {{ number_format($order->total ?? $order->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if ($order->status === 'pending')
                <div class="bg-white rounded-2xl border border-gold-200 p-5">
                    <p class="text-sm font-black text-navy-900 mb-4">✅ Konfirmasi Order → Buat Penjualan</p>
                    <p class="text-xs text-navy-500 mb-4">
                        Konfirmasi menciptakan <b>penjualan</b> (stok terpotong otomatis, tercatat di laporan).
                        Verifikasi pembayaran transfer terlebih dahulu bila metode pembayarannya Transfer.
                        @if ($order->payment_method === 'transfer' && !$order->bukti_path)
                            <span class="text-amber-600 font-bold">Belum ada bukti transfer diunggah.</span>
                        @endif
                    </p>
                    <form method="POST" action="{{ route('order.konfirmasi', $order->id) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @csrf
                        <div>
                            <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Ongkos Kirim</label>
                            <input type="number" name="ongkos_kirim" value="{{ old('ongkos_kirim', 0) }}" min="0" step="1000"
                                   class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Kurir</label>
                            <input type="text" name="kurir" value="{{ old('kurir') }}" placeholder="JNE / J&T / GoSend..."
                                   class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Catatan Admin (opsional)</label>
                            <input type="text" name="catatan_admin" value="{{ old('catatan_admin') }}" maxlength="500"
                                   class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        </div>
                        <div class="sm:col-span-2 pt-2 flex items-center gap-3">
                            <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-xl transition-colors text-sm">
                                Konfirmasi &amp; Buat Penjualan
                            </button>
                            <button type="submit" form="formBatal"
                                    onclick="return confirm('Batalkan order ini?')"
                                    class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-6 py-3 rounded-xl transition-colors text-sm">
                                Batalkan Order
                            </button>
                        </div>
                    </form>
                    <form id="formBatal" method="POST" action="{{ route('order.batal', $order->id) }}" class="hidden">@csrf</form>
                </div>
            @elseif ($order->status === 'confirmed')
                <div class="bg-white rounded-2xl border border-emerald-200 p-5">
                    <p class="text-sm font-black text-navy-900 mb-2">✅ Telah dikonfirmasi</p>
                    <div class="text-sm text-navy-600 space-y-1">
                        <p>Ongkos kirim: <b>Rp {{ number_format($order->ongkos_kirim ?? 0, 0, ',', '.') }}</b>{{ $order->kurir ? ' · ' . $order->kurir : '' }}</p>
                        @if ($order->penjualan)
                            <p>
                                Penjualan: <a href="{{ route('penjualan.show', $order->penjualan_id) }}" class="font-bold text-gold-600 hover:text-gold-700">{{ $order->penjualan->no_invoice }}</a>
                                <a href="{{ route('penjualan.cetak', $order->penjualan_id) }}" class="text-xs text-navy-500 ml-2">cetak struk</a>
                            </p>
                        @endif
                        <p>Dikonfirmasi: {{ $order->confirmed_at?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-rose-200 p-5">
                    <p class="text-sm font-black text-navy-900">🚫 Order dibatalkan</p>
                    <p class="text-sm text-navy-600 mt-1">Dibatalkan: {{ $order->cancelled_at?->format('d/m/Y H:i') }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-navy-100 p-5">
                <p class="text-sm font-black text-navy-900 mb-3">🧾 Data Order</p>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-navy-400">Dibuat</dt><dd class="font-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-navy-400">Metode</dt><dd class="font-semibold">{{ $order->payment_label }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-navy-400">Pembeli</dt><dd class="font-semibold text-right">{{ $order->reseller ? $order->reseller->name . ' (Mitra)' : 'Publik / Retail' }}</dd></div>
                </dl>
            </div>

            <div class="bg-white rounded-2xl border border-navy-100 p-5">
                <p class="text-sm font-black text-navy-900 mb-3">👤 Penerima</p>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-navy-400">Nama</dt><dd class="font-semibold text-right">{{ $order->nama_pelanggan }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-navy-400">Telepon</dt><dd class="font-semibold text-right">{{ $order->telepon }}</dd></div>
                </dl>
                @if ($order->alamat)
                    <p class="text-xs text-navy-500 mt-3">Alamat: {{ $order->alamat }}</p>
                @endif
                @if ($order->catatan)
                    <p class="text-xs text-navy-500 mt-2">Catatan: {{ $order->catatan }}</p>
                @endif
                @if ($order->catatan_admin)
                    <p class="text-xs text-navy-500 mt-2">Catatan admin: {{ $order->catatan_admin }}</p>
                @endif
            </div>

            @if ($order->bukti_path)
                <div class="bg-white rounded-2xl border border-navy-100 p-5">
                    <p class="text-sm font-black text-navy-900 mb-3">🧾 Bukti Transfer</p>
                    <a href="{{ asset('storage/' . $order->bukti_path) }}" target="_blank" rel="noopener"
                       class="block rounded-xl overflow-hidden border border-navy-100">
                        <img src="{{ asset('storage/' . $order->bukti_path) }}" alt="Bukti transfer {{ $order->kode }}" class="w-full h-auto" loading="lazy">
                    </a>
                </div>
            @endif
        </div>
    </div>

@endsection