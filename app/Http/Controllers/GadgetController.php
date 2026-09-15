<?php

namespace App\Http\Controllers;

use App\Models\Gadget;
use App\Models\GadgetFoto;
use App\Models\StokLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GadgetController extends Controller
{
    public function landing()
    {
        $totalProduk = Gadget::count();
        $totalStock = (int) Gadget::sum('stock');
        $stokRendah = Gadget::where('status', 'Habis')->count();
        $kategori = Gadget::distinct()->count('kategori');
        $recent = Gadget::latest('id')->take(5)->get();
        $perKategori = Gadget::select('kategori', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(stock) as stok'))
            ->groupBy('kategori')
            ->get();

        $recentLogs = StokLog::with('gadget', 'user')->latest('id')->take(8)->get();

        $chartData = [
            'categories' => $perKategori->map(fn ($c) => [
                'kategori' => $c->kategori,
                'stok' => (int) $c->stok,
                'jumlah' => (int) $c->jumlah,
            ])->values(),
        ];

        return view('landing', compact(
            'totalProduk', 'totalStock', 'stokRendah', 'kategori', 'recent', 'chartData', 'recentLogs'
        ));
    }

    public function create()
    {
        return view('gadget.create', ['kategoriList' => $this->kategoriOptions()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'kategori' => 'required',
            'deskripsi' => 'required',
            'stock' => 'required|integer|min:0',
            'status' => 'required',
            'tanggal_pembelian' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $gadget = Gadget::create($request->only([
            'nama_produk',
            'kategori',
            'deskripsi',
            'stock',
            'status',
            'tanggal_pembelian',
        ]));

        // Catat stok awal
        StokLog::create([
            'gadget_id' => $gadget->id,
            'perubahan' => (int) $gadget->stock,
            'stok_sebelum' => 0,
            'stok_sesudah' => (int) $gadget->stock,
            'keterangan' => 'Stok awal',
            'user_id' => Auth::id(),
        ]);

        // Handle foto
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('thumbnails', 'public');

            GadgetFoto::create([
                'id' => $gadget->id,
                'url' => $path,
            ]);
        }

        return redirect()->route('gadget.index')
            ->with('success', 'Gadget berhasil ditambahkan');
    }

    public function index()
    {
        $data = Gadget::latest('id')->get();
        $kategoriList = $this->kategoriOptions();

        return view('gadget.index', compact('data', 'kategoriList'));
    }

    public function edit($id)
    {
        $gadget = Gadget::where('id', $id)->first();
        return view('gadget.edit', compact('gadget'), ['kategoriList' => $this->kategoriOptions()]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required',
            'kategori' => 'required',
            'deskripsi' => 'required',
            'stock' => 'required|integer|min:0',
            'status' => 'required',
            'tanggal_pembelian' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $gadget = Gadget::where('id', $id)->first();
        $stokLama = (int) $gadget->stock;
        $gadget->update($request->only([
            'nama_produk',
            'kategori',
            'deskripsi',
            'stock',
            'status',
            'tanggal_pembelian',
        ]));

        // Catat jika stok berubah lewat edit
        if ((int) $gadget->stock !== $stokLama) {
            StokLog::create([
                'gadget_id' => $gadget->id,
                'perubahan' => (int) $gadget->stock - $stokLama,
                'stok_sebelum' => $stokLama,
                'stok_sesudah' => (int) $gadget->stock,
                'keterangan' => 'Perubahan data (edit)',
                'user_id' => Auth::id(),
            ]);
        }

        // Handle foto
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('thumbnails', 'public');

            // Delete old foto if exists
            if ($gadget->thumbnail) {
                Storage::disk('public')->delete($gadget->thumbnail->url);
                $gadget->thumbnail->update(['url' => $path]);
            } else {
                GadgetFoto::create([
                    'id' => $gadget->id,
                    'url' => $path,
                ]);
            }
        }

        return redirect()->route('gadget.index')
            ->with('success', 'Data berhasil diubah!');
    }

    public function show($id)
    {
        $gadget = Gadget::where('id', $id)->firstOrFail();
        $stokLogs = $gadget->stokLogs()->with('user')->latest('id')->limit(50)->get();

        return view('gadget.show', compact('gadget', 'stokLogs'));
    }

    public function ubahStok(Request $request, $id, $arah)
    {
        if (! in_array($arah, ['naik', 'turun'])) {
            return back()->withErrors(['stok' => 'Aksi stok tidak valid.']);
        }

        $gadget = Gadget::where('id', $id)->firstOrFail();
        $stokSebelum = (int) $gadget->stock;
        $delta = $arah === 'naik' ? 1 : -1;
        $stockBaru = max(0, $stokSebelum + $delta);

        if ($stokSebelum === $stockBaru) {
            return back()->with('success', "Stok \"{$gadget->nama_produk}\" sudah tidak bisa dikurangi (0).");
        }

        $gadget->stock = $stockBaru;

        if ($gadget->stock === 0 && $gadget->status === 'Tersedia') {
            $gadget->status = 'Habis';
        } elseif ($gadget->stock > 0 && $gadget->status === 'Habis') {
            $gadget->status = 'Tersedia';
        }

        $gadget->save();

        StokLog::create([
            'gadget_id' => $gadget->id,
            'perubahan' => $delta,
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => (int) $gadget->stock,
            'keterangan' => $arah === 'naik' ? 'Stok naik' : 'Stok turun',
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', "Stok \"{$gadget->nama_produk}\" diubah menjadi {$gadget->stock}.");
    }

    public function destroy($id)
    {
        $gadget = Gadget::where('id', $id)->firstOrFail();

        if ($gadget->thumbnail) {
            Storage::disk('public')->delete($gadget->thumbnail->url);
            $gadget->thumbnail->delete();
        }

        $gadget->delete();

        return redirect()->route('gadget.index')
            ->with('success', 'Data sudah dihapus');
    }

    public function export()
    {
        $data = Gadget::query()->orderBy('id')->get();
        $filename = 'daftar-produk-' . date('Y-m-d-His') . '.csv';

        $rows = [[
            'ID', 'Nama Produk', 'Kategori', 'Stok', 'Status', 'Tanggal Pembelian',
        ]];

        foreach ($data as $g) {
            $rows[] = [
                $g->id,
                $g->nama_produk,
                $g->kategori,
                $g->stock,
                $g->status,
                $g->tanggal_pembelian,
            ];
        }

        $out = fopen('php://temp', 'w');
        foreach ($rows as $row) {
            fputcsv($out, $row);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return response("\xEF\xBB\xBF" . $csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function kategoriOptions()
    {
        $defaults = ['SmartPhone', 'Laptop', 'Tablet', 'SmartWatch'];
        $existing = Gadget::query()->distinct()->pluck('kategori')->map(fn ($k) => trim((string) $k))->filter()->all();

        return collect($defaults)->concat($existing)->unique()->sort()->values()->all();
    }
}