@extends('layouts.store')

@section('title', 'Daftar Mitra Reseller')

@section('meta_desc', 'Daftar menjadi mitra reseller ' . $settings['store_name'] . ' — akses harga grosir khusus, price list harian, dan dukungan dropship.')

@section('content')

    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-12">
        <div class="bg-white rounded-3xl border border-navy-100 shadow-sm p-6 md:p-8">
            <div class="text-center mb-6">
                <span class="inline-flex items-center gap-2 bg-gold-500/15 text-gold-700 text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-widest">
                    🏬 Portal B2B
                </span>
                <h1 class="text-2xl font-black text-navy-900 mt-3">Daftar Mitra Reseller</h1>
                <p class="text-sm text-navy-500 mt-1">Bergabung untuk harga khusus, price list harian, &amp; dukungan dropship.</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700 text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('shop.mitra.daftar.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Nama Pemilik *</label>
                        <input type="text" name="owner_name" value="{{ old('owner_name') }}" required maxlength="150"
                               class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Nama Konter / Toko *</label>
                        <input type="text" name="store_name" value="{{ old('store_name') }}" required maxlength="150"
                               class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Nama Akun Login *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="150"
                           class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Password *</label>
                        <input type="password" name="password" required minlength="8"
                               class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Ulangi Password *</label>
                    <input type="password" name="password_confirmation" required minlength="8"
                           class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">No. HP *</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required maxlength="30"
                               class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">No. WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp') ?: old('phone') }}" maxlength="30"
                               class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Alamat Toko *</label>
                    <textarea name="address" required rows="3" maxlength="500"
                              class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Foto KTP / Toko</label>
                    <input type="file" name="ktp_photo" accept="image/*"
                           class="mt-1.5 w-full text-sm text-navy-500 file:mr-4 file:rounded-lg file:border-0 file:bg-navy-900 file:px-4 file:py-2.5 file:text-xs file:font-bold file:text-white hover:file:bg-navy-800">
                    <p class="text-[11px] text-navy-400 mt-1">Opsional · JPG/PNG/WebP maks. 2MB · mempercepat verifikasi oleh admin.</p>
                </div>

                <button type="submit"
                        class="w-full bg-gold-500 hover:bg-gold-600 text-navy-900 font-black py-4 rounded-xl transition-colors text-base">
                    Daftar Sebagai Mitra
                </button>
            </form>

            <p class="text-center text-sm text-navy-500 mt-5">
                Sudah punya akun? <a href="{{ route('shop.mitra.masuk') }}" class="font-bold text-gold-600 hover:text-gold-700">Masuk di sini</a>
            </p>
        </div>
    </div>

@endsection