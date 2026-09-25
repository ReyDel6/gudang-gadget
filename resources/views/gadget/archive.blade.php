@extends('layouts.app')

@section('title', 'Arsip Produk')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-navy-800">Arsip Produk</h1>
        <a href="{{ route('gadget.index') }}"
           class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            ← Semua Produk
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">ID</th>
                        <th class="px-4 py-3 font-medium">SKU</th>
                        <th class="px-4 py-3 font-medium">Nama Produk</th>
                        <th class="px-4 py-3 font-medium">Kategori</th>
                        <th class="px-4 py-3 font-medium">Diarsipkan</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @forelse ($data as $row)
                        <tr>
                            <td class="px-4 py-3 font-mono text-navy-400">#{{ $row->id }}</td>
                            <td class="px-4 py-3 font-mono text-navy-500">{{ $row->sku ?: '—' }}</td>
                            <td class="px-4 py-3 font-semibold text-navy-800">{{ $row->nama_produk }}</td>
                            <td class="px-4 py-3 text-navy-600">{{ $row->kategori }}</td>
                            <td class="px-4 py-3 text-navy-500">{{ $row->deleted_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('gadget.restore', $row->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg border border-emerald-100 text-emerald-600 hover:bg-emerald-50 font-semibold text-xs">
                                            Restore
                                        </button>
                                    </form>
                                    <form action="{{ route('gadget.force-destroy', $row->id) }}" method="POST"
                                          data-confirm="Hapus permanen {{ $row->nama_produk }}? Tindakan ini tidak dapat dibatalkan."
                                          onsubmit="return confirm(this.dataset.confirm);">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg border border-rose-100 text-rose-600 hover:bg-rose-50 font-semibold text-xs">
                                            Hapus Permanen
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-navy-400">
                                Tidak ada produk yang diarsipkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($data->hasPages())
            <div class="px-4 py-4 border-t border-navy-100">
                {{ $data->links() }}
            </div>
        @endif
    </div>

@endsection