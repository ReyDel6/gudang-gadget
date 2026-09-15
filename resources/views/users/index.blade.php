@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-navy-800">Manajemen Pengguna</h1>
            <p class="text-sm text-navy-400 mt-1">Kelola akun yang dapat mengakses panel.</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Form tambah --}}
        <div class="bg-white rounded-2xl border border-navy-100 p-6 h-fit">
            <h2 class="text-base font-bold text-navy-800 mb-1">Tambah Pengguna</h2>
            <p class="text-sm text-navy-400 mb-5">Buat akun baru untuk staf gudang.</p>

            <form method="POST" action="{{ route('user.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Nama</label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('name') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('name') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Email</label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('email') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('email') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Sandi</label>
                    <input type="password" name="password" required
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('password') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('password') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1.5">Ulangi Sandi</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>

                <button type="submit" class="w-full bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Tambahkan
                </button>
            </form>
        </div>

        {{-- Daftar pengguna --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-navy-100 overflow-hidden">

            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">ID</th>
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-4 py-3 text-navy-400 font-mono">#{{ $user->id }}</td>
                            <td class="px-4 py-3 font-semibold text-navy-800">
                                {{ $user->name }}
                                @if (auth()->id() === $user->id)
                                    <span class="ml-1 text-[10px] uppercase tracking-wide bg-gold-100 text-gold-700 px-2 py-0.5 rounded-full">Anda</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-navy-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('user.edit', $user->id) }}"
                                       class="px-3 py-1.5 rounded-lg border border-navy-100 text-navy-600 hover:bg-navy-50 font-semibold text-xs">
                                        Edit
                                    </a>
                                    @if (auth()->id() !== $user->id)
                                        <form action="{{ route('user.destroy', $user->id) }}" method="POST"
                                              onsubmit="return confirm('Hapus pengguna {{ $user->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg border border-rose-100 text-rose-600 hover:bg-rose-50 font-semibold text-xs">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-navy-400">Belum ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection