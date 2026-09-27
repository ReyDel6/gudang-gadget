@extends('layouts.app')

@section('title', 'Pelunasan ' . $servis->no_tiket)

@section('content')

    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-bold text-navy-800">Pelunasan Kasir — {{ $servis->no_tiket }}</h1>
            <a href="{{ route('servis.show', $servis->id) }}" class="text-sm font-semibold text-navy-500 hover:text-navy-800">← Detail tiket</a>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-navy-100 p-6 mb-5">
            <h2 class="text-sm font-black uppercase tracking-wide text-navy-800 mb-4">Rincian Tagihan</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-navy-500">Pelanggan</dt><dd class="font-semibold text-navy-800">{{ $servis->customer_name }}</dd></div>
                <div class="flex justify-between"><dt class="text-navy-500">Unit</dt><dd class="font-semibold text-navy-800">{{ $servis->device_brand }} {{ $servis->device_model }}</dd></div>
                <div class="flex justify-between"><dt class="text-navy-500">Biaya Jasa</dt><dd class="font-semibold">Rp {{ number_format((float) $servis->service_fee, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-navy-500">Suku Cadang</dt><dd class="font-semibold">Rp {{ number_format((float) $servis->sparepart_fee, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-navy-500">Total</dt><dd class="font-black text-navy-900">Rp {{ number_format((float) $servis->total_cost, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-navy-500">Uang Muka (DP)</dt><dd class="font-semibold text-navy-700">- Rp {{ number_format((float) $servis->down_payment, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between border-t border-navy-100 pt-2 mt-2">
                    <dt class="font-black text-navy-900">SISA TAGIHAN</dt>
                    <dd class="font-black text-2xl text-gold-600">Rp {{ number_format((float) $servis->remaining_cost, 0, ',', '.') }}</dd>
                </div>
            </dl>
        </div>

        <form method="POST" action="{{ route('servis.lunas.store', $servis->id) }}" class="bg-white rounded-2xl border border-navy-100 p-6">
            @csrf
            <h2 class="text-sm font-black uppercase tracking-wide text-navy-800 mb-4">Metode Pembayaran</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
                @foreach ($metode as $kode => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="{{ $kode }}" class="peer sr-only" required
                               @checked($loop->first)>
                        <span class="block text-center border border-navy-200 rounded-xl px-3 py-3 text-sm font-bold text-navy-700 peer-checked:border-gold-500 peer-checked:bg-gold-500/10 peer-checked:text-navy-900">
                            {{ $label }}
                        </span>
                    </label>
                @endforeach
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Nominal Dibayar *</label>
                    <input type="number" step="0.01" min="0" name="paid_amount" required value="{{ old('paid_amount', $servis->remaining_cost) }}"
                           class="w-full rounded-xl border border-navy-200 px-4 py-3 text-lg font-black focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Referensi (No. Transaksi / QRIS)</label>
                    <input type="text" name="payment_ref" value="{{ old('payment_ref') }}"
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
            </div>

            <p class="text-xs text-navy-400 mt-4 mb-4">Setelah lunas: status tiket menjadi <b>Diambil / Lunas</b>, kartu garansi terbit ({{ $servis->warranty_days }} hari, s/d {{ now()->addDays((int) $servis->warranty_days)->format('d M Y') }}), dan stok suku cadang telah terpotong otomatis.</p>

            <button type="submit" class="w-full bg-gold-500 hover:bg-gold-600 text-navy-900 text-sm font-black py-3.5 rounded-xl">Proses Pelunasan</button>
        </form>
    </div>

@endsection