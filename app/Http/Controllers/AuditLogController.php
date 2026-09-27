<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query();

        if ($kata = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($kata) {
                $q->where('user_name', 'like', "%{$kata}%")
                    ->orWhere('aksi', 'like', "%{$kata}%")
                    ->orWhere('model_type', 'like', "%{$kata}%");
            });
        }
        if ($aksi = $request->input('aksi')) {
            $query->where('aksi', $aksi);
        }
        if ($tanggal = $request->input('tanggal')) {
            $query->whereDate('created_at', $tanggal);
        }

        $logs = $query->latest('id')->paginate(20)->withQueryString();

        return view('audit.index', compact('logs'));
    }
}