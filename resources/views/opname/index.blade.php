@extends('layouts.app')

@section('title', 'Stok Opname')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Stok Opname</h1>
        <button onclick="document.getElementById('modalBuka').classList.remove('hidden')"
                class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            + Mulai Opname
        </button>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm font-semibold mb-5">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('opname.index') }}" class="flex flex-col sm:flex-row gap-3 mb-5 print-hidden">
        <select name="status" onchange="this.form.submit()"
                class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 bg-white text-navy-700">
            <option value="">Semua Status</option>
            @foreach (\App\Models\StokOpname::STATUS as $kode => $label)
                <option value="{{ $kode }}" @selected(request('status') === $kode)>{{ $label }}</option>
            @endforeach
        </select>
        @if (request('status'))
            <a href="{{ route('opname.index') }}" class="border border-navy-100 bg-white text-navy-600 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-navy-50">Reset</a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">No. Opname</th>
                        <th class="px-4 py-3 font-medium">Dibuka</th>
                        <th class="px-4 py-3 font-medium">Auditor</th>
                        <th class="px-4 py-3 font-medium">Kategori</th>
                        <th class="px-4 py-3 font-medium">Sistem / Fisik</th>
                        <th class="px-4 py-3 font-medium">Selisih</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($opnames as $opname)
                        <tr>
                            <td class="px-4 py-3 font-mono font-semibold text-navy-800">{{ $opname->no_invoice }}</td>
                            <td class="px-4 py-3 text-navy-500 whitespace-nowrap">{{ $opname->started_at?->format('d M Y H:i') ?: '-' }}</td>
                            <td class="px-4 py-3 text-navy-600">{{ $opname->auditor?->name ?: '-' }}</td>
                            <td class="px-4 py-3 text-navy-600">{{ $opname->category_filter ?: 'Semua' }}</td>
                            <td class="px-4 py-3 font-mono text-navy-700">
                                {{ number_format($opname->total_system_items) }} / {{ number_format($opname->total_physical_items) }}
                            </td>
                            <td class="px-4 py-3 font-mono font-bold {{ ($opname->total_difference ?? 0) == 0 ? 'text-navy-500' : 'text-gold-700' }}">
                                {{ number_format($opname->total_difference) }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $warna = [
                                        'in_progress' => 'bg-gold-100 text-gold-700',
                                        'completed' => 'bg-emerald-50 text-emerald-700',
                                        'cancelled' => 'bg-navy-50 text-navy-400',
                                    ];
                                @endphp
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $warna[$opname->status] }}">
                                    {{ $opname->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('opname.show', $opname->id) }}" class="text-gold-600 hover:text-gold-700 font-semibold text-xs">Buka</a>
                                @if ($opname->status === \App\Models\StokOpname::ST_COMPLETED)
                                    <a href="{{ route('opname.cetak', $opname->id) }}" class="text-navy-500 hover:text-navy-700 font-semibold text-xs ml-2">Cetak</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-navy-400">
                                Belum ada sesi stok opname. Klik "Mulai Opname" untuk mulai rekonsiliasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-navy-100">
            {{ $opnames->links() }}
        </div>
    </div>

    {{-- Modal mulai opname --}}
    <div id="modalBuka" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-navy-900/60 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
            <div class="bg-navy-900 text-white px-5 py-4 flex justify-between items-center">
                <h3 class="font-black">Mulai Stok Opname</h3>
                <button onclick="document.getElementById('modalBuka').classList.add('hidden')" class="text-slate-400 hover:text-white text-xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('opname.store') }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-navy-400 uppercase block mb-1.5">Kategori (opsional)</label>
                    <select name="category_filter" class="w-full rounded-lg border border-navy-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 bg-white">
                        <option value="">Semua kategori</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k }}" @selected(old('category_filter') === $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-navy-400 uppercase block mb-1.5">Catatan (opsional)</label>
                    <textarea name="notes" rows="2" placeholder="cth: Opname rutin akhir bulan"
                              class="w-full rounded-lg border border-navy-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">{{ old('notes') }}</textarea>
                </div>
                <p class="text-xs text-navy-500 bg-navy-50 rounded-lg px-3 py-2">
                    Sistem akan menyimpan <b>snapshot stok sistem</b> saat ini. Selanjutnya scan SKU unit fisik satu per satu.
                </p>
                <button class="w-full bg-gold-500 hover:bg-gold-600 text-white font-black py-3 rounded-xl transition-colors">Mulai Opname</button>
            </form>
        </div>
    </div>

@endsection