@extends('layouts.app')

@section('title', 'Mitra Reseller')

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-navy-800">Manajemen Mitra Reseller</h1>
            <p class="text-sm text-navy-500 mt-1">Verifikasi pendaftaran & kelola status mitra B2B.</p>
        </div>
        <form method="GET" action="{{ route('mitra.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/konter/no HP..."
                   class="w-64 rounded-lg border border-navy-100 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            <button type="submit" class="bg-navy-900 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">Cari</button>
        </form>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        @foreach (['pending' => 'Menunggu', 'approved' => 'Aktif', 'rejected' => 'Ditolak', 'suspended' => 'Tangguhkan'] as $k => $label)
            <a href="{{ route('mitra.index', ['status' => $k, 'search' => request('search')]) }}"
               class="bg-white rounded-2xl border {{ request('status') === $k ? 'border-gold-500 ring-2 ring-gold-500/30' : 'border-navy-100 hover:border-gold-400' }} p-4 transition-colors block">
                <p class="text-[11px] font-bold uppercase tracking-wide text-navy-400">{{ $label }}</p>
                <p class="text-2xl font-black text-navy-900 mt-0.5">{{ $penghitung[$k] }}</p>
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-navy-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">
                <thead>
                    <tr class="bg-navy-50 text-navy-500 text-left">
                        <th class="px-4 py-3 font-medium">Mitra</th>
                        <th class="px-4 py-3 font-medium">Konter / Pemilik</th>
                        <th class="px-4 py-3 font-medium">Kontak</th>
                        <th class="px-4 py-3 font-medium">Alamat</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Terdaftar</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-50">
                    @forelse ($profiles as $profil)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-bold text-navy-800">{{ $profil->user?->name }}</p>
                                <a href="mailto:{{ $profil->user?->email }}" class="text-xs text-navy-400">{{ $profil->user?->email }}</a>
                                @if ($profil->ktp_url)
                                    <a href="{{ $profil->ktp_url }}" target="_blank"
                                       class="block mt-1 text-xs font-bold text-gold-600 hover:text-gold-700">Lihat Foto KTP/Toko ↗</a>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-navy-800">{{ $profil->store_name }}</p>
                                <p class="text-xs text-navy-400">{{ $profil->owner_name }}</p>
                            </td>
                            <td class="px-4 py-3 text-navy-600">
                                <p>{{ $profil->phone }}</p>
                                @if ($profil->whatsapp && $profil->whatsapp !== $profil->phone)
                                    <p class="text-xs text-navy-400">WA: {{ $profil->whatsapp }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-navy-500 max-w-[220px]">{{ $profil->address ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block text-[11px] font-bold rounded-full px-2.5 py-1
                                    {{ $profil->isApproved() ? 'bg-emerald-100 text-emerald-700' : ($profil->status === 'pending' ? 'bg-gold-100 text-gold-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ $profil->status_label }}
                                </span>
                                @if ($profil->notes)
                                    <p class="text-[11px] text-navy-400 mt-1">{{ $profil->notes }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-navy-500">{{ $profil->created_at?->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex flex-wrap gap-1.5 justify-end">
                                    <button type="button"
                                            onclick="setStatus({{ $profil->id }}, 'approved', '{{ addslashes($profil->store_name) }}')"
                                            class="text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg transition-colors">
                                        Setujui
                                    </button>
                                    <button type="button"
                                            onclick="setStatus({{ $profil->id }}, 'suspended', '{{ addslashes($profil->store_name) }}')"
                                            class="text-xs font-bold border border-navy-200 hover:bg-navy-50 text-navy-700 px-3 py-1.5 rounded-lg transition-colors">
                                        Tangguhkan
                                    </button>
                                    <button type="button"
                                            onclick="setStatus({{ $profil->id }}, 'rejected', '{{ addslashes($profil->store_name) }}')"
                                            class="text-xs font-bold border border-rose-200 hover:bg-rose-50 text-rose-600 px-3 py-1.5 rounded-lg transition-colors">
                                        Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-navy-400">Belum ada mitra yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $profiles->links() }}
    </div>

    {{-- Modal konfirmasi --}}
    <div id="modalStatus" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-navy-900/60 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 pt-5 pb-2 border-b border-navy-100">
                <h3 class="font-black text-navy-800">Ubah Status Mitra</h3>
                <p class="text-sm text-navy-500 mt-0.5" id="modalStatusText">—</p>
            </div>
            <form id="formStatus" method="POST" action="{{ route('mitra.status', 0) }}">
                @csrf
                <input type="hidden" name="status" id="statusField">
                <div class="px-6 py-4 space-y-3">
                    <div>
                        <label class="block text-sm font-semibold text-navy-700 mb-1">Catatan (opsional)</label>
                        <input type="text" name="notes" maxlength="255" placeholder="cth: terverifikasi via WA / KTP valid"
                               class="w-full rounded-lg border border-navy-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-navy-100 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modalStatus').classList.add('hidden')"
                            class="border border-navy-200 bg-white hover:bg-navy-50 text-navy-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-navy-900 hover:bg-navy-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                        Simpan Status
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function setStatus(id, status, nama) {
            const label = status === 'approved' ? 'Setujui (Aktif)'
                : status === 'suspended' ? 'Tangguhkan'
                : 'Tolak';
            document.getElementById('modalStatusText').textContent = label + ' mitra "' + nama + '"?';
            document.getElementById('statusField').value = status;
            const form = document.getElementById('formStatus');
            form.action = '{{ url('mitra') }}/' + id + '/status';
            document.getElementById('modalStatus').classList.remove('hidden');
        }
    </script>
@endpush