<?php

namespace App\Http\Controllers;

use App\Models\Gadget;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\StokLog;
use App\Models\Supplier;
use App\Support\Pembayaran;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $periode = $this->periodeParams($request);

        $produkQuery = Gadget::query();
        if ($kategori = $request->input('kategori')) {
            $produkQuery->where('kategori', $kategori);
        }
        if ($supplier = $request->input('supplier')) {
            $produkQuery->where('supplier', $supplier);
        }
        if ($status = $request->input('status')) {
            $produkQuery->where('status', $status);
        }

        $logQuery = StokLog::with('gadget')
            ->periode($periode['awal'], $periode['akhir']);
        if ($jenis = $request->input('jenis')) {
            $logQuery->where('tipe', $jenis);
        }

        $logBase = (clone $logQuery)->get();

        $totalProduk = (clone $produkQuery)->count();
        $totalStok = (int) (clone $produkQuery)->sum('stock');
        $nilaiAset = (float) (clone $produkQuery)->get()->sum(fn (Gadget $g) => $g->nilai_aset);
        $produkHabis = (clone $produkQuery)->habis()->count();
        $produkMenipis = (clone $produkQuery)->menipis()->count();

        $masuk = (int) $logBase->where('perubahan', '>', 0)->sum('perubahan');
        $keluar = (int) abs($logBase->where('perubahan', '<', 0)->sum('perubahan'));
        $jumlahTransaksi = $logBase->count();

        $penjualanQuery = Penjualan::with('items')->aktif()->periode($periode['awal'], $periode['akhir']);
        $penjualanList = $penjualanQuery->get();
        $pendapatan = (float) $penjualanList->sum('total');
        $labaPeriode = (float) $penjualanList->sum(fn (Penjualan $p) => $p->laba);
        $jumlahPenjualan = $penjualanList->count();
        $totalPembelian = (float) Pembelian::periode($periode['awal'], $periode['akhir'])->sum('total');
        $jumlahPembelian = (int) Pembelian::periode($periode['awal'], $periode['akhir'])->count();

        $products = (clone $produkQuery)->latest('id')->paginate(15)->withQueryString();
        $logs = $logQuery->latest('id')->paginate(15)->withQueryString();

        $kategoriList = $this->kategoriOptions();
        $supplierList = $this->supplierOptions();

        return view('laporan.index', compact(
            'totalProduk', 'totalStok', 'nilaiAset', 'produkHabis', 'produkMenipis',
            'masuk', 'keluar', 'jumlahTransaksi', 'products', 'logs',
            'pendapatan', 'labaPeriode', 'jumlahPenjualan', 'totalPembelian', 'jumlahPembelian',
            'kategoriList', 'supplierList', 'periode'
        ));
    }

    public function labaRugi(Request $request)
    {
        $periode = $this->periodeParams($request);
        if (! $periode['awal']) {
            $periode['awal'] = today()->startOfMonth()->toDateString();
        }
        if (! $periode['akhir']) {
            $periode['akhir'] = today()->toDateString();
        }

        $data = $this->ringkasanLabaRugi($periode['awal'], $periode['akhir']);

        return view('laporan.laba-rugi', compact('periode', 'data'));
    }

    public function labaRugiPrint(Request $request)
    {
        $periode = $this->periodeParams($request);
        if (! $periode['awal']) {
            $periode['awal'] = today()->startOfMonth()->toDateString();
        }
        if (! $periode['akhir']) {
            $periode['akhir'] = today()->toDateString();
        }

        $data = $this->ringkasanLabaRugi($periode['awal'], $periode['akhir']);

        return view('laporan.laba-rugi-print', compact('periode', 'data'));
    }

    public function perBulan($tahun)
    {
        $bulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $awal = sprintf('%s-%02d-01', $tahun, $i);
            $akhir = \Carbon\Carbon::parse($awal)->endOfMonth()->toDateString();
            $ringkas = $this->ringkasanLabaRugi($awal, $akhir);

            $bulan[] = [
                'nomor' => $i,
                'label' => date('F Y', strtotime($awal)),
                'akhir' => $akhir,
                'penjualan' => $ringkas['pendapatanBersih'],
                'laba' => $ringkas['labaBersih'],
                'pembelian' => $ringkas['totalPembelian'],
                'jumlahPenjualan' => $ringkas['jumlahPenjualan'],
            ];
        }

        $ringkasTahun = $this->ringkasanLabaRugi("{$tahun}-01-01", "{$tahun}-12-31");
        $tahunTersedia = Penjualan::orderBy('no_invoice')->get('no_invoice')
            ->map(fn ($p) => (int) substr($p->no_invoice, 0, 4))->filter()->unique()->sortDesc()->values()->all();
        if (empty($tahunTersedia)) {
            $tahunTersedia = [(int) date('Y')];
        }

        return view('laporan.per-bulan', compact('tahun', 'bulan', 'ringkasTahun', 'tahunTersedia'));
    }

    public function harian(Request $request)
    {
        $tanggal = $request->input('tanggal') ?: today()->toDateString();

        $list = Penjualan::with('items')->whereDate('tanggal', $tanggal)
            ->orderBy('id')->get();
        $aktif = $list->reject(fn (Penjualan $p) => $p->payment_status === 'void');

        $data = [
            'tanggal' => $tanggal,
            'jumlah' => $aktif->count(),
            'omzet_kotor' => round((float) $aktif->sum('subtotal'), 2),
            'diskon' => round((float) $aktif->sum('diskon'), 2),
            'ppn' => round((float) $aktif->sum('pajak_nominal'), 2),
            'omzet_bersih' => round((float) $aktif->sum('total'), 2),
            'per_metode' => $aktif->groupBy('payment_method')
                ->mapWithKeys(fn ($g, $k) => [Pembayaran::labelMetode($k) => round((float) $g->sum('total'), 2)])
                ->all(),
            'void_jumlah' => $list->where('payment_status', 'void')->count(),
        ];

        $top = PenjualanItem::query()
            ->join('penjualans', 'penjualans.id', '=', 'penjualan_items.penjualan_id')
            ->whereDate('penjualans.tanggal', $tanggal)
            ->where('penjualans.payment_status', '!=', 'void')
            ->groupBy('penjualan_items.nama_produk')
            ->selectRaw('penjualan_items.nama_produk, SUM(penjualan_items.qty) AS total_qty, SUM(penjualan_items.subtotal) AS total_subtotal')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return view('laporan.harian', compact('tanggal', 'data', 'list', 'top'));
    }

    public function harianCsv(Request $request)
    {
        $tanggal = $request->input('tanggal') ?: today()->toDateString();

        $rows = Penjualan::with('items')->whereDate('tanggal', $tanggal)->orderBy('id')->get();

        $out = fopen('php://temp', 'w');
        fputcsv($out, ['No. Invoice', 'Jam', 'Kasir', 'Customer', 'Telp', 'Jumlah Item', 'Subtotal', 'Diskon', 'PPN', 'Total', 'Metode', 'Dibayar', 'Status']);

        foreach ($rows as $p) {
            fputcsv($out, [
                $p->no_invoice,
                $p->created_at ? $p->created_at->format('H:i') : '',
                (string) ($p->user_name ?? ''),
                (string) ($p->customer ?? ''),
                (string) ($p->customer_phone ?? ''),
                (string) $p->items->sum('qty'),
                number_format($p->subtotal, 2, ',', '.'),
                number_format((float) $p->diskon, 2, ',', '.'),
                number_format($p->pajak_nominal, 2, ',', '.'),
                number_format((float) $p->total, 2, ',', '.'),
                $p->payment_label,
                number_format($p->dibayar, 2, ',', '.'),
                $p->status_label,
            ]);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return response("\xEF\xBB\xBF" . $csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="penjualan-' . $tanggal . '.csv"',
        ]);
    }

    protected function ringkasanLabaRugi(string $awal, string $akhir): array
    {
        $penjualanList = Penjualan::with('items')->aktif()->periode($awal, $akhir)->get();

        $pendapatanKotor = (float) $penjualanList->sum(fn (Penjualan $p) => $p->subtotal);
        $diskon = (float) $penjualanList->sum('diskon');
        $ppn = (float) $penjualanList->sum('pajak_nominal');
        $pendapatanBersih = (float) $penjualanList->sum('total');
        $hpp = (float) $penjualanList->sum(fn (Penjualan $p) => $p->items->sum(fn ($i) => (float) $i->harga_beli * (int) $i->qty));
        $labaBersih = (float) $penjualanList->sum(fn (Penjualan $p) => $p->laba);

        $mutasiKeluar = StokLog::whereIn('tipe', [StokLog::TIPE_PENGELUARAN, StokLog::TIPE_RUSAK])
            ->where('perubahan', '<', 0)
            ->periode($awal, $akhir)
            ->with('gadget')
            ->get();
        $mutasiKeluarNilai = (float) $mutasiKeluar->sum(fn (StokLog $s) => abs($s->perubahan) * (float) $s->gadget?->harga_beli);
        $jumlahMutasiKeluar = (int) abs($mutasiKeluar->sum('perubahan'));

        $totalPembelian = (float) Pembelian::periode($awal, $akhir)->sum('total');
        $jumlahPembelian = (int) Pembelian::periode($awal, $akhir)->count();
        $jumlahPenjualan = $penjualanList->count();

        $nilaiPersediaanAkhir = (float) Gadget::query()->get()->sum(fn (Gadget $g) => (float) $g->stock * (float) $g->harga_beli);

        return compact(
            'pendapatanKotor', 'diskon', 'ppn', 'pendapatanBersih', 'hpp',
            'labaBersih', 'mutasiKeluarNilai', 'jumlahMutasiKeluar',
            'totalPembelian', 'jumlahPembelian', 'jumlahPenjualan', 'nilaiPersediaanAkhir'
        );
    }

    public function export(Request $request)
    {
        $rows = $this->buildRows($request, 'csv');

        $out = fopen('php://temp', 'w');
        fputcsv($out, ['ID', 'SKU', 'Nama Produk', 'Kategori', 'Stok', 'Satuan', 'Harga Beli', 'Nilai Aset', 'Status', 'Supplier', 'Lokasi Rak']);
        foreach ($rows as $row) {
            fputcsv($out, array_map([$this, 'csvGuard'], $row));
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return response("\xEF\xBB\xBF" . $csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-produk-' . date('Y-m-d-His') . '.csv"',
        ]);
    }

    public function exportXls(Request $request)
    {
        $rows = $this->buildRows($request, 'xls');
        $id = 'gudgad-' . date('Ymd-His');

        $html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="UTF-8"></head>'
            . '<table border="1">'
            . '<tr><th>ID</th><th>SKU</th><th>Nama Produk</th><th>Kategori</th><th>Stok</th><th>Satuan</th><th>Harga Beli</th><th>Nilai Aset</th><th>Status</th><th>Supplier</th><th>Lokasi Rak</th></tr>';
        foreach ($rows as $row) {
            $html .= '<tr>' . collect($row)->map(fn ($c) => '<td>' . htmlspecialchars((string) $c) . '</td>')->implode('') . '</tr>';
        }
        $html .= '</table></html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-produk-' . $id . '.xls"',
        ]);
    }

    public function print(Request $request)
    {
        $periode = $this->periodeParams($request);

        $produkQuery = Gadget::query();
        if ($kategori = $request->input('kategori')) {
            $produkQuery->where('kategori', $kategori);
        }
        if ($supplier = $request->input('supplier')) {
            $produkQuery->where('supplier', $supplier);
        }
        if ($status = $request->input('status')) {
            $produkQuery->where('status', $status);
        }

        $logQuery = StokLog::with('gadget')
            ->periode($periode['awal'], $periode['akhir']);
        if ($jenis = $request->input('jenis')) {
            $logQuery->where('tipe', $jenis);
        }

        $logBase = (clone $logQuery)->get();

        $totalProduk = (clone $produkQuery)->count();
        $totalStok = (int) (clone $produkQuery)->sum('stock');
        $nilaiAset = (float) (clone $produkQuery)->get()->sum(fn (Gadget $g) => $g->nilai_aset);
        $produkHabis = (clone $produkQuery)->habis()->count();
        $produkMenipis = (clone $produkQuery)->menipis()->count();

        $masuk = (int) $logBase->where('perubahan', '>', 0)->sum('perubahan');
        $keluar = (int) abs($logBase->where('perubahan', '<', 0)->sum('perubahan'));
        $jumlahTransaksi = $logBase->count();

        $penjualanList = Penjualan::with('items')->aktif()->periode($periode['awal'], $periode['akhir'])->get();
        $pendapatan = (float) $penjualanList->sum('total');
        $labaPeriode = (float) $penjualanList->sum(fn (Penjualan $p) => $p->laba);
        $jumlahPenjualan = $penjualanList->count();
        $totalPembelian = (float) Pembelian::periode($periode['awal'], $periode['akhir'])->sum('total');
        $jumlahPembelian = (int) Pembelian::periode($periode['awal'], $periode['akhir'])->count();

        $products = (clone $produkQuery)->orderBy('kategori')->orderBy('nama_produk')->get();
        $logs = $logBase;

        return view('laporan.print', compact(
            'totalProduk', 'totalStok', 'nilaiAset', 'produkHabis', 'produkMenipis',
            'masuk', 'keluar', 'jumlahTransaksi', 'products', 'logs', 'periode',
            'pendapatan', 'labaPeriode', 'jumlahPenjualan', 'totalPembelian', 'jumlahPembelian'
        ));
    }

    protected function buildRows(Request $request, string $mode): array
    {
        $query = Gadget::query();

        if ($kategori = $request->input('kategori')) {
            $query->where('kategori', $kategori);
        }
        if ($supplier = $request->input('supplier')) {
            $query->where('supplier', $supplier);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return $query->orderBy('kategori')->orderBy('nama_produk')->get()->map(function (Gadget $g) {
            return [
                (string) $g->id,
                (string) $g->sku,
                (string) $g->nama_produk,
                (string) $g->kategori,
                (string) $g->stock,
                (string) ($g->satuan ?? 'pcs'),
                (string) $g->harga_beli,
                number_format($g->nilai_aset, 2, ',', '.'),
                (string) $g->status,
                (string) ($g->supplier ?? ''),
                (string) ($g->lokasi_rak ?? ''),
            ];
        })->all();
    }

    protected function periodeParams(Request $request): array
    {
        $awal = $request->input('tanggal_awal');
        $akhir = $request->input('tanggal_akhir');

        return [
            'awal' => $awal ?: null,
            'akhir' => $akhir ?: null,
        ];
    }

    protected function kategoriOptions(): array
    {
        $defaults = ['SmartPhone', 'Laptop', 'Tablet', 'SmartWatch'];
        $existing = Gadget::query()->distinct()->pluck('kategori')
            ->map(fn ($k) => trim((string) $k))->filter()->all();

        return collect($defaults)->concat($existing)->unique()->sort()->values()->all();
    }

    protected function supplierOptions(): array
    {
        $existing = Gadget::query()->distinct()->pluck('supplier')
            ->map(fn ($s) => trim((string) $s))->filter()->all();
        $masters = Supplier::query()->pluck('nama')->all();

        return collect($existing)->concat($masters)->unique()->sort()->values()->all();
    }

    protected function csvGuard(mixed $value): string
    {
        $value = (string) $value;
        if (preg_match('/^[=\+\-@]/', $value)) {
            return "'" . $value;
        }

        return $value;
    }
}