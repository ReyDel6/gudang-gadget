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