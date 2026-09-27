@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Audit Log Aktivitas</h1>
    </div>

    <form method="GET" action="{{ route('audit.index') }}" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / aksi / model..."
               class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 bg-white text-navy-700">
        <select name="aksi" onchange="this.form.submit()"
                class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 bg-white text-navy-700">
            <option value="">Semua Aksi</option>
            @foreach (['tambah produk', 'ubah produk', 'arsip produk', 'tambah kategori', 'hapus kategori', 'tambah supplier', 'hapus supplier', 'tambah pengguna', 'ubah pengguna', 'hapus pengguna', 'ubah password', 'tambah penjualan', 'batal penjualan', 'tambah pembelian', 'batal pembelian'] as $item)
                <option value="{{ $item }}" @selected(request('aksi') === $item)>{{ $item }}</option>
            @endforeach
        </select>
        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
               class="rounded-lg border border-navy-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500 text-navy-700">
        <button type="submit"
                class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
            Terapkan
        </button>
        @if (request()->hasAny(['search', 'aksi', 'tanggal']))
            <a href="{{ route('audit.index') }}"
               class="border border-navy-100 bg-white text-navy-600 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-navy-50">
                Reset
            </a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">Waktu</th>
                        <th class="px-4 py-3 font-medium">Oleh</th>
                        <th class="px-4 py-3 font-medium">Aksi</th>
                        <th class="px-4 py-3 font-medium">Objek</th>
                        <th class="px-4 py-3 font-medium">Perubahan</th>
                        <th class="px-4 py-3 font-medium">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="px-4 py-3 text-navy-500 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            <td class="px-4 py-3 text-navy-800">{{ $log->user_name }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-navy-50 px-2.5 py-0.5 text-navy-600 text-xs font-semibold">{{ $log->aksi }}</span>
                            </td>
                            <td class="px-4 py-3 text-navy-600">
                                @if ($log->model_type)
                                    <span class="font-mono text-xs">{{ class_basename($log->model_type) }}#{{ $log->model_id }}</span>
                                @else
                                    <span class="text-navy-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-navy-600 max-w-md">
                                @if ($log->perubahan)
                                    <div class="overflow-x-auto">
                                        <table class="text-xs border border-navy-100 rounded-lg overflow-hidden">
                                            @foreach ($log->perubahan as $kolom => $nilai)
                                                <tr>
                                                    <th class="px-2 py-0.5 bg-navy-50 text-left font-medium text-navy-500 whitespace-nowrap">{{ $kolom }}</th>
                                                    <td class="px-2 py-0.5 font-mono text-navy-700 max-w-[180px] truncate">{{ is_array($nilai) ? json_encode($nilai) : $nilai }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                @else
                                    <span class="text-navy-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-navy-500">{{ $log->ip ?: '—' }}</td>
                        </tr>
                    @empty
                        <x-empty-state colspan="6" icon="activity" message="Belum ada aktivitas tercatat." />
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $logs->links() }}
    </div>

@endsection