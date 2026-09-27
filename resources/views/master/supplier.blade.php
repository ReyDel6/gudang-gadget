@extends('layouts.app')

@section('title', 'Master Supplier')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Master Supplier</h1>
    </div>

    @if (! empty($orphan))
        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-700 text-sm">
            <span class="font-semibold">Supplier terpakai di produk tapi belum ada di daftar ini:</span>
            @foreach ($orphan as $s)
                <span class="inline-block rounded-full bg-white border border-amber-200 px-2.5 py-0.5 text-xs font-semibold ml-1 mt-1">{{ $s }}</span>
            @endforeach
            <span class="block mt-1">Supplier otomatis tersinkron saat produk disimpan.</span>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-navy-100 p-6">
            <h2 class="text-base font-bold text-navy-800 mb-4">Tambah Supplier</h2>
            <form action="{{ route('master.supplier.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Nama <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required value="{{ old('nama') }}"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('nama') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('nama') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Kontak (Nama)</label>
                    <input type="text" name="kontak" value="{{ old('kontak') }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Telepon</label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('email') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('email') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="2"
                              class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">{{ old('alamat') }}</textarea>
                </div>
                <button type="submit"
                        class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
                    Simpan
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-navy-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-navy-50 text-navy-500 text-left">
                            <th class="px-4 py-3 font-medium">Supplier</th>
                            <th class="px-4 py-3 font-medium">Kontak</th>
                            <th class="px-4 py-3 font-medium">Telepon</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy-100">
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-navy-800">{{ $supplier->nama }}</td>
                                <td class="px-4 py-3 text-navy-600">{{ $supplier->kontak ?: '—' }}</td>
                                <td class="px-4 py-3 text-navy-600">{{ $supplier->telepon ?: '—' }}</td>
                                <td class="px-4 py-3 text-navy-600">{{ $supplier->email ?: '—' }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <form action="{{ route('master.supplier.destroy', $supplier->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus supplier \"{{ $supplier->nama }}\" dari daftar? Produk yang memakainya tidak ikut dihapus.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-rose-500 hover:text-rose-700 font-medium">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <x-empty-state colspan="5" icon="tag" message="Belum ada supplier master." />
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $suppliers->links() }}
        </div>
    </div>

@endsection