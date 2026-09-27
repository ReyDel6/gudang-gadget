<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\TradeInMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TradeInMasterController extends Controller
{
    public function index(Request $request)
    {
        $query = TradeInMaster::query()->orderBy('brand')->orderBy('model_name');

        if ($cari = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($cari) {
                $q->where('brand', 'like', "%{$cari}%")
                    ->orWhere('model_name', 'like', "%{$cari}%")
                    ->orWhere('capacity', 'like', "%{$cari}%");
            });
        }

        $matriks = $query->paginate(25)->withQueryString();

        return view('trade-in.index', compact('matriks'));
    }

    public function create()
    {
        return view('trade-in.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'brand' => ['required', 'string', 'max:50'],
            'model_name' => ['required', 'string', 'max:100'],
            'capacity' => ['nullable', 'string', 'max:50'],
            'base_price_grade_a' => ['required', 'numeric', 'min:0'],
            'price_grade_b' => ['required', 'numeric', 'min:0'],
            'price_grade_c' => ['required', 'numeric', 'min:0'],
            'price_grade_d' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $row = TradeInMaster::create([
            ...$data,
            'base_price_grade_a' => $data['base_price_grade_a'],
            'price_grade_b' => $data['price_grade_b'],
            'price_grade_c' => $data['price_grade_c'],
            'price_grade_d' => $data['price_grade_d'],
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLog::catat(Auth::user(), 'tambah matriks trade-in', $row, [
            'brand' => $row->brand,
            'model_name' => $row->model_name,
        ], $request->ip());

        return redirect()->route('trade-in.index')->with('success', 'Matriks taksiran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $row = TradeInMaster::findOrFail($id);

        return view('trade-in.edit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $row = TradeInMaster::findOrFail($id);

        $data = $request->validate([
            'brand' => ['required', 'string', 'max:50'],
            'model_name' => ['required', 'string', 'max:100'],
            'capacity' => ['nullable', 'string', 'max:50'],
            'base_price_grade_a' => ['required', 'numeric', 'min:0'],
            'price_grade_b' => ['required', 'numeric', 'min:0'],
            'price_grade_c' => ['required', 'numeric', 'min:0'],
            'price_grade_d' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $row->update([
            ...$data,
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLog::catat(Auth::user(), 'ubah matriks trade-in', $row, [
            'brand' => $row->brand,
            'model_name' => $row->model_name,
        ], $request->ip());

        return redirect()->route('trade-in.index')->with('success', 'Matriks taksiran diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $row = TradeInMaster::findOrFail($id);

        AuditLog::catat(Auth::user(), 'hapus matriks trade-in', $row, [
            'brand' => $row->brand,
            'model_name' => $row->model_name,
        ], $request->ip());

        $row->delete();

        return redirect()->route('trade-in.index')->with('success', 'Matriks taksiran dihapus.');
    }
}