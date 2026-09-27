<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ResellerProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MitraAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = ResellerProfile::with('user');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($cari = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($cari) {
                $q->where('store_name', 'like', "%{$cari}%")
                    ->orWhere('owner_name', 'like', "%{$cari}%")
                    ->orWhere('phone', 'like', "%{$cari}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$cari}%")->orWhere('email', 'like', "%{$cari}%"));
            });
        }

        $profiles = $query->latest('id')->paginate(15)->withQueryString();
        $penghitung = [
            'pending' => ResellerProfile::where('status', ResellerProfile::STATUS_PENDING)->count(),
            'approved' => ResellerProfile::where('status', ResellerProfile::STATUS_APPROVED)->count(),
            'rejected' => ResellerProfile::where('status', ResellerProfile::STATUS_REJECTED)->count(),
            'suspended' => ResellerProfile::where('status', ResellerProfile::STATUS_SUSPENDED)->count(),
        ];

        return view('mitra-admin.index', compact('profiles', 'penghitung'));
    }

    public function setStatus(Request $request, $id)
    {
        $status = $request->validate([
            'status' => ['required', 'string', Rule::in([
                ResellerProfile::STATUS_APPROVED,
                ResellerProfile::STATUS_REJECTED,
                ResellerProfile::STATUS_SUSPENDED,
            ])],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $profil = ResellerProfile::findOrFail($id);
        $sebelum = $profil->status;
        $profil->status = $status['status'];
        if (isset($status['notes'])) {
            $profil->notes = $status['notes'];
        }
        $profil->save();

        AuditLog::catat($request->user(), 'ubah status mitra', $profil, [
            'mitra' => $profil->store_name,
            'status_sebelum' => $sebelum,
            'status_baru' => $status['status'],
        ]);

        $label = $profil->status_label;

        return back()->with('success', "Status mitra \"{$profil->store_name}\" diperbarui menjadi {$label}.");
    }
}