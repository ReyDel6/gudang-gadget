@extends('layouts.app')

@section('title', 'Edit Data Produk')

@section('content')

    <div class="max-w-3xl mx-auto bg-white rounded-2xl border border-navy-100 p-8">
        <h1 class="text-xl font-bold text-navy-800">Edit Data Produk</h1>
        <p class="text-sm text-navy-400 mt-1 mb-6">ID Produk: <span class="font-mono">{{ $gadget->id }}</span> — SKU: <span class="font-mono">{{ $gadget->sku ?: 'auto' }}</span></p>

        <form action="{{ route('gadget.update', $gadget->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Nama Produk <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_produk" required value="{{ old('nama_produk', $gadget->nama_produk) }}"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('nama_produk') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('nama_produk') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $gadget->sku) }}" placeholder="Kosongkan untuk otomatis"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('sku') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('sku') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="kategori" list="kategori-list" required value="{{ old('kategori', $gadget->kategori) }}"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('kategori') ? 'border-rose-400' : 'border-navy-100' }}">
                    <datalist id="kategori-list">
                        @foreach ($kategoriList as $kategori)
                            <option value="{{ $kategori }}"></option>
                        @endforeach
                    </datalist>
                    @error('kategori') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Supplier</label>
                    <input type="text" name="supplier" value="{{ old('supplier', $gadget->supplier) }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Lokasi Rak</label>
                    <input type="text" name="lokasi_rak" value="{{ old('lokasi_rak', $gadget->lokasi_rak) }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Stok <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" required min="0" value="{{ old('stock', $gadget->stock) }}"
                           class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('stock') ? 'border-rose-400' : 'border-navy-100' }}">
                    @error('stock') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Stok Minimum</label>
                    <input type="number" name="stok_minimum" min="0" value="{{ old('stok_minimum', $gadget->stok_minimum) }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Satuan</label>
                    <input type="text" name="satuan" value="{{ old('satuan', $gadget->satuan) }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Harga Beli (Rp)</label>
                    <input type="number" name="harga_beli" min="0" step="0.01" value="{{ old('harga_beli', $gadget->harga_beli) }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Serial Number</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $gadget->serial_number) }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Tanggal Pembelian</label>
                    <input type="date" name="tanggal_pembelian" value="{{ old('tanggal_pembelian', optional($gadget->tanggal_pembelian)->format('Y-m-d')) }}"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Status</label>
                    <select name="status" required
                            class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                        @foreach (['Tersedia', 'Habis', 'Tidak Dijual'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $gadget->status) == $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-navy-700 mb-1">Foto Produk</label>
                    @if ($gadget->thumbnail)
                        <div class="mb-3">
                            <img src="{{ $gadget->foto_url }}" alt="{{ $gadget->nama_produk }}"
                                 class="h-20 w-20 rounded-lg border border-navy-100 object-cover">
                        </div>
                    @endif
                    <input type="file" name="foto" accept="image/*"
                           class="w-full rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                    @error('foto') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-navy-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="4"
                          class="w-full rounded-lg border px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 {{ $errors->first('deskripsi') ? 'border-rose-400' : 'border-navy-100' }}">{{ old('deskripsi', $gadget->deskripsi) }}</textarea>
                @error('deskripsi') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-navy-700 hover:bg-navy-800 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Simpan perubahan
                </button>
                <a href="{{ route('gadget.index') }}"
                   class="px-6 py-2.5 rounded-lg text-navy-600 hover:bg-navy-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection