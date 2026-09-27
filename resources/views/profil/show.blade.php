@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')

    <div class="max-w-3xl mx-auto space-y-6">

        <div>
            <h1 class="text-xl font-bold text-navy-800">Profil Saya</h1>
            <p class="text-sm text-navy-400 mt-1">Informasi akun dan pengaturan password.</p>
        </div>

        <div class="bg-white rounded-2xl border border-navy-100 p-6">
            <h2 class="font-bold text-navy-800 mb-4">Informasi Akun</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <dt class="text-navy-400">Nama</dt>
                    <dd class="font-semibold text-navy-800 mt-0.5">{{ $user->name }}</dd>
                </div>
                <div>
                    <dt class="text-navy-400">Email</dt>
                    <dd class="font-semibold text-navy-800 mt-0.5">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-navy-400">Peran</dt>
                    <dd class="mt-0.5">
                        <span class="rounded-full bg-navy-50 px-2.5 py-0.5 text-navy-600 text-xs font-semibold">{{ ucfirst($user->role) }}</span>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-2xl border border-navy-100 p-6">
            <h2 class="font-bold text-navy-800 mb-1">Ubah Password</h2>
            <p class="text-sm text-navy-400 mb-5">Gunakan password minimal 8 karakter.</p>

            <form action="{{ route('profil.password') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Password Lama</label>
                    <input type="password" name="password_lama" required autocomplete="current-password"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('password_lama') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('password_lama') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Password Baru</label>
                        <input type="password" name="password" required autocomplete="new-password"
                               class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('password') ? 'border-rose-400' : 'border-navy-100' }}">
                        @error('password') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-navy-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                               class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('password') ? 'border-rose-400' : 'border-navy-100' }}">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                        Simpan Password
                    </button>
                </div>
            </form>
        </div>

    </div>

@endsection