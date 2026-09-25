<?php

namespace App\Http\Controllers;

use App\Models\Gadget;
use App\Models\StokLog;
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

        $products = (clone $produkQuery)->latest('id')->paginate(15)->withQueryString();
        $logs = $logQuery->latest('id')->paginate(15)->withQueryString();

        $kategoriList = $this->kategoriOptions();

        return view('laporan.index', compact(
            'totalProduk', 'totalStok', 'nilaiAset', 'produkHabis', 'produkMenipis',
            'masuk', 'keluar', 'jumlahTransaksi', 'products', 'logs',
            'kategoriList', 'periode'
        ));
    }

    public function export(Request $request)
    {
        $query = Gadget::query();

        if ($kategori = $request->input('kategori')) {
            $query->where('kategori', $kategori);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $rows = $query->orderBy('kategori')->orderBy('nama_produk')->get()->map(function (Gadget $g) {
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

    protected function csvGuard(mixed $value): string
    {
        $value = (string) $value;
        if (preg_match('/^[=\+\-@]/', $value)) {
            return "'" . $value;
        }

        return $value;
    }
}