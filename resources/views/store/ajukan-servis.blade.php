@extends('layouts.store')

@section('title', 'Ajukan Servis')

@section('meta_desc', 'Ajukan servis & reparasi gadget Anda ke ' . $settings['store_name'] . ' — isi form ini, nanti dapat nomor tiket untuk memantau progres pengerjaan secara online.')

@section('content')

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">

        <div class="text-center mb-8">
            <div class="grid place-items-center w-14 h-14 mx-auto rounded-2xl bg-navy-900 text-gold-400 mb-4">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-navy-900">Ajukan Servis Gadget</h1>
            <p class="text-sm text-navy-500 mt-2 max-w-md mx-auto">
                Isi form ini untuk mendaftarkan perbaikan gadget Anda.
                Tim kami akan menghubungi Anda untuk konfirmasi, dan Anda mendapat <b>nomor tiket</b>
                untuk memantau progres pengerjaan secara online.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-700 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('shop.intake.store') }}" class="bg-white rounded-2xl border border-navy-100 p-6 shadow-sm">
            @csrf

            <h2 class="text-sm font-black uppercase tracking-wide text-navy-800 mb-4">Identitas Pelanggan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-navy-500 mb-1.5">Nama Lengkap *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-navy-500 mb-1.5">Nomor WhatsApp *</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required placeholder="08xx-xxxx-xxxx"
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wide text-navy-500 mb-1.5">Alamat / Kota</label>
                    <input type="text" name="customer_address" value="{{ old('customer_address') }}"
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
            </div>

            <h2 class="text-sm font-black uppercase tracking-wide text-navy-800 mb-4">Unit Gadget</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-navy-500 mb-1.5">Merk *</label>
                    <input type="text" name="device_brand" value="{{ old('device_brand') }}" required placeholder="Apple, Samsung, Xiaomi..."
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-navy-500 mb-1.5">Model *</label>
                    <input type="text" name="device_model" value="{{ old('device_model') }}" required placeholder="Contoh: iPhone 13 Pro 128GB"
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-navy-500 mb-1.5">IMEI / Serial Number</label>
                    <input type="text" name="imei_or_serial" value="{{ old('imei_or_serial') }}"
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-navy-500 mb-1.5">Kelengkapan Unit</label>
                    <input type="text" name="completeness" value="{{ old('completeness') }}" placeholder="Unit only, charger, dus..."
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wide text-navy-500 mb-1.5">Kondisi Fisik Awal / Minus</label>
                    <textarea name="initial_condition" rows="2" placeholder="Contoh: layar retak, pernah servis di tempat lain..."
                              class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">{{ old('initial_condition') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wide text-navy-500 mb-1.5">Keluhan &amp; Permintaan Servis *</label>
                    <textarea name="problem_description" rows="3" required placeholder="Jelaskan masalah pada gadget Anda..."
                              class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">{{ old('problem_description') }}</textarea>
                </div>
            </div>

            <div class="rounded-xl bg-navy-50 px-4 py-3 text-xs text-navy-500 mb-5">
                Estimasi biaya akan dikonfirmasi setelah pengecekan oleh teknisi. Anda tidak membayar apa pun saat mengajukan.
                Garansi servis berlaku sesuai jenis perbaikan (umumnya 30 hari).
            </div>

            <button type="submit" class="w-full bg-gold-500 hover:bg-gold-600 text-navy-900 font-black py-3.5 rounded-xl text-sm transition-colors">
                Kirim Permohonan Servis
            </button>

            <p class="text-center text-xs mt-4 text-navy-400">
                Sudah punya nomor tiket? <a href="{{ route('shop.tracking') }}" class="font-bold text-gold-600 hover:text-gold-700">Cek status servis di sini →</a>
            </p>
        </form>
    </div>

@endsection