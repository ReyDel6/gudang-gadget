<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Gadget;
use App\Models\Kategori;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterController extends Controller
{
    public function kategori(Request $request)
    {
        $kategoriList = Kategori::orderBy('nama')
            ->withCount(['produk' => fn ($q) => $q->whereNull('deleted_at')])
            ->paginate(15)->withQueryString();

        // Kategori yang terpakai di produk tapi belum di daftar master.
        $orphan = Gadget::whereNull('deleted_at')
            ->distinct()->pluck('kategori')
            ->map(fn ($k) => trim((string) $k))->filter()
            ->filter(fn ($k) => ! Kategori::where('nama', $k)->exists())
            ->values()->all();

        return view('master.kategori', compact('kategoriList', 'orphan'));
    }

    public function storeKategori(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('categories', 'nama')],
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ]);

        $kategori = Kategori::create($data);
        AuditLog::catat($request->user(), 'tambah kategori', $kategori, ['nama' => $kategori->nama]);

        return back()->with('success', "Kategori \"{$kategori->nama}\" ditambahkan.");
    }

    public function destroyKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        if (Gadget::where('kategori', $kategori->nama)->exists()) {
            return back()->withErrors(['nama' => "Kategori \"{$kategori->nama}\" masih dipakai produk dan tidak bisa dihapus. Ganti kategori produk terlebih dahulu atau biarkan saja."]);
        }

        AuditLog::catat($request->user(), 'hapus kategori', $kategori, ['nama' => $kategori->nama]);
        $kategori->delete();

        return back()->with('success', "Kategori \"{$kategori->nama}\" dihapus dari daftar.");
    }

    public function supplier(Request $request)
    {
        $suppliers = Supplier::orderBy('nama')->paginate(15)->withQueryString();

        $orphan = Gadget::whereNull('deleted_at')
            ->distinct()->pluck('supplier')
            ->map(fn ($s) => trim((string) $s))->filter()
            ->filter(fn ($s) => ! Supplier::where('nama', $s)->exists())
            ->values()->all();

        return view('master.supplier', compact('suppliers', 'orphan'));
    }

    public function storeSupplier(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150', Rule::unique('suppliers', 'nama')],
            'kontak' => ['nullable', 'string', 'max:100'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'email', 'max:150'],
            'alamat' => ['nullable', 'string', 'max:255'],
        ]);

        $supplier = Supplier::create($data);
        AuditLog::catat($request->user(), 'tambah supplier', $supplier, ['nama' => $supplier->nama]);

        return back()->with('success', "Supplier \"{$supplier->nama}\" ditambahkan.");
    }

    public function destroySupplier(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        if (Gadget::where('supplier', $supplier->nama)->exists()) {
            return back()->withErrors(['nama' => "Supplier \"{$supplier->nama}\" masih dipakai produk dan tidak bisa dihapus."]);
        }

        AuditLog::catat($request->user(), 'hapus supplier', $supplier, ['nama' => $supplier->nama]);
        $supplier->delete();

        return back()->with('success', "Supplier \"{$supplier->nama}\" dihapus dari daftar.");
    }
}