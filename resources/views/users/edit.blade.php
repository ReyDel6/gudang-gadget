@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-navy-800">Edit Pengguna</h1>
            <p class="text-sm text-navy-400 mt-1">ID #{{ $user->id }} · {{ $user->name }}</p>
        </div>
        <a href="{{ route('user.index') }}"
           class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            ← Kembali
        </a>
    </div>

    <div class="max-w-2xl mx-auto bg-white rounded-2xl border border-navy-100 p-8">
        <form action="{{ route('user.update', $user->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-navy-700 mb-1.5">Nama</label>
                <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                       class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('name') ? 'border-rose-400' : 'border-navy-100' }}">
                @error('name') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-navy-700 mb-1.5">Email</label>
                <input type="email" name="email" required value="{{ old('email', $user->email) }}"
                       class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('email') ? 'border-rose-400' : 'border-navy-100' }}">
                @error('email') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-navy-700 mb-1.5">Sandi Baru <span class="text-navy-400 font-normal">(kosongkan jika tidak diganti)</span></label>
                <input type="password" name="password" autocomplete="new-password"
                       class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('password') ? 'border-rose-400' : 'border-navy-100' }}">
                @error('password') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-navy-700 mb-1.5">Ulangi Sandi Baru</label>
                <input type="password" name="password_confirmation" autocomplete="new-password"
                       class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Simpan perubahan
                </button>
                <a href="{{ route('user.index') }}" class="px-6 py-2.5 rounded-lg text-navy-600 hover:bg-navy-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection