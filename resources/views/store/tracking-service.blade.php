@extends('layouts.store')

@section('title', 'Tracking Service')

@section('meta_desc', 'Pantau status servis perbaikan gadget Anda di ' . $settings['store_name'] . ' — cukup masukkan nomor tiket (SRV-....) atau nomor HP yang terdaftar.')

@section('content')

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">

        <div class="text-center mb-8">
            <div class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-navy-900 text-gold-400 mb-4">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-navy-900">Tracking Service</h1>
            <p class="text-sm text-navy-500 mt-2 max-w-md mx-auto">
                Periksa status perbaikan gadget Anda. Masukkan <b>nomor tiket</b> atau <b>nomor HP</b> yang tertera pada slip servis.
            </p>
        </div>

        <form method="GET" action="{{ route('shop.tracking') }}" class="bg-white rounded-2xl border border-navy-100 p-5 mb-8 shadow-sm">
            <label for="q" class="block text-xs font-black uppercase tracking-wide text-navy-500 mb-2">Nomor Tiket / No. HP</label>
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="text" id="q" name="q" value="{{ $q }}"
                       placeholder="Contoh: SRV-20260927-001 atau 081234567890"
                       class="flex-1 rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                <button type="submit" class="bg-gold-500 hover:bg-gold-600 text-navy-900 font-black px-8 py-3 rounded-xl text-sm transition-colors">
                    Lacak Sekarang
                </button>
            </div>
        </form>

        @if ($error)
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-700 text-sm text-center">
                ⚠️ {{ $error }}
            </div>
        @endif

        @if ($result)
            @php $t = $result; @endphp
            <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden shadow-sm mb-8">

                <div class="p-5 md:p-6 border-b border-navy-100">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-black uppercase tracking-wide text-navy-400">Nomor Tiket</p>
                            <h2 class="text-xl font-black text-navy-900">{{ $t->no_tiket }}</h2>
                        </div>
                        <span class="inline-flex text-xs font-black px-3 py-1.5 rounded-full {{ $t->status_warna }}">
                            {{ $t->status_label }}
                        </span>
                    </div>
                    @if ($t->is_garansi)
                        <p class="text-[11px] font-black text-gold-600 mt-2">KLAIM GARANSI — tiket dari servis {{ $t->parent?->no_tiket }}</p>
                    @endif
                </div>

                <div class="p-5 md:p-6 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                    <div>
                        <p class="text-xs text-navy-400">Pelanggan</p>
                        <p class="font-bold text-navy-900">{{ $t->customer_name }} <span class="text-xs font-normal text-navy-400">({{ $t->customer_phone }})</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-navy-400">Unit</p>
                        <p class="font-bold text-navy-900">{{ $t->device_brand }} {{ $t->device_model }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-navy-400">Keluhan</p>
                        <p class="text-navy-700 mt-0.5">{{ $t->problem_description }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-navy-400">Tanggal Masuk</p>
                        <p class="font-semibold text-navy-800">{{ $t->received_at?->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-navy-400">Teknisi</p>
                        <p class="font-semibold text-navy-800">{{ $t->technician?->name ?? 'Belum ditugaskan' }}</p>
                    </div>
                    @if ($t->warranty_until)
                        <div class="sm:col-span-2">
                            <p class="text-xs text-navy-400">Garansi Berlaku Sampai</p>
                            <p class="font-bold text-emerald-700">{{ $t->warranty_until->format('d M Y') }}</p>
                        </div>
                    @endif
                </div>

                @if ($t->logs->isNotEmpty())
                    <div class="p-5 md:p-6 border-t border-navy-100">
                        <p class="text-sm font-black uppercase tracking-wide text-navy-800 mb-4">Perkembangan Servis</p>
                        <div class="space-y-4">
                            @foreach ($t->logs as $log)
                                <div class="relative pl-8">
                                    <span class="absolute left-0 top-1 w-3.5 h-3.5 rounded-full border-2 border-gold-500 bg-white
                                                 {{ $loop->first ? '!bg-gold-500' : '' }}"></span>
                                    <p class="text-sm font-bold text-navy-900">{{ $log->status_label }}</p>
                                    <p class="text-xs text-navy-400">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                                    @if ($log->notes)
                                        <p class="text-sm text-navy-600 mt-1">{{ $log->notes }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($t->status === 'ready')
                    <div class="px-5 md:px-6 pb-6">
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            ✅ Unit Anda sudah <b>selesai diperbaiki</b> dan siap diambil. Silakan datang ke
                            {{ $settings['store_name'] }} untuk pembayaran sisa tagihan &amp; penyerahan unit.
                        </div>
                    </div>
                @endif
            </div>

            <p class="text-center text-xs text-navy-400 mb-8">
                Butuh bantuan? Hubungi kami di
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp_number']) }}" target="_blank" class="text-emerald-600 font-bold">
                    WhatsApp {{ $settings['whatsapp_number'] }}
                </a>
            </p>
        @endif

        <div class="text-center">
            <a href="{{ route('shop.home') }}" class="text-sm font-bold text-gold-600 hover:text-gold-700">← Kembali ke beranda toko</a>
        </div>
    </div>

@endsection