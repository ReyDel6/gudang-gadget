<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventaris - Gudang Gadget</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1c2333; margin: 24px; }
        h1 { font-size: 18px; margin: 0 0 2px; }
        .muted { color: #64748b; font-size: 11px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1c2333; padding-bottom: 8px; margin-bottom: 12px; }
        .stats { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
        .stat { border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; }
        .stat b { display: block; font-size: 16px; }
        .stat span { color: #64748b; font-size: 10px; text-transform: uppercase; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 7px; text-align: left; }
        th { background: #f1f5f9; }
        .right { text-align: right; }
        .section { font-size: 14px; font-weight: bold; margin: 16px 0 6px; }
        @media print { body { margin: 8px; } }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Gudang Gadget — Laporan Inventaris</h1>
            <div class="muted">Dicetak {{ now()->format('d M Y H:i') }}
                @if ($periode['awal'] || $periode['akhir'])
                    · Periode {{ $periode['awal'] ?: '…' }} s.d. {{ $periode['akhir'] ?: '…' }}
                @endif
            </div>
        </div>
    </div>

    <div class="stats">
        <div class="stat"><span>Total Produk</span><b>{{ number_format($totalProduk) }}</b></div>
        <div class="stat"><span>Total Stok</span><b>{{ number_format($totalStok) }}</b></div>
        <div class="stat"><span>Nilai Aset</span><b>Rp {{ number_format($nilaiAset, 0, ',', '.') }}</b></div>
        <div class="stat"><span>Habis / Menipis</span><b>{{ $produkHabis }} / {{ $produkMenipis }}</b></div>
        <div class="stat"><span>Mutasi Periode</span><b>+{{ number_format($masuk) }} / −{{ number_format($keluar) }}</b></div>
        <div class="stat"><span>Transaksi</span><b>{{ number_format($jumlahTransaksi) }}</b></div>
        <div class="stat"><span>Pendapatan Penjualan</span><b>Rp {{ number_format($pendapatan, 0, ',', '.') }}</b></div>
        <div class="stat"><span>Laba Penjualan</span><b>Rp {{ number_format($labaPeriode, 0, ',', '.') }}</b></div>
        <div class="stat"><span>Total Pembelian</span><b>Rp {{ number_format($totalPembelian, 0, ',', '.') }}</b></div>
        <div class="stat"><span>Transaksi Jual/Beli</span><b>{{ $jumlahPenjualan }} / {{ $jumlahPembelian }}</b></div>
    </div>

    <div class="section">Data Produk</div>
    <table>
        <thead>
            <tr><th>#</th><th>SKU</th><th>Nama Produk</th><th>Kategori</th><th>Stok</th><th>Satuan</th><th class="right">Harga Beli</th><th class="right">Nilai Aset</th><th>Status</th><th>Supplier</th><th>Lokasi</th></tr>
        </thead>
        <tbody>
            @forelse ($products as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->sku ?: '—' }}</td>
                    <td>{{ $row->nama_produk }}</td>
                    <td>{{ $row->kategori }}</td>
                    <td>{{ $row->stock }}</td>
                    <td>{{ $row->satuan }}</td>
                    <td class="right">Rp {{ number_format($row->harga_beli, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($row->nilai_aset, 0, ',', '.') }}</td>
                    <td>{{ $row->status }}</td>
                    <td>{{ $row->supplier ?: '—' }}</td>
                    <td>{{ $row->lokasi_rak ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="11" class="muted">Tidak ada produk.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section">Riwayat Mutasi</div>
    <table>
        <thead>
            <tr><th>Waktu</th><th>Produk</th><th>Tipe</th><th class="right">Perubahan</th><th class="right">Stok Akhir</th><th>Oleh</th></tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $log->gadget?->nama_produk ?: '—' }}</td>
                    <td>{{ $log->tipe }}</td>
                    <td class="right">{{ $log->perubahan > 0 ? '+' : '' }}{{ $log->perubahan }}</td>
                    <td class="right">{{ $log->stok_sesudah }}</td>
                    <td>{{ $log->pelaku }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">Tidak ada mutasi pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="muted">Dibuat oleh {{ Auth::user()->name }} — laporan.buat_cetak</div>
</body>
</html>