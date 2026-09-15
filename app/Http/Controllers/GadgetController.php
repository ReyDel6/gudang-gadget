<?php

namespace App\Http\Controllers;

use App\Models\Gadget;
use App\Models\GadgetFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class GadgetController extends Controller
{
    public function landing()
    {
        $totalProduk = Gadget::count();
        $totalStock = (int) Gadget::sum('stock');
        $stokRendah = Gadget::where('status', 'Habis')->count();
        $kategori = Gadget::select('kategori')->distinct()->count();
        $recent = Gadget::latest()->take(5)->get();
        $perKategori = Gadget::select('kategori', \Illuminate\Support\Facades\DB::raw('COUNT(*) as jumlah'), \Illuminate\Support\Facades\DB::raw('SUM(stock) as stok'))
            ->groupBy('kategori')
            ->get();

        $chartData = [
            'categories' => $perKategori->map(fn ($c) => [
                'kategori' => $c->kategori,
                'stok' => (int) $c->stok,
                'jumlah' => (int) $c->jumlah,
            ])->values(),
        ];

        return view('landing', compact(
            'totalProduk', 'totalStock', 'stokRendah', 'kategori', 'recent', 'chartData'
        ));
    }

    public function create()
    {
        return view('gadget.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'kategori' => 'required',
            'deskripsi' => 'required',
            'stock' => 'required',
            'status' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $gadget = Gadget::create($request->only([
            'nama_produk',
            'kategori',
            'deskripsi',
            'stock',
            'status',
        ]));

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
        $data = Gadget::latest()->get();
        $kategoriList = Gadget::query()->distinct()->pluck('kategori')->sort()->values();

        return view('gadget.index', compact('data', 'kategoriList'));
    }

    public function edit($id)
    {
        $gadget = Gadget::where('id', $id)->first();
        return view('gadget.edit', compact('gadget'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required',
            'kategori' => 'required',
            'deskripsi' => 'required',
            'stock' => 'required',
            'status' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $gadget = Gadget::where('id', $id)->first();
        $gadget->update($request->only([
            'nama_produk',
            'kategori',
            'deskripsi',
            'stock',
            'status',
        ]));

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

        return view('gadget.show', compact('gadget'));
    }

    public function ubahStok(Request $request, $id, $arah)
    {
        $gadget = Gadget::where('id', $id)->firstOrFail();
        $delta = $arah === 'naik' ? 1 : -1;
        $gadget->stock = max(0, (int) $gadget->stock + $delta);

        if ($gadget->stock === 0 && $gadget->status === 'Tersedia') {
            $gadget->status = 'Habis';
        } elseif ($gadget->stock > 0 && $gadget->status === 'Habis') {
            $gadget->status = 'Tersedia';
        }

        $gadget->save();

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
}
