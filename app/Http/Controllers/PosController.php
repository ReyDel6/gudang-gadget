<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Gadget;
use App\Models\GadgetImei;
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
            ->with(['imeis' => fn ($q) => $q->status(GadgetImei::STA_AVAILABLE)->orderBy('imei')])
            ->orderBy('nama_produk')
            ->get(['id', 'nama_produk', 'sku', 'harga_beli', 'harga_jual', 'stock', 'kategori', 'satuan']);

        $matriksTradeIn = \App\Models\TradeInMaster::aktif()->orderBy('brand')->orderBy('model_name')->get();

        $shiftAktif = CashierShift::aktif()->where('user_id', Auth::id())->first();

        return view('pos.index', compact('products', 'shiftAktif', 'matriksTradeIn'));
    }

    public function store(Request $request)
    {
        $rules = array_merge(PenjualanService::aturan(), PenjualanService::aturanTambahan($request->all()));
        $data = $request->validate($rules, PenjualanService::pesan());

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