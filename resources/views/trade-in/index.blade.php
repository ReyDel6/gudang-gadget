@extends('layouts.app')

@section('title', 'Matriks Tukar Tambah')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Matriks Taksiran Tukar Tambah</h1>
        <a href="{{ route('trade-in.create') }}"
           class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            + Tambah Item
        </a>
    </div>

    <form method="GET" action="{{ route('trade-in.index') }}" class="flex flex-col sm:flex-row gap-3 mb-5 print-hidden">
        <input type="text" name="q" value="{{ request('q') }}"
               placeholder="Cari brand / model / kapasitas..."
               class="w-full max-w-sm rounded-lg border border-navy-100 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
        <button type="submit"
                class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">Cari</button>
    </form>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm font-semibold mb-5">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">Brand</th>
                        <th class="px-4 py-3 font-medium">Model & Kapasitas</th>
                        <th class="px-4 py-3 font-medium text-right">Grade A</th>
                        <th class="px-4 py-3 font-medium text-right">Grade B</th>
                        <th class="px-4 py-3 font-medium text-right">Grade C</th>
                        <th class="px-4 py-3 font-medium text-right">Grade D</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($matriks as $row)
                        <tr class="{{ $row->is_active ? '' : 'opacity-50' }}">
                            <td class="px-4 py-3 font-semibold text-navy-800">{{ $row->brand }}</td>
                            <td class="px-4 py-3 text-navy-700">
                                {{ $row->model_name }}
                                <span class="text-navy-400">{{ $row->capacity ? '(' . $row->capacity . ')' : '' }}</span>
                            </td>
                            @foreach (['A', 'B', 'C', 'D'] as $g)
                                <td class="px-4 py-3 font-mono font-bold text-navy-800 text-right">
                                    {{ number_format($row->nilaiUntuk($g), 0, ',', '.') }}
                                </td>
                            @endforeach
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $row->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-navy-50 text-navy-400' }}">
                                    {{ $row->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('trade-in.edit', $row->id) }}"
                                   class="text-gold-600 hover:text-gold-700 font-semibold text-xs">Edit</a>
                                @if (Auth::user()->role === 'admin')
                                    <form method="POST" action="{{ route('trade-in.destroy', $row->id) }}"
                                          onsubmit="return confirm('Hapus matriks ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-rose-500 hover:text-rose-600 font-semibold text-xs ml-2">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-navy-400">
                                Belum ada data matriks. Tambahkan brand & model agar customer bisa cek taksiran online.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-navy-100">
            {{ $matriks->links() }}
        </div>
    </div>

@endsection