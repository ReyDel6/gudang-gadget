<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Opname {{ $opname->no_invoice }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #111; width: 720px; margin: 24px auto; }
        .tengah { text-align: center; }
        .hr { border-top: 2px solid #111; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 5px 8px; text-align: left; }
        th { background: #eee; }
        .num { text-align: right; font-family: monospace; }
        .aksi { font-family: Arial, sans-serif; font-size: 13px; margin: 0 0 16px; }
        .aksi a, .aksi button { display: inline-block; margin-right: 6px; padding: 8px 14px; border-radius: 8px; text-decoration: none; border: 1px solid #ccc; background: #f6f6f6; color: #111; cursor: pointer; }
        .ttd { margin-top: 48px; display: flex; justify-content: space-between; }
        .ttd div { text-align: center; width: 220px; }
        .ttd .atas { height: 60px; }
        @media print { body { margin: 0; width: auto; } .aksi { display: none; } }
    </style>
</head>
<body>
    <div class="aksi">
        <button onclick="window.print()">Cetak</button>
    </div>

    <div class="tengah">
        <div style="font-size: 18px; font-weight: 800;">GUDANG GADGET</div>
        <div>Berita Acara Stok Opname</div>
    </div>
    <div class="hr"></div>
    <table style="width:auto;">
        <tr><td style="border:none; width:120px;">No. Opname</td><td style="border:none; font-weight:bold;">{{ $opname->no_invoice }}</td></tr>
        <tr><td style="border:none;">Tanggal Mulai</td><td style="border:none;">{{ $opname->started_at?->format('d M Y H:i') }}</td></tr>
        @if ($opname->completed_at)
            <tr><td style="border:none;">Tanggal Selesai</td><td style="border:none;">{{ $opname->completed_at->format('d M Y H:i') }}</td></tr>
        @endif
        <tr><td style="border:none;">Auditor</td><td style="border:none;">{{ $opname->auditor?->name }}</td></tr>
        <tr><td style="border:none;">Kategori</td><td style="border:none;">{{ $opname->category_filter ?: 'Semua (Full Count)' }}</td></tr>
        @if ($opname->notes)
            <tr><td style="border:none;">Catatan</td><td style="border:none;">{{ $opname->notes }}</td></tr>
        @endif
    </table>

    <table style="width: 320px; margin-top: 14px;">
        <tr>
            <th>Stok Sistem</th>
            <th>Stok Fisik</th>
            <th>Selisih</th>
        </tr>
        <tr>
            <td class="num">{{ number_format($opname->total_system_items) }}</td>
            <td class="num">{{ number_format($opname->total_physical_items) }}</td>
            <td class="num">{{ number_format($opname->total_difference) }}</td>
        </tr>
    </table>

    <table style="margin-top: 16px;">
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th>SKU</th>
                <th>Produk</th>
                <th style="width:60px;" class="num">Sistem</th>
                <th style="width:60px;" class="num">Fisik</th>
                <th style="width:60px;" class="num">Selisih</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($opname->items()->with('gadget')->orderBy('gadget_id')->get() as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->gadget?->sku ?: '-' }}</td>
                    <td>{{ $row->gadget?->nama_produk }}</td>
                    <td class="num">{{ $row->system_stock }}</td>
                    <td class="num">{{ $row->physical_stock ?? '—' }}</td>
                    <td class="num">{{ $row->difference ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="tengah">Tidak ada item tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd">
        <div>
            <div class="atas">Auditor,</div>
            <div style="margin-top: 8px;">( {{ $opname->auditor?->name ?: '....' }} )</div>
        </div>
        <div>
            <div class="atas">Mengetahui,</div>
        </div>
    </div>
    <script>window.print();</script>
</body>
</html>