<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi - Gudang Gadget</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1c2333; margin: 24px; }
        h1 { font-size: 18px; margin: 0 0 2px; }
        .muted { color: #64748b; font-size: 11px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1c2333; padding-bottom: 8px; margin-bottom: 14px; }
        h2.title { text-align: center; margin: 4px 0 2px; }
        p.period { text-align: center; color: #64748b; margin: 0 0 18px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        .row-bold { font-weight: 700; }

        @media print {
            body { margin: 12mm; }
        }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <h1>GUDANG GADGET</h1>
            <div class="muted">Laporan Laba Rugi &bull; Dicetak {{ \Carbon\Carbon::now()->translatedFormat('d M Y H:i') }}</div>
        </div>
    </div>

    <h2 class="title">LAPORAN LABA RUGI</h2>
    <p class="period">
        Periode {{ \Carbon\Carbon::parse($periode['awal'])->translatedFormat('d M Y') }} s.d.
        {{ \Carbon\Carbon::parse($periode['akhir'])->translatedFormat('d M Y') }}
    </p>

    <table>
        <tbody>
            <tr><td class="row-bold" style="padding:6px 0 2px;">PENDAPATAN</td></tr>
            <tr>
                <td style="padding-left:24px; padding:2px 0;">Penjualan (subtotal)</td>
                <td class="row-bold" style="text-align:right; font-family:'Courier New', monospace; padding:2px 0;">Rp {{ number_format($data['pendapatanKotor'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding-left:24px; padding:2px 0;">Diskon diberikan</td>
                <td style="text-align:right; font-family:'Courier New', monospace; padding:2px 0;">(Rp {{ number_format($data['diskon'], 0, ',', '.') }})</td>
            </tr>
            <tr>
                <td style="padding-left:24px; padding:2px 0;">PPN diterima</td>
                <td style="text-align:right; font-family:'Courier New', monospace; padding:2px 0;">Rp {{ number_format($data['ppn'], 0, ',', '.') }}</td>
            </tr>
            <tr class="row-bold" style="border-top:2px solid #1c2333;">
                <td style="padding:6px 0 2px;">Pendapatan Bersih</td>
                <td style="text-align:right; font-family:'Courier New', monospace; padding:6px 0 2px;">Rp {{ number_format($data['pendapatanBersih'], 0, ',', '.') }}</td>
            </tr>

            <tr><td class="row-bold" style="padding:10px 0 2px;">HARGA POKOK PENJUALAN (HPP)</td></tr>
            <tr>
                <td style="padding-left:24px; padding:2px 0;">HPP barang terjual</td>
                <td style="text-align:right; font-family:'Courier New', monospace; padding:2px 0;">(Rp {{ number_format($data['hpp'], 0, ',', '.') }})</td>
            </tr>
            <tr class="row-bold">
                <td style="padding:6px 0 2px;">Laba Kotor</td>
                <td style="text-align:right; font-family:'Courier New', monospace; padding:6px 0 2px;">Rp {{ number_format($data['pendapatanBersih'] - $data['hpp'], 0, ',', '.') }}</td>
            </tr>

            <tr><td class="row-bold" style="padding:10px 0 2px;">BEBAN / KERUGIAN OPERASIONAL</td></tr>
            <tr>
                <td style="padding-left:24px; padding:2px 0;">Pengurangan persediaan (rusak/retur) — {{ $data['jumlahMutasiKeluar'] }} unit</td>
                <td style="text-align:right; font-family:'Courier New', monospace; padding:2px 0;">(Rp {{ number_format($data['mutasiKeluarNilai'], 0, ',', '.') }})</td>
            </tr>

            <tr class="row-bold" style="border-top:2px solid #1c2333;">
                <td style="padding:8px 0 2px;">LABA RUGI BERSIH</td>
                <td style="text-align:right; font-family:'Courier New', monospace; padding:8px 0 2px;">Rp {{ number_format(abs($data['labaBersih']), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table style="margin-top:18px;">
        <tr><td>Total pembelian periode: <b>Rp {{ number_format($data['totalPembelian'], 0, ',', '.') }}</b> ({{ $data['jumlahPembelian'] }} transaksi)</td></tr>
        <tr><td style="padding-top:4px;">Nilai persediaan akhir: <b>Rp {{ number_format($data['nilaiPersediaanAkhir'], 0, ',', '.') }}</b></td></tr>
    </table>

    <script>window.print();</script>
</body>
</html>