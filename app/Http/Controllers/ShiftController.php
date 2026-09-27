<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function index()
    {
        $shiftAktif = CashierShift::aktif()->where('user_id', Auth::id())->first();
        $shifts = CashierShift::where('user_id', Auth::id())
            ->orderByDesc('opened_at')
            ->limit(50)
            ->get();

        return view('shift.index', compact('shiftAktif', 'shifts'));
    }

    public function buka(Request $request)
    {
        $data = $request->validate([
            'start_cash' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        if (CashierShift::aktif()->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['shift' => 'Anda masih punya shift yang belum ditutup.']);
        }

        CashierShift::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'start_cash' => (float) ($data['start_cash'] ?? 0),
            'notes' => $data['notes'] ?? null,
            'opened_at' => now(),
        ]);

        return redirect()->route('shift.index')->with('success', 'Shift kasir dibuka.');
    }

    public function tutup(Request $request, $id)
    {
        $data = $request->validate([
            'actual_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $shift = CashierShift::where('id', $id)->where('user_id', $user->id)->aktif()->firstOrFail();

        $omzet = (float) $shift->omzet;
        $endCash = round((float) $shift->start_cash + $omzet, 2);
        $difference = round((float) $data['actual_cash'] - $endCash, 2);

        $shift->update([
            'end_cash' => $endCash,
            'actual_cash' => (float) $data['actual_cash'],
            'difference' => $difference,
            'notes' => $shift->notes . ($data['notes'] ? PHP_EOL . $data['notes'] : ''),
            'closed_at' => now(),
        ]);

        return redirect()->route('shift.index')
            ->with('success', 'Shift ditutup. Selisih: ' . number_format($difference, 0, ',', '.'));
    }
}