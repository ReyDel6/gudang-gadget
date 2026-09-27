@extends('layouts.app')

@section('title', 'Tiket Servis Baru')

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-navy-800">Tiket Servis Baru</h1>
            <p class="text-sm text-navy-400">Surat tanda terima servis — isi identitas pelanggan, unit, dan keluhan.</p>
        </div>
        <a href="{{ route('servis.index') }}" class="text-sm font-semibold text-navy-500 hover:text-navy-800">← Kembali ke daftar</a>
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

    <form method="POST" action="{{ route('servis.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl border border-navy-100 p-6">
            <h2 class="text-sm font-black uppercase tracking-wide text-navy-800 mb-4">A. Identitas Pelanggan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Nama Lengkap *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Nomor WhatsApp *</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required placeholder="08xx-xxxx-xxxx"
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Alamat / Kota</label>
                    <input type="text" name="customer_address" value="{{ old('customer_address') }}"
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-navy-100 p-6">
            <h2 class="text-sm font-black uppercase tracking-wide text-navy-800 mb-4">B. Identitas Gadget</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Merk *</label>
                    <input type="text" name="device_brand" value="{{ old('device_brand') }}" required placeholder="Apple, Samsung, Xiaomi..."
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Model *</label>
                    <input type="text" name="device_model" value="{{ old('device_model') }}" required placeholder="iPhone 13 Pro 128GB Sierra Blue"
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">IMEI / Serial Number</label>
                    <input type="text" name="imei_or_serial" value="{{ old('imei_or_serial') }}"
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Pola / PIN / Passcode</label>
                    <input type="text" name="passcode" value="{{ old('passcode') }}" placeholder="Untuk kebutuhan testing teknisi"
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Warna / Akses Keamanan</label>
                    <input type="text" name="warna" value="{{ old('warna') }}" placeholder="Opsional — warna unit"
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Ditugaskan ke Teknisi</label>
                    <select name="technician_id" class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                        <option value="">— Belum ditugaskan —</option>
                        @foreach ($teknisi as $t)
                            <option value="{{ $t->id }}" @selected(old('technician_id') == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Kelengkapan Unit (Checklist bawaan)</label>
                    <input type="text" name="completeness" value="{{ old('completeness') }}" placeholder="Contoh: Unit Only, charger, SIM tray, dus..."
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Kondisi Fisik Awal &amp; Minus Bawaan</label>
                    <textarea name="initial_condition" rows="2" placeholder="Contoh: layar retak, lecet bodi, Face ID mati, pernah servis di tempat lain..."
                              class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">{{ old('initial_condition') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Keluhan &amp; Permintaan Servis *</label>
                    <textarea name="problem_description" rows="3" required placeholder="Jelaskan masalah dari pelanggan..."
                              class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">{{ old('problem_description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-navy-100 p-6">
            <h2 class="text-sm font-black uppercase tracking-wide text-navy-800 mb-4">C. Estimasi Biaya &amp; Uang Muka</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Uang Muka (DP)</label>
                    <input type="number" step="0.01" min="0" name="down_payment" value="{{ old('down_payment', 0) }}"
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Durasi Garansi (hari)</label>
                    <input type="number" min="0" max="365" name="warranty_days" value="{{ old('warranty_days', 30) }}"
                           class="w-full rounded-xl border border-navy-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-navy-800 hover:bg-navy-700 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition-colors">
                        Simpan &amp; Cetak
                    </button>
                </div>
            </div>
            <p class="text-xs text-navy-400 mt-3">Nomor tiket dibuat otomatis (SRV-YYYYMMDD-NNN). Biaya jasa &amp; suku cadang ditambahkan teknisi di halaman detail tiket.</p>
        </div>
    </form>

@endsection