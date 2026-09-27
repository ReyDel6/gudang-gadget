@extends('layouts.app')

@section('title', 'Banner Storefront')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Banner Promo Storefront</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('shop.home') }}" target="_blank"
               class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Buka Katalog Publik ↗
            </a>
            <a href="{{ route('store.settings') }}"
               class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Pengaturan Toko
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <form action="{{ route('store.banner.store') }}" method="POST" enctype="multipart/form-data"
                  class="bg-white rounded-2xl border border-navy-100 p-5 space-y-3">
                @csrf
                <h2 class="font-black text-navy-800">Tambah Banner</h2>
                <div>
                    <label class="block text-sm font-semibold text-navy-700 mb-1">Judul *</label>
                    <input type="text" name="title" required maxlength="100" value="{{ old('title') }}"
                           class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-700 mb-1">Subjudul</label>
                    <input type="text" name="subtitle" maxlength="180" value="{{ old('subtitle') }}"
                           class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-700 mb-1">Gambar</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                           class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm">
                    <p class="text-xs text-navy-400 mt-1">JPG/PNG/WebP, maks 3MB. Kosongkan untuk menggunakan placeholder.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-700 mb-1">URL Tujuan</label>
                    <input type="text" name="cta_link" maxlength="255" value="{{ old('cta_link') }}" placeholder="cth: /shop/katalog"
                           class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-navy-700 mb-1">Urutan</label>
                        <input type="number" name="order_position" min="0" value="0"
                               class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                    <label class="flex items-end gap-2 pb-2 text-sm text-navy-700 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-navy-200 text-gold-500"> Aktif
                    </label>
                </div>
                <button type="submit" class="w-full bg-gold-500 hover:bg-gold-600 text-navy-900 font-bold px-4 py-2.5 rounded-lg transition-colors">Simpan Banner</button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-4">
            @forelse ($banners as $banner)
                <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
                    <div class="flex flex-wrap items-center gap-4 p-4">
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                             class="w-24 h-16 object-cover rounded-lg bg-navy-50 border border-navy-100">
                        <div class="flex-1 min-w-40">
                            <p class="font-bold text-navy-800">{{ $banner->title }}
                                <span class="text-[10px] font-bold rounded-full px-2 py-0.5 ml-1 {{ $banner->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-navy-100 text-navy-400' }}">
                                    {{ $banner->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                </span>
                            </p>
                            @if ($banner->subtitle)
                                <p class="text-sm text-navy-500">{{ $banner->subtitle }}</p>
                            @endif
                            <p class="text-xs text-navy-400 mt-1">Urutan {{ $banner->order_position }}
                                @if ($banner->cta_link) · Arah: {{ $banner->cta_link }} @endif</p>
                        </div>
                        <form action="{{ route('store.banner.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Hapus banner ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:bg-rose-50 text-sm font-semibold px-3 py-2 rounded-lg transition-colors">Hapus</button>
                        </form>
                    </div>

                    <details class="border-t border-navy-100">
                        <summary class="cursor-pointer px-4 py-3 text-sm font-semibold text-navy-600 hover:bg-navy-50">Edit banner</summary>
                        <form action="{{ route('store.banner.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-sm font-semibold text-navy-700 mb-1">Judul *</label>
                                <input type="text" name="title" required maxlength="100" value="{{ $banner->title }}"
                                       class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-navy-700 mb-1">Subjudul</label>
                                <input type="text" name="subtitle" maxlength="180" value="{{ $banner->subtitle }}"
                                       class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-navy-700 mb-1">Gambar baru (opsional)</label>
                                <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                                       class="w-full rounded-lg border border-navy-200 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-navy-700 mb-1">URL Tujuan</label>
                                <input type="text" name="cta_link" maxlength="255" value="{{ $banner->cta_link }}"
                                       class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-navy-700 mb-1">Urutan</label>
                                <input type="number" name="order_position" min="0" value="{{ $banner->order_position }}"
                                       class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                            </div>
                            <label class="flex items-end gap-2 pb-2.5 text-sm text-navy-700 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" @checked($banner->is_active) class="h-4 w-4 rounded border-navy-200 text-gold-500"> Aktif
                            </label>
                            <div class="sm:col-span-2">
                                <button type="submit" class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">Simpan Perubahan</button>
                            </div>
                        </form>
                    </details>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-navy-100 p-10 text-center text-navy-400">Belum ada banner. Tambahkan banner promo pertama Anda.</div>
            @endforelse
        </div>
    </div>

@endsection