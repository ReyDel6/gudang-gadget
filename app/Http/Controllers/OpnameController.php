<?php

namespace App\Http\Controllers;

use App\Models\Gadget;
use App\Models\StokOpname;
use App\Services\OpnameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OpnameController extends Controller
{
    public function index(Request $request)
    {
        $query = StokOpname::with('auditor', 'items')->latest('id');

        $query->status($request->input('status'));

        $opnames = $query->paginate(15)->withQueryString();

        $kategori = Gadget::query()->whereNotNull('kategori')->distinct()->orderBy('kategori')->pluck('kategori');

        return view('opname.index', compact('opnames', 'kategori'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_filter' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $opname = OpnameService::buka(Auth::user(), $data['category_filter'] ?: null, $data['notes'] ?: null);
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('opname.show', $opname->id)
            ->with('success', "Opname {$opname->no_invoice} dibuka. Mulai scan SKU unit fisik.");
    }

    public function show(Request $request, $id)
    {
        $opname = StokOpname::with(['items.gadget', 'auditor'])->findOrFail($id);

        $items = $opname->items()->with('gadget')
            ->orderByRaw('physical_stock IS NULL ASC')
            ->orderBy('gadget_id')
            ->paginate(50)
            ->withQueryString();

        $terhitung = $opname->items()->whereNotNull('physical_stock')->count();

        return view('opname.show', compact('opname', 'items', 'terhitung'));
    }

    public function scan(Request $request, $id)
    {
        $opname = StokOpname::findOrFail($id);

        $sku = (string) $request->input('sku');

        try {
            $hasil = OpnameService::scan($opname, $sku);
        } catch (ValidationException $e) {
            return response()->json(['ok' => false, 'message' => $e->errors()['sku'][0] ?? 'Gagal scan.'], 422);
        }

        return response()->json($hasil);
    }

    public function selesai(Request $request, $id)
    {
        $opname = StokOpname::findOrFail($id);

        $notes = $request->validate(['notes' => ['nullable', 'string', 'max:255']])['notes'] ?? null;

        try {
            OpnameService::selesai($opname, $notes);
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('opname.index')->with('success', "Opname {$opname->no_invoice} selesai. Selisih disesuaikan ke gudang & tercatat di mutasi.");
    }

    public function batal($id)
    {
        $opname = StokOpname::findOrFail($id);

        try {
            OpnameService::batal($opname);
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('opname.index')->with('success', "Opname {$opname->no_invoice} dibatalkan.");
    }

    public function cetak($id)
    {
        $opname = StokOpname::with(['items.gadget', 'auditor'])->findOrFail($id);

        return view('opname.cetak', compact('opname'));
    }
}