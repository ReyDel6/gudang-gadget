<?php

namespace App\Http\Controllers;

use App\Models\Gadget;
use App\Models\Penjualan;
use App\Services\PenjualanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with('items');

        if ($cari = trim((string) $request->input('search'))) {
            $query->where('no_invoice', 'like', "%{$cari}%")
                ->orWhere('customer', 'like', "%{$cari}%")
                ->orWhere('customer_phone', 'like', "%{$cari}%");
        }
        $query->periode($request->input('tanggal_awal'), $request->input('tanggal_akhir'));

        $penjualans = $query->latest('id')->paginate(15)->withQueryString();

        return view('penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        $products = Gadget::orderBy('nama_produk')->get(['id', 'nama_produk', 'sku', 'harga_beli', 'harga_jual', 'stock', 'satuan']);
        $shiftAktif = Auth::user()
            ? \App\Models\CashierShift::aktif()->where('user_id', Auth::id())->first()
            : null;

        return view('penjualan.create', compact('products', 'shiftAktif'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(PenjualanService::aturan(), PenjualanService::pesan());

        $user = Auth::user();
        $shiftAktif = \App\Models\CashierShift::aktif()->where('user_id', $user->id)->first();

        try {
            $penjualan = PenjualanService::buat($data, $user, $request->ip(), $shiftAktif?->id);
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('penjualan.show', $penjualan->id)
            ->with('success', "Penjualan {$penjualan->no_invoice} berhasil dicatat.");
    }

    public function show($id)
    {
        $penjualan = Penjualan::with('items.gadget')->findOrFail($id);

        return view('penjualan.show', compact('penjualan'));
    }

    public function cetak($id)
    {
        $penjualan = Penjualan::with('items')->findOrFail($id);

        return view('penjualan.print', compact('penjualan'));
    }

    /**
     * Void / retur. Seluruh transaksi yang sudah tercatat wajib otorisasi
     * password admin sebelum stok dikembalikan ke gudang.
     */
    public function destroy(Request $request, $id)
    {
        $data = $request->validate([
            'password' => ['required', 'string'],
            'void_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        if (! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Password admin salah. Transaksi tidak dibatalkan.',
            ]);
        }

        $penjualan = Penjualan::findOrFail($id);
        PenjualanService::void($penjualan, $user, $request->ip(), $data['void_reason'] ?? null);

        return redirect()->route('penjualan.index')
            ->with('success', "Penjualan \"{$penjualan->no_invoice}\" dibatalkan dan stok dikembalikan.");
    }
}