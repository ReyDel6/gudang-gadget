@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')

    <style>
        .lr-total td { font-weight: 700; color: #152A45; background: #F8FAFC; }
    </style>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-navy-800">Laporan Laba Rugi</h1>
            <p class="text-sm text-navy-400 mt-1">Ringkasan pendapatan, HPP, dan laba pada periode terpilih.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('laporan.print') }}" class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Laporan Umum
            </a>
            <a href="{{ route('laporan.laba-rugi.cetak') }}?tanggal_awal={{ $periode['awal'] }}&tanggal_akhir={{ $periode['akhir'] }}"
               class="border border-navy-100 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Cetak
            </a>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-navy-100 p-4 mb-6 flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-medium text-navy-400 uppercase tracking-wide mb-1">Tanggal Awal</label>
            <input type="date" name="tanggal_awal" value="{{ $periode['awal'] }}"
                   class="rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-navy-400 uppercase tracking-wide mb-1">Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" value="{{ $periode['akhir'] }}"
                   class="rounded-lg border border-navy-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
        </div>
        <button type="submit" class="bg-navy-700 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            Tampilkan
        </button>
    </form>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-navy-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-navy-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-navy-800">Laba Rugi</h2>
                    <p class="text-xs text-navy-400">{{ \Carbon\Carbon::parse($periode['awal'])->translatedFormat('d M Y') }} — {{ \Carbon\Carbon::parse($periode['akhir'])->translatedFormat('d M Y') }}</p>
                </div>
                <span class="text-xs text-navy-400">{{ $data['jumlahPenjualan'] }} transaksi penjualan</span>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-navy-100">
                    <tr>
                        <td class="px-6 py-3 font-semibold text-navy-800" colspan="5">PENDAPATAN</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-2.5 text-navy-600 pl-10">Penjualan (subtotal)</td>
                        <td class="px-4 py-2.5 text-right font-mono text-navy-700">Rp {{ number_format($data['pendapatanKotor'], 0, ',', '.') }}</td>
                        <td class="w-1/4"></td>
                    </tr>
                    <tr>
                        <td class="px-6 py-2.5 text-navy-600 pl-10">Diskon diberikan</td>
                        <td class="px-4 py-2.5 text-right font-mono text-rose-600">− Rp {{ number_format($data['diskon'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="px-6 py-2.5 text-navy-600 pl-10">PPN diterima</td>
                        <td class="px-4 py-2.5 text-right font-mono text-navy-700">Rp {{ number_format($data['ppn'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    <tr class="lr-total">
                        <td class="px-6 py-3 pl-10">Pendapatan Bersih</td>
                        <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($data['pendapatanBersih'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="px-6 py-3 font-semibold text-navy-800" colspan="5">HARGA POKOK PENJUALAN (HPP)</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-2.5 text-navy-600 pl-10">HPP barang terjual (Σ harga beli × qty)</td>
                        <td class="px-4 py-2.5 text-right font-mono text-rose-600">− Rp {{ number_format($data['hpp'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    <tr class="lr-total">
                        <td class="px-6 py-3 pl-10">LABA KOTOR</td>
                        <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($data['pendapatanBersih'] - $data['hpp'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="px-6 py-3 font-semibold text-navy-800" colspan="5">BEBAN / KERUGIAN OPERASIONAL</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-2.5 text-navy-600 pl-10">Pengurangan persediaan (rusak/retur)</td>
                        <td class="px-4 py-2.5 text-right font-mono text-rose-600">− Rp {{ number_format($data['mutasiKeluarNilai'], 0, ',', '.') }}</td>
                        <td class="text-right pr-6 text-navy-400">{{ $data['jumlahMutasiKeluar'] }} unit</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-3 font-bold bg-navy-700 text-white" colspan="3">LABA RUGI BERSIH</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-3 pl-16 text-navy-400">{{ $data['labaBersih'] >= 0 ? 'Laba' : 'Rugi' }} periode</td>
                        <td class="px-4 py-3 text-right font-mono font-bold {{ $data['labaBersih'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            Rp {{ number_format(abs($data['labaBersih']), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-navy-100 p-6">
                <h3 class="text-sm font-bold text-navy-800 mb-4">Aktivitas Pembelian</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-navy-500">Total pembelian</dt><dd class="font-mono font-semibold text-navy-800">Rp {{ number_format($data['totalPembelian'], 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-navy-500">Jumlah transaksi</dt><dd class="font-semibold text-navy-800">{{ $data['jumlahPembelian'] }}</dd></div>
                </dl>
            </div>
            <div class="bg-white rounded-2xl border border-navy-100 p-6">
                <h3 class="text-sm font-bold text-navy-800 mb-4">Neraca Ringkas</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-navy-500">Nilai persediaan akhir</dt><dd class="font-mono font-semibold text-navy-800">Rp {{ number_format($data['nilaiPersediaanAkhir'], 0, ',', '.') }}</dd></div>
                </dl>
            </div>
            <a href="{{ route('laporan.per-bulan', date('Y')) }}"
               class="block bg-white rounded-2xl border border-navy-100 hover:border-gold-300 p-6 transition-colors">
                <h3 class="text-sm font-bold text-navy-800">Laporan per Bulan →</h3>
                <p class="text-xs text-navy-400 mt-1">Rekap penjualan, pembelian, dan laba tiap bulan.</p>
            </a>
        </div>
    </div>

@endsection