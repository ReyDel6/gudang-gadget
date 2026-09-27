<?php

namespace App\Http\Controllers;

use App\Models\Gadget;
use App\Models\ServiceTicket;
use App\Models\ServiceTicketItem;
use App\Models\StoreSetting;
use App\Models\User;
use App\Services\BarcodeGenerator;
use App\Services\ServisService;
use App\Support\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ServisController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', '');
        $q = trim((string) $request->input('q', ''));

        $query = ServiceTicket::with(['technician', 'cashier'])->withCount('items');

        if (in_array($status, array_keys(ServiceTicket::STATUS))) {
            $query->where('status', $status);
        }

        if ($q !== '') {
            $query->where(function ($b) use ($q) {
                $b->where('no_tiket', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_phone', 'like', "%{$q}%")
                    ->orWhere('device_model', 'like', "%{$q}%")
                    ->orWhere('imei_or_serial', 'like', "%{$q}%");
            });
        }

        $tikets = $query->latest('created_at')->paginate(20)->withQueryString();

        $ringkasan = collect(ServiceTicket::STATUS)->mapWithKeys(fn ($label, $kode) => [
            $kode => ServiceTicket::where('status', $kode)->count(),
        ]);

        return view('servis.index', compact('tikets', 'status', 'q', 'ringkasan'));
    }

    public function create()
    {
        $teknisi = User::where('role', User::ROLE_TEKNISI)->orderBy('name')->get(['id', 'name']);

        return view('servis.create', compact('teknisi'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:500'],
            'device_brand' => ['required', 'string', 'max:50'],
            'device_model' => ['required', 'string', 'max:100'],
            'imei_or_serial' => ['nullable', 'string', 'max:100'],
            'passcode' => ['nullable', 'string', 'max:50'],
            'completeness' => ['nullable', 'string', 'max:500'],
            'initial_condition' => ['nullable', 'string', 'max:1000'],
            'problem_description' => ['required', 'string', 'max:2000'],
            'technician_id' => ['nullable', 'integer', 'exists:users,id'],
            'down_payment' => ['nullable', 'numeric', 'min:0'],
            'warranty_days' => ['nullable', 'integer', 'min:0', 'max:365'],
        ]);

        $tiket = ServisService::buatTiket(Auth::user(), $data, $request->ip());

        return redirect()->route('servis.show', $tiket->id)
            ->with('success', 'Tiket servis ' . $tiket->no_tiket . ' berhasil dibuat.');
    }

    public function show(ServiceTicket $servis)
    {
        $servis->load(['items.gadget', 'logs.user', 'technician', 'cashier', 'parent', 'children']);

        $teknisi = User::where('role', User::ROLE_TEKNISI)->orderBy('name')->get(['id', 'name']);
        if ($servis->technician_id && ! $teknisi->contains('id', $servis->technician_id)) {
            $teknisi->prepend($servis->technician);
        }

        $spareparts = Gadget::aktif()->where('stock', '>', 0)->orderBy('kategori')->orderBy('nama_produk')
            ->get(['id', 'nama_produk', 'sku', 'kategori', 'harga_beli', 'harga_jual', 'stock', 'satuan']);

        $metode = array_intersect_key(Pembayaran::METODE, array_flip(ServiceTicket::METODE_PELUNASAN));
        $waLinks = $this->waLinks($servis);

        return view('servis.show', compact('servis', 'teknisi', 'spareparts', 'metode', 'waLinks'));
    }

    public function status(Request $request, ServiceTicket $servis)
    {
        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(array_keys(array_diff_key(ServiceTicket::STATUS, [ServiceTicket::STATUS_DIAMBIL => true])))],
            'notes' => ['nullable', 'string', 'max:1000'],
            'technician_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        ServisService::ubahStatus($servis, Auth::user(), $data['status'], $data['notes'] ?? null, $data['technician_id'] ?? null, $request->ip());

        return back()->with('success', 'Status tiket ' . $servis->no_tiket . ' diperbarui menjadi: ' . $servis->status_label . '.');
    }

    public function tambahItem(Request $request, ServiceTicket $servis)
    {
        $data = $request->validate([
            'item_type' => ['required', Rule::in([ServiceTicketItem::TIPE_JASA, ServiceTicketItem::TIPE_SPAREPART])],
            'gadget_id' => ['required_if:item_type,sparepart', 'nullable', 'integer', 'exists:products,id'],
            'item_name' => ['required_if:item_type,service_fee', 'nullable', 'string', 'max:150'],
            'service_name' => ['nullable', 'string', 'max:150'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
            'sell_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $item = ServisService::tambahItem($servis, Auth::user(), $data, $request->ip());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Item berhasil ditambahkan: ' . $item->item_name);
    }

    public function hapusItem(ServiceTicket $servis, ServiceTicketItem $item)
    {
        try {
            ServisService::hapusItem($servis, Auth::user(), $item, request()->ip());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Item berhasil dihapus.');
    }

    public function qc(Request $request, ServiceTicket $servis)
    {
        $data = $request->validate([
            'qc_checklist' => ['nullable', 'array'],
            'qc_checklist.*' => [Rule::in(array_keys(ServiceTicket::QC_ITEMS))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        ServisService::simpanQc($servis, Auth::user(), $data['qc_checklist'] ?? [], $data['notes'] ?? null, $request->ip());

        return back()->with('success', 'Quality check tiket ' . $servis->no_tiket . ' berhasil disimpan.');
    }

    public function lunas(ServiceTicket $servis)
    {
        if ($servis->status === ServiceTicket::STATUS_DIAMBIL) {
            return redirect()->route('servis.show', $servis->id)->with('success', 'Tiket ini sudah dilunasi.');
        }
        if (! in_array($servis->status, [ServiceTicket::STATUS_BARU, ServiceTicket::STATUS_DIAGNOSA, ServiceTicket::STATUS_TUNGGU_PART, ServiceTicket::STATUS_DIKERJAKAN, ServiceTicket::STATUS_SIAP])) {
            return back()->withInput()->withErrors(['lunas' => 'Tiket tidak dapat dilunasi pada status saat ini.']);
        }
        $servis->loadMissing(['items', 'technician']);
        $metode = array_intersect_key(Pembayaran::METODE, array_flip(ServiceTicket::METODE_PELUNASAN));

        return view('servis.lunas', compact('servis', 'metode'));
    }

    public function lunasStore(Request $request, ServiceTicket $servis)
    {
        $data = $request->validate([
            'payment_method' => ['required', Rule::in(ServiceTicket::METODE_PELUNASAN)],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'payment_ref' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            ServisService::pelunasan($servis, Auth::user(), $data['payment_method'], (float) $data['paid_amount'], $data['payment_ref'] ?? null, $request->ip());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('servis.show', $servis->id)
            ->with('success', 'Pelunasan tiket ' . $servis->no_tiket . ' berhasil. Garansi aktif sampai ' . $servis->warranty_until?->format('d M Y') . '.');
    }

    public function garansi(Request $request, ServiceTicket $servis)
    {
        if ($servis->status !== ServiceTicket::STATUS_DIAMBIL) {
            return back()->withErrors(['garansi' => 'Klaim garansi hanya dapat dibuat setelah unit diambil (lunas).']);
        }

        $data = $request->validate([
            'problem_description' => ['required', 'string', 'max:2000'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'device_brand' => ['nullable', 'string', 'max:50'],
            'device_model' => ['nullable', 'string', 'max:100'],
            'warranty_days' => ['nullable', 'integer', 'min:0', 'max:365'],
        ]);

        $tiket = ServisService::buatGaransi($servis, Auth::user(), $data, $request->ip());

        return redirect()->route('servis.show', $tiket->id)
            ->with('success', 'Tiket garansi ' . $tiket->no_tiket . ' berhasil dibuat.');
    }

    public function cetak(ServiceTicket $servis)
    {
        $servis->load(['items', 'technician', 'cashier']);
        $store = [
            'nama' => StoreSetting::get('store_name', 'Gudang Gadget'),
            'alamat' => StoreSetting::get('store_address', ''),
            'no_wa' => StoreSetting::get('whatsapp_number', ''),
        ];
        $barcode = BarcodeGenerator::code39($servis->no_tiket);

        return view('servis.print-slip', compact('servis', 'store', 'barcode'));
    }

    public function cetakLunas(ServiceTicket $servis)
    {
        if ($servis->status !== ServiceTicket::STATUS_DIAMBIL) {
            abort(409, 'Tiket belum lunas. Silakan lakukan pelunasan terlebih dahulu.');
        }
        $servis->load(['items', 'technician', 'cashier']);
        $store = [
            'nama' => StoreSetting::get('store_name', 'Gudang Gadget'),
            'alamat' => StoreSetting::get('store_address', ''),
            'no_wa' => StoreSetting::get('whatsapp_number', ''),
        ];

        return view('servis.print-lunas', compact('servis', 'store'));
    }

    protected function waLinks(ServiceTicket $servis): array
    {
        $number = preg_replace('/[^0-9]/', '', (string) StoreSetting::get('whatsapp_number', '6281234567890'));
        $unit = $servis->device_brand . ' ' . $servis->device_model;
        $store = StoreSetting::get('store_name', 'Gudang Gadget');

        $texts = [
            'tanda_terima' => "Halo {$servis->customer_name},\n\nTerima kasih telah menitipkan servis di {$store}.\n\n" .
                "Nomor Tiket: {$servis->no_tiket}\nUnit: {$unit}\nKeluhan: {$servis->problem_description}\n" .
                "Status: Unit Diterima (pending). Perkembangan servis dapat dipantau di https://gudanggadget.online/shop/tracking-service\n\nTerima kasih.",
            'konfirmasi_biaya' => "Halo {$servis->customer_name},\n\nUntuk tiket servis No. {$servis->no_tiket} ({$unit}), kami sampaikan hasil pemeriksaan dan perkiraan biaya.\n" .
                "Mohon konfirmasi izin pengerjaan via WhatsApp ini (*bagus kantor/resmi*). Terima kasih.",
            'siap_diambil' => "Halo {$servis->customer_name},\n\nUnit Anda sudah SELESAI diperbaiki dan siap diambil di toko.\n" .
                "Nomor Tiket: {$servis->no_tiket}\nUnit: {$unit}\n\nSilakan datang ke {$store} untuk pembayaran & pengambilan. Terima kasih!",
        ];

        return array_map(fn ($t) => 'https://wa.me/' . $number . '/?text=' . rawurlencode($t), $texts);
    }
}