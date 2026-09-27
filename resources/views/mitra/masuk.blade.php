@extends('layouts.store')

@section('title', 'Masuk Portal Mitra')

@section('meta_desc', 'Login portal mitra reseller ' . $settings['store_name'] . ' untuk akses harga khusus, price list harian, dan dukungan dropship.')

@section('content')

    <div class="max-w-md mx-auto px-4 sm:px-6 py-14">
        <div class="bg-white rounded-3xl border border-navy-100 shadow-sm p-6 md:p-8">
            <div class="text-center mb-6">
                <span class="inline-flex items-center gap-2 bg-gold-500/15 text-gold-700 text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-widest">
                    🔑 Portal B2B
                </span>
                <h1 class="text-2xl font-black text-navy-900 mt-3">Masuk Mitra Reseller</h1>
                <p class="text-sm text-navy-500 mt-1">Harga khusus aktif setelah akun terverifikasi admin.</p>
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

            <form method="POST" action="{{ route('shop.mitra.demo.masuk') }}" class="mb-6">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-gold-500 hover:bg-gold-600 text-navy-900 font-black py-4 rounded-xl transition-colors text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Coba Login Demo (1-klik)
                </button>
                <p class="text-xs text-navy-400 mt-2 leading-relaxed">
                    Akun demo otomatis dibuat & status <b>terverifikasi</b>. Akses penuh: beranda mitra,
                    price list, unduh CSV, cetak price list, dan mode harga mitra di storefront.
                </p>
            </form>

            <div class="relative mb-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-navy-100"></div></div>
                <div class="relative flex justify-center text-xs font-bold text-navy-400 uppercase tracking-widest bg-white px-3"><span>atau masuk dengan akun sendiri</span></div>
            </div>

            <form method="POST" action="{{ route('shop.mitra.masuk.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-navy-500 uppercase tracking-wide">Password</label>
                    <input type="password" name="password" required
                           class="mt-1.5 w-full rounded-xl border border-navy-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <label class="flex items-center gap-2 text-sm text-navy-500">
                    <input type="checkbox" name="remember" value="1"
                           class="rounded border-navy-200 text-gold-500 focus:ring-gold-500">
                    Ingat saya
                </label>
                <button type="submit"
                        class="w-full bg-navy-900 hover:bg-navy-800 text-white font-black py-4 rounded-xl transition-colors text-base">
                    Masuk
                </button>
            </form>

            <p class="text-center text-sm text-navy-500 mt-5">
                Belum punya akun? <a href="{{ route('shop.mitra.daftar') }}" class="font-bold text-gold-600 hover:text-gold-700">Daftar sebagai mitra</a>
            </p>
        </div>
    </div>

@endsection