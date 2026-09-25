<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustRequest;
use App\Models\Gadget;
use App\Models\StokLog;
use App\Services\StokService;
use Illuminate\Http\Request;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $query = StokLog::with('gadget', 'user');

        if ($tipe = $request->input('tipe')) {
            $query->where('tipe', $tipe);
        }
        if ($search = trim((string) $request->input('search'))) {
            $query->whereHas('gadget', function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%");
            });
        }
        $query->periode($request->input('tanggal_awal'), $request->input('tanggal_akhir'));

        $logs = $query->latest('id')->paginate(20)->withQueryString();

        return view('mutasi.index', compact('logs'));
    }

    public function create()
    {
        $products = Gadget::orderBy('nama_produk')->get(['id', 'nama_produk', 'sku', 'stock', 'satuan']);

        return view('mutasi.create', compact('products'));
    }

    public function store(StockAdjustRequest $request)
    {
        $gadget = Gadget::findOrFail($request->integer('gadget_id'));
        $jenis = $request->input('jenis');
        $qty = $request->integer('qty');
        $alasan = $request->input('alasan');

        $tipe = match ($jenis) {
            'Penyesuaian (+)' => StokLog::TIPE_PENYESUAIAN,
            'Penyesuaian (-)' => StokLog::TIPE_PENYESUAIAN,
            default => $jenis,
        };
        $delta = in_array($jenis, ['Pengeluaran', 'Penyesuaian (-)'], true) ? -$qty : $qty;

        StokService::adjust($gadget, $tipe, $delta, $alasan);

        return redirect()->route('mutasi.index')
            ->with('success', "Mutasi \"{$jenis}\" {$qty} {$gadget->satuan} pada \"{$gadget->nama_produk}\" berhasil dicatat.");
    }
}