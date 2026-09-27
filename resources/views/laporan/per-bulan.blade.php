@extends('layouts.app')

@section('title', 'Laporan per Bulan — ' . $tahun)

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-navy-800">Laporan per Bulan</h1>
            <p class="text-sm text-navy-400 mt-1">Rekap penjualan, pembelian, dan laba untuk tahun {{ $tahun }}.</p>
        </div>
        <div class="flex items-center gap-3">
            <select onchange="location.href = '/laporan/per-bulan/' + this.value"
                    class="rounded-lg border border-navy-100 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                @foreach ($tahunTersedia as $t)
                    <option value="{{ $t }}" @selected($t == $tahun)>Tahun {{ $t }}</option>
                @endforeach
            </select>
            <a href="{{ route('laporan.laba-rugi') }}" class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                ← Laba Rugi
            </a>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-navy-100 p-4">
            <p class="text-gold-500 font-semibold text-xs mb-1">Penjualan {{ $tahun }}</p>
            <p class="text-xl font-bold text-navy-800">Rp {{ number_format($ringkasTahun['pendapatanBersih'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-navy-100 p-4">
            <p class="text-emerald-600 font-semibold text-xs mb-1">Laba {{ $tahun }}</p>
            <p class="text-xl font-bold text-emerald-700">Rp {{ number_format($ringkasTahun['labaBersih'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-navy-100 p-4">
            <p class="text-navy-400 font-semibold text-xs mb-1">Pembelian {{ $tahun }}</p>
            <p class="text-xl font-bold text-navy-800">Rp {{ number_format($ringkasTahun['totalPembelian'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-navy-100 p-4">
            <p class="text-navy-400 font-semibold text-xs mb-1">Transaksi Penjualan</p>
            <p class="text-xl font-bold text-navy-800">{{ $ringkasTahun['jumlahPenjualan'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">Bulan</th>
                        <th class="px-4 py-3 font-medium text-right">Penjualan</th>
                        <th class="px-4 py-3 font-medium text-right">Pembelian</th>
                        <th class="px-4 py-3 font-medium text-right">Laba</th>
                        <th class="px-4 py-3 font-medium text-right">Transaksi</th>
                        <th class="px-4 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100">
                    @foreach ($bulan as $row)
                        <tr class="{{ $row['penjualan'] == 0 && $row['pembelian'] == 0 ? 'opacity-50' : '' }}">
                            <td class="px-4 py-3 font-semibold text-navy-800">{{ $row['label'] }}</td>
                            <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($row['penjualan'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($row['pembelian'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono {{ $row['laba'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Rp {{ number_format($row['laba'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-navy-600">{{ $row['jumlahPenjualan'] }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('laporan.laba-rugi') }}?tanggal_awal={{ $tahun }}-{{ sprintf('%02d', $row['nomor']) }}-01&tanggal_akhir={{ $row['akhir'] }}"
                                   class="text-navy-600 hover:text-gold-600 font-medium">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-navy-50 font-bold text-navy-800">
                        <td class="px-4 py-3">Total {{ $tahun }}</td>
                        <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($ringkasTahun['pendapatanBersih'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($ringkasTahun['totalPembelian'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono {{ $ringkasTahun['labaBersih'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Rp {{ number_format($ringkasTahun['labaBersih'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">{{ $ringkasTahun['jumlahPenjualan'] }}</td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@endsection