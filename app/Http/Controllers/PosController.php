<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Gadget;
use App\Services\PenjualanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function index()
    {
        $products = Gadget::query()
            ->with(['tierPrices' => fn ($q) => $q->orderBy('min_qty')])
            ->orderBy('nama_produk')
            ->get(['id', 'nama_produk', 'sku', 'harga_beli', 'harga_jual', 'stock', 'kategori', 'satuan']);

        $shiftAktif = CashierShift::aktif()->where('user_id', Auth::id())->first();

        return view('pos.index', compact('products', 'shiftAktif'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(PenjualanService::aturan(), PenjualanService::pesan());

        $user = Auth::user();
        $shiftAktif = CashierShift::aktif()->where('user_id', $user->id)->first();

        try {
            $penjualan = PenjualanService::buat($data, $user, $request->ip(), $shiftAktif?->id);
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('penjualan.show', $penjualan->id)
            ->with('success', "Penjualan {$penjualan->no_invoice} berhasil. Kembalian Rp " . number_format($penjualan->kembalian, 0, ',', '.') . '.');
    }
}