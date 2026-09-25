<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportGadgetRequest;
use App\Http\Requests\StoreGadgetRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Requests\UpdateGadgetRequest;
use App\Models\Gadget;
use App\Models\GadgetFoto;
use App\Models\StokLog;
use App\Services\BarcodeGenerator;
use App\Services\StokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GadgetController extends Controller
{
    public function landing()
    {
        $totalProduk = Gadget::count();
        $totalStock = (int) Gadget::sum('stock');
        $nilaiAset = (float) Gadget::withoutTrashed()->get()->sum(fn (Gadget $g) => $g->nilai_aset);
        $habis = Gadget::habis()->count();
        $menipis = Gadget::menipis()->count();
        $kategori = Gadget::distinct()->count('kategori');
        $recent = Gadget::latest('id')->take(5)->get();
        $perKategori = Gadget::select('kategori', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(stock) as stok'))
            ->groupBy('kategori')
            ->get();

        $lowStock = Gadget::menipis()->latest('id')->take(6)->get();
        $recentLogs = StokLog::with('gadget', 'user')->latest('id')->take(8)->get();

        $chartData = [
            'categories' => $perKategori->map(function ($c) {
                return [
                    'kategori' => $c->kategori,
                    'stok' => (int) $c->stok,
                    'jumlah' => (int) $c->jumlah,
                ];
            })->values(),
        ];

        return view('landing', compact(
            'totalProduk', 'totalStock', 'nilaiAset', 'habis', 'menipis', 'kategori',
            'recent', 'lowStock', 'chartData', 'recentLogs'
        ));
    }

    public function create()
    {
        return view('gadget.create', ['kategoriList' => $this->kategoriOptions()]);
    }

    public function store(StoreGadgetRequest $request)
    {
        $data = $request->validated();
        $stock = (int) ($data['stock'] ?? 0);
        $status = $data['status'] ?? (($stock > 0) ? 'Tersedia' : 'Habis');
        $sku = $this->generateSku($data['sku'] ?? null);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('thumbnails', 'public');
        }

        try {
            $gadget = DB::transaction(function () use ($data, $stock, $status, $sku, $fotoPath) {
                $gadget = Gadget::create($this->collectData($data, $stock, $status, $sku));

                StokService::log($gadget, $stock, 0, $stock, StokLog::TIPE_STOK_AWAL, 'Produk baru dibuat dengan stok awal');

                if ($fotoPath) {
                    GadgetFoto::create(['id' => $gadget->id, 'url' => $fotoPath]);
                }

                return $gadget;
            });
        } catch (\Throwable $e) {
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            throw $e;
        }

        return redirect()->route('gadget.index')
            ->with('success', "Gadget \"{$gadget->nama_produk}\" berhasil ditambahkan.");
    }

    public function index(Request $request)
    {
        $query = Gadget::query();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('supplier', 'like', "%{$search}%");
            });
        }
        if ($kategori = $request->input('kategori')) {
            $query->where('kategori', $kategori);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $data = $query->latest('id')->paginate(15)->withQueryString();
        $kategoriList = $this->kategoriOptions();

        return view('gadget.index', compact('data', 'kategoriList'));
    }

    public function show($id)
    {
        $gadget = Gadget::where('id', $id)->firstOrFail();
        $stokLogs = $gadget->stokLogs()->with('user')->latest('id')->limit(50)->get();

        return view('gadget.show', compact('gadget', 'stokLogs'));
    }

    public function edit($id)
    {
        $gadget = Gadget::where('id', $id)->firstOrFail();

        return view('gadget.edit', compact('gadget'), ['kategoriList' => $this->kategoriOptions()]);
    }

    public function update(UpdateGadgetRequest $request, $id)
    {
        $gadget = Gadget::where('id', $id)->firstOrFail();
        $data = $request->validated();

        $stokLama = (int) $gadget->stock;
        $stock = (int) ($data['stock'] ?? $stokLama);
        $status = $data['status'] ?? $gadget->status;
        $sku = $gadget->sku ?: $this->generateSku($data['sku'] ?? null);

        $fotoPath = null;
        $oldFotoUrl = null;

        try {
            DB::transaction(function () use ($data, $gadget, $stock, $status, $sku, $stokLama, $request, &$fotoPath, &$oldFotoUrl) {
                if ($request->hasFile('foto')) {
                    $fotoPath = $request->file('foto')->store('thumbnails', 'public');
                    $oldFotoUrl = $gadget->thumbnail?->url;
                }

                $gadget->update($this->collectData($data, $stock, $status, $sku));

                if ($stock !== $stokLama) {
                    StokService::log($gadget, $stock - $stokLama, $stokLama, $stock, StokLog::TIPE_PENYESUAIAN, 'Perubahan data (edit)');
                }

                if ($fotoPath) {
                    if ($oldFotoUrl) {
                        $gadget->thumbnail()->update(['url' => $fotoPath]);
                    } else {
                        GadgetFoto::create(['id' => $gadget->id, 'url' => $fotoPath]);
                    }
                }
            });
        } catch (\Throwable $e) {
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            throw $e;
        }

        // File foto lama dihapus setelah transaksi berhasil.
        if ($fotoPath && $oldFotoUrl && $oldFotoUrl !== $fotoPath) {
            Storage::disk('public')->delete($oldFotoUrl);
        }

        return redirect()->route('gadget.index')
            ->with('success', 'Data berhasil diubah!');
    }

    public function ubahStok(Request $request, $id, $arah)
    {
        if (! in_array($arah, ['naik', 'turun'], true)) {
            return back()->withErrors(['stok' => 'Aksi stok tidak valid.']);
        }

        $gadget = Gadget::where('id', $id)->firstOrFail();
        $delta = $arah === 'naik' ? 1 : -1;

        // StokService menjalankan transaction + lock; ValidationException
        // otomatis di-redirect kembali dengan pesan error.
        StokService::adjust(
            $gadget,
            $arah === 'naik' ? StokLog::TIPE_PENERIMAAN : StokLog::TIPE_PENGELUARAN,
            $delta
        );

        return back()->with('success', "Stok \"{$gadget->nama_produk}\" diperbarui.");
    }

    public function transfer(TransferRequest $request, $id)
    {
        $gadget = Gadget::where('id', $id)->firstOrFail();
        $tujuan = $request->input('lokasi_rak_tujuan');
        $qty = (int) ($request->input('qty') ?? 0);
        $dari = $gadget->lokasi_rak ?: '-';
        $satuan = $gadget->satuan;

        DB::transaction(function () use ($gadget, $tujuan, $qty, $dari, $satuan) {
            $gadget->lokasi_rak = $tujuan;
            $gadget->save();

            $alasan = "Pindah lokasi dari rak {$dari} ke {$tujuan}";
            if ($qty > 0) {
                $alasan .= " ({$qty} {$satuan})";
            }

            StokService::log($gadget, 0, (int) $gadget->stock, (int) $gadget->stock, StokLog::TIPE_TRANSFER, $alasan);
        });

        return back()->with('success', "Produk dipindahkan ke rak \"{$tujuan}\".");
    }

    public function destroy($id)
    {
        $gadget = Gadget::where('id', $id)->firstOrFail();
        $nama = $gadget->nama_produk;
        $gadget->delete(); // soft delete / arsip

        return redirect()->route('gadget.index')
            ->with('success', "Data \"{$nama}\" diarsipkan. Lihat pada menu Arsip Produk.");
    }

    public function archive()
    {
        $data = Gadget::onlyTrashed()->latest('id')->paginate(15)->withQueryString();

        return view('gadget.archive', compact('data'));
    }

    public function restore($id)
    {
        $gadget = Gadget::onlyTrashed()->where('id', $id)->firstOrFail();
        $gadget->restore();

        return back()->with('success', "Data \"{$gadget->nama_produk}\" berhasil dipulihkan.");
    }

    public function forceDestroy($id)
    {
        $gadget = Gadget::onlyTrashed()->where('id', $id)->firstOrFail();

        if ($gadget->thumbnail) {
            Storage::disk('public')->delete($gadget->thumbnail->url);
            $gadget->thumbnail->delete();
        }

        $nama = $gadget->nama_produk;
        $gadget->forceDelete();

        return back()->with('success', "Data \"{$nama}\" dihapus permanen.");
    }

    public function export(Request $request)
    {
        $rows = $this->buildFilteredRows($request, ['kategori', 'nama_produk']);

        $filename = 'daftar-produk-' . date('Y-m-d-His') . '.csv';

        $contents = array_merge([[
            'ID', 'SKU', 'Nama Produk', 'Kategori', 'Supplier', 'Lokasi Rak', 'Serial Number',
            'Stok', 'Satuan', 'Harga Beli', 'Nilai Aset', 'Status', 'Tanggal Pembelian',
        ]], $rows);

        $out = fopen('php://temp', 'w');
        foreach ($contents as $row) {
            fputcsv($out, array_map([$this, 'csvGuard'], $row));
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return response("\xEF\xBB\xBF" . $csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function importForm()
    {
        return view('gadget.import');
    }

    public function importStore(ImportGadgetRequest $request)
    {
        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        if (! $handle) {
            return back()->withErrors(['file' => 'Tidak dapat membaca file CSV.']);
        }

        $headerRow = fgetcsv($handle, 0, ',', '"', '\\');
        if (! $headerRow) {
            fclose($handle);

            return back()->withErrors(['file' => 'File CSV kosong.']);
        }

        // Strip BOM pada header pertama.
        $headerRow[0] = str_replace("\xEF\xBB\xBF", '', $headerRow[0]);
        $index = $this->mapHeader($headerRow);

        $records = [];
        $errors = [];
        $rowNo = 1;

        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            $rowNo++;
            if (count(array_filter($row)) === 0) {
                continue; // baris kosong
            }

            $r = $this->rowToRecord($row, $index);
            if (empty($r['nama_produk'])) {
                $errors[] = "Baris {$rowNo}: kolom 'nama_produk' wajib diisi.";
                continue;
            }
            $records[] = $r;
        }
        fclose($handle);

        if ($errors) {
            return back()->withErrors(['file' => implode(' ', array_slice($errors, 0, 5))]);
        }
        if (! $records) {
            return back()->withErrors(['file' => 'Tidak ada baris data yang valid pada file CSV.']);
        }

        DB::transaction(function () use ($records) {
            foreach ($records as $r) {
                $stock = max(0, (int) ($r['stock'] ?? 0));
                $status = in_array($r['status'] ?? '', ['Tersedia', 'Habis', 'Tidak Dijual'], true)
                    ? $r['status']
                    : (($stock > 0) ? 'Tersedia' : 'Habis');
                $sku = $r['sku'] ?? $this->generateSku();

                $gadget = Gadget::create([
                    'nama_produk' => $r['nama_produk'],
                    'sku' => $sku,
                    'kategori' => $r['kategori'] ?? 'Umum',
                    'supplier' => $r['supplier'] ?? null,
                    'lokasi_rak' => $r['lokasi_rak'] ?? null,
                    'deskripsi' => $r['deskripsi'] ?? null,
                    'harga_beli' => (float) ($r['harga_beli'] ?? 0),
                    'satuan' => $r['satuan'] ?? 'pcs',
                    'tanggal_pembelian' => $r['tanggal_pembelian'] ?? null,
                    'stock' => $stock,
                    'stok_minimum' => ($r['stok_minimum'] !== '' && $r['stok_minimum'] !== null) ? (int) $r['stok_minimum'] : null,
                    'serial_number' => $r['serial_number'] ?? null,
                    'status' => $status,
                ]);

                StokService::log($gadget, $stock, 0, $stock, StokLog::TIPE_STOK_AWAL, 'Import dari CSV');
            }
        });

        return redirect()->route('gadget.import')
            ->with('success', count($records) . ' produk berhasil diimpor dari CSV.');
    }

    public function importTemplate()
    {
        $header = ['sku', 'nama_produk', 'kategori', 'deskripsi', 'stock', 'status', 'harga_beli', 'satuan', 'stok_minimum', 'supplier', 'lokasi_rak', 'serial_number', 'tanggal_pembelian'];

        $out = fopen('php://temp', 'w');
        fputcsv($out, $header);
        fputcsv($out, ['SKU-001', 'Contoh Produk', 'SmartPhone', 'Deskripsi contoh', '5', 'Tersedia', '1500000', 'pcs', '3', 'PT Supplier', 'Rak-A1', '', '2026-09-21']);
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return response("\xEF\xBB\xBF" . $csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-import-produk.csv"',
        ]);
    }

    public function barcode($id)
    {
        $gadget = Gadget::where('id', $id)->firstOrFail();
        $barcode = BarcodeGenerator::code39($gadget->sku ?: ('ID' . str_pad((string) $gadget->id, 6, '0', STR_PAD_LEFT)));

        return view('gadget.barcode', compact('gadget', 'barcode'));
    }

    protected function kategoriOptions(): array
    {
        $defaults = ['SmartPhone', 'Laptop', 'Tablet', 'SmartWatch'];
        $existing = Gadget::query()->distinct()->pluck('kategori')
            ->map(fn ($k) => trim((string) $k))->filter()->all();

        return collect($defaults)->concat($existing)->unique()->sort()->values()->all();
    }

    protected function collectData(array $data, int $stock, string $status, string $sku): array
    {
        return [
            'nama_produk' => $data['nama_produk'],
            'sku' => $sku,
            'kategori' => $data['kategori'],
            'supplier' => $data['supplier'] ?? null,
            'lokasi_rak' => $data['lokasi_rak'] ?? null,
            'deskripsi' => $data['deskripsi'] ?? null,
            'harga_beli' => $data['harga_beli'] ?? 0,
            'satuan' => $data['satuan'] ?? 'pcs',
            'tanggal_pembelian' => $data['tanggal_pembelian'] ?? null,
            'stock' => $stock,
            'stok_minimum' => ($data['stok_minimum'] ?? null) !== null ? (int) $data['stok_minimum'] : null,
            'serial_number' => $data['serial_number'] ?? null,
            'status' => $status,
        ];
    }

    protected function generateSku(?string $prefer = null): string
    {
        if ($prefer && ! Gadget::withTrashed()->where('sku', $prefer)->exists()) {
            return strtoupper(trim($prefer));
        }

        do {
            $sku = 'GDG-' . strtoupper(Str::random(6));
        } while (Gadget::withTrashed()->where('sku', $sku)->exists());

        return $sku;
    }

    protected function buildFilteredRows(Request $request, array $orderBy = ['kategori', 'nama_produk']): array
    {
        $query = Gadget::query();

        if ($search = trim((string) $request->input('search'))) {
            $query->where('nama_produk', 'like', "%{$search}%");
        }
        if ($kategori = $request->input('kategori')) {
            $query->where('kategori', $kategori);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return $query->orderBy($orderBy[0])->orderBy($orderBy[1])->get()->map(function (Gadget $g) {
            return [
                (string) $g->id,
                (string) $g->sku,
                (string) $g->nama_produk,
                (string) $g->kategori,
                (string) ($g->supplier ?? ''),
                (string) ($g->lokasi_rak ?? ''),
                (string) ($g->serial_number ?? ''),
                (string) $g->stock,
                (string) ($g->satuan ?? 'pcs'),
                (string) $g->harga_beli,
                number_format($g->nilai_aset, 2, ',', '.'),
                (string) $g->status,
                $g->tanggal_pembelian?->format('Y-m-d') ?? '',
            ];
        })->all();
    }

    protected function mapHeader(array $header): array
    {
        $map = [];
        foreach ($header as $i => $name) {
            $key = strtolower(trim(str_replace(['-', ' '], '_', $name)));
            $aliases = [
                'nama' => 'nama_produk',
                'name' => 'nama_produk',
                'nama_produk' => 'nama_produk',
                'sku' => 'sku',
                'kategori' => 'kategori',
                'category' => 'kategori',
                'deskripsi' => 'deskripsi',
                'description' => 'deskripsi',
                'stok' => 'stock',
                'stock' => 'stock',
                'qty' => 'stock',
                'status' => 'status',
                'harga_beli' => 'harga_beli',
                'harga' => 'harga_beli',
                'satuan' => 'satuan',
                'unit' => 'satuan',
                'stok_minimum' => 'stok_minimum',
                'min_stok' => 'stok_minimum',
                'supplier' => 'supplier',
                'lokasi_rak' => 'lokasi_rak',
                'rak' => 'lokasi_rak',
                'serial_number' => 'serial_number',
                'serial' => 'serial_number',
                'tanggal_pembelian' => 'tanggal_pembelian',
                'tanggal' => 'tanggal_pembelian',
            ];
            $map[$i] = $aliases[$key] ?? null;
        }

        return $map;
    }

    protected function rowToRecord(array $row, array $index): array
    {
        $record = [];
        foreach ($index as $col => $field) {
            if ($field) {
                $record[$field] = trim((string) ($row[$col] ?? ''));
            }
        }

        return array_filter($record, fn ($v) => $v !== '');
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