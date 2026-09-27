@extends('layouts.app')

@section('title', $edit ? 'Edit Matriks Trade-In' : 'Tambah Matriks Trade-In')

@section('content')

    <div class="max-w-2xl">
        <a href="{{ route('trade-in.index') }}" class="text-xs font-semibold text-navy-400 hover:text-gold-600">← Kembali ke matriks</a>
        <h1 class="text-xl font-bold text-navy-800 mt-1">{{ $edit ? 'Edit Matriks Trade-In' : 'Tambah Matriks Trade-In' }}</h1>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm mt-4">
                <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ $edit ? route('trade-in.update', $row->id) : route('trade-in.store') }}" class="mt-5 space-y-4">
            @csrf
            @if ($edit) @method('PUT') @endif

            <div class="bg-white rounded-2xl border border-navy-100 p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs font-bold text-navy-400 uppercase tracking-wide">Brand *</label>
                        <input type="text" name="brand" required value="{{ old('brand', $row->brand ?? '') }}"
                               class="mt-1 w-full rounded-lg border border-navy-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-xs font-bold text-navy-400 uppercase tracking-wide">Model *</label>
                        <input type="text" name="model_name" required value="{{ old('model_name', $row->model_name ?? '') }}"
                               placeholder="cth: iPhone 13 / Galaxy S21 / Redmi Note 11"
                               class="mt-1 w-full rounded-lg border border-navy-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-navy-400 uppercase tracking-wide">Kapasitas (opsional)</label>
                    <input type="text" name="capacity" value="{{ old('capacity', $row->capacity ?? '') }}"
                           placeholder="cth: 128GB / 256GB"
                           class="mt-1 w-full rounded-lg border border-navy-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach ([
                        'base_price_grade_a' => 'Grade A (Rp)',
                        'price_grade_b' => 'Grade B (Rp)',
                        'price_grade_c' => 'Grade C (Rp)',
                        'price_grade_d' => 'Grade D (Rp)',
                    ] as $field => $label)
                        <div>
                            <label class="text-xs font-bold text-navy-400 uppercase tracking-wide">{{ $label }} *</label>
                            <input type="number" name="{{ $field }}" min="0" step="1000" required
                                   value="{{ old($field, $row->$field ?? '0') }}"
                                   class="mt-1 w-full rounded-lg border border-navy-100 px-3 py-2.5 text-sm text-right font-mono focus:outline-none focus:ring-2 focus:ring-gold-500">
                        </div>
                    @endforeach
                </div>

                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" name="is_active" value="1"
                           class="w-4.5 h-4.5 rounded border-navy-200 text-gold-500 focus:ring-gold-500"
                           @checked($edit ? $row->is_active : true)>
                    <span class="text-sm font-bold text-navy-800">Aktif (muncul di kalkulator publik)</span>
                </label>
            </div>

            <div class="flex items-center gap-3">
                <button class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-bold px-6 py-2.5 rounded-lg transition-colors">
                    {{ $edit ? 'Simpan Perubahan' : 'Simpan Matriks' }}
                </button>
                <a href="{{ route('trade-in.index') }}" class="text-sm font-semibold text-navy-500 hover:text-navy-700">Batal</a>
            </div>
        </form>
    </div>

@endsection