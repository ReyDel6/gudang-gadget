@extends('layouts.app')

@section('title', 'Pengaturan Toko')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Pengaturan Profil Toko</h1>
        <a href="{{ route('store.banner.index') }}"
           class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            Kelola Banner
        </a>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">{{ session('success') }}</div>
    @endif

    <div class="max-w-3xl">
        <div class="rounded-xl border border-gold-200 bg-gold-50 px-4 py-3 text-sm text-gold-800 mb-6">
            Pengaturan ini disinkronkan otomatis ke Katalog Publik (<code>/shop</code>): nama toko, menu WhatsApp, footer, dan peta lokasi.
        </div>

        <form action="{{ route('store.settings.store') }}" method="POST" class="bg-white rounded-2xl border border-navy-100 p-6 space-y-5">
            @csrf
            @foreach ($fields as $key => $def)
                <div>
                    <label class="block text-sm font-semibold text-navy-700 mb-1">{{ $def['label'] }}</label>
                    @if (($def['type'] ?? 'text') === 'textarea')
                        <textarea name="{{ $key }}" rows="3" maxlength="1000"
                                  class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">{{ $values[$key] ?? '' }}</textarea>
                    @else
                        <input type="{{ $def['type'] ?? 'text' }}" name="{{ $key }}" value="{{ $values[$key] ?? '' }}"
                               maxlength="255"
                               class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                    @endif
                    @if (!empty($def['hint']))
                        <p class="text-xs text-navy-400 mt-1">{{ $def['hint'] }}</p>
                    @endif
                    @error($key)
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <div class="pt-2">
                <button type="submit" class="bg-gold-500 hover:bg-gold-600 text-navy-900 font-bold px-6 py-2.5 rounded-lg transition-colors">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

@endsection