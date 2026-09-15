@extends('layouts.app')

@section('title', 'Formulir Tambah Produk')

@section('content')

    <div class="max-w-2xl mx-auto bg-white rounded-2xl border border-navy-100 p-8">
        <h1 class="text-xl font-bold text-navy-800">Formulir Tambah Produk baru</h1>
        <p class="text-sm text-navy-400 mt-1 mb-6">Semua kolom wajib diisi.</p>

        <form action="{{ route('gadget.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-navy-700 mb-1">Nama Produk</label>
                <input type="text" name="nama_produk" required value="{{ old('nama_produk') }}"
                       class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('nama_produk') ? 'border-rose-400' : 'border-navy-100' }}">
                @error('nama_produk') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Kategori</label>
                    <select name="kategori" required
                            class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                        @foreach ($kategoriList as $kategori)
                            <option value="{{ $kategori }}" @selected(old('kategori') == $kategori)>{{ $kategori }}</option>
                        @endforeach
                    </select>
                    @error('kategori') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Stock</label>
                    <input type="number" name="stock" required min="0" value="{{ old('stock') }}"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('stock') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('stock') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Status</label>
                    <select name="status" required
                            class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                        @foreach (['Tersedia', 'Habis', 'Tidak Dijual'] as $status)
                            <option value="{{ $status }}" @selected(old('status') == $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Foto Produk</label>
                    <input type="file" name="foto" accept="image/*"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                    @error('foto') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-navy-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" required rows="4"
                          class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('deskripsi') ? 'border-rose-400' : 'border-navy-100' }}">{{ old('deskripsi') }}</textarea>
                @error('deskripsi') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Simpan
                </button>
                <a href="{{ route('gadget.index') }}"
                   class="px-6 py-2.5 rounded-lg text-navy-600 hover:bg-navy-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection