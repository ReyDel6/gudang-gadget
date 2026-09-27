<?php

namespace App\Http\Controllers;

use App\Models\Gadget;
use App\Models\ResellerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MitraController extends Controller
{
    protected array $publicColumns = [
        'id', 'nama_produk', 'sku', 'kategori', 'deskripsi', 'harga_jual',
        'harga_promo', 'stock', 'satuan', 'status', 'is_published', 'is_featured',
        'condition', 'specifications', 'warranty_info', 'created_at', 'updated_at',
    ];

    public function daftar()
    {
        $settings = (new StorefrontController)->settings();

        return view('mitra.daftar', compact('settings'));
    }

    public function daftarStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'store_name' => ['required', 'string', 'max:150'],
            'owner_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'ktp_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $ktpPath = null;
        if ($request->hasFile('ktp_photo')) {
            $ktpPath = $request->file('ktp_photo')->store('reseller', 'public');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => User::ROLE_RESELLER,
        ]);

        $user->reseller()->create([
            'store_name' => $data['store_name'],
            'owner_name' => $data['owner_name'],
            'phone' => $data['phone'],
            'whatsapp' => $data['whatsapp'] ?: $data['phone'],
            'address' => $data['address'],
            'ktp_path' => $ktpPath,
            'status' => ResellerProfile::STATUS_PENDING,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('shop.mitra.beranda')
            ->with('success', 'Pendaftaran mitra berhasil. Admin akan memverifikasi akun Anda segera.');
    }

    public function masuk()
    {
        $settings = (new StorefrontController)->settings();

        return view('mitra.masuk', compact('settings'));
    }

    public function masukStore(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $user = Auth::user();
        if (! $user->isReseller()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors(['email' => 'Akun ini bukan akun mitra reseller.']);
        }

        $request->session()->regenerate();

        return redirect()->route('shop.mitra.beranda')
            ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    public function keluar(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shop.home')
            ->with('success', 'Anda telah keluar dari portal mitra.');
    }

    public const DEMO_EMAIL = 'demo.mitra@gudanggadget.com';

    public function demoMasuk(Request $request)
    {
        $user = User::query()->where('email', self::DEMO_EMAIL)->first();

        if (! $user) {
            $user = User::create([
                'name' => 'Mitra Demo',
                'email' => self::DEMO_EMAIL,
                'password' => Str::random(24),
                'role' => User::ROLE_RESELLER,
            ]);
        }

        if ($user->role !== User::ROLE_RESELLER) {
            $user->update(['role' => User::ROLE_RESELLER]);
        }

        if (! $user->reseller) {
            $user->reseller()->create([
                'store_name' => 'Toko Gadget Demo',
                'owner_name' => 'Owner Demo',
                'phone' => '081234567899',
                'whatsapp' => '081234567899',
                'address' => 'Jl. Demo No. 1, Indonesia',
                'status' => ResellerProfile::STATUS_APPROVED,
            ]);
        } elseif ($user->reseller->status !== ResellerProfile::STATUS_APPROVED) {
            $user->reseller->update(['status' => ResellerProfile::STATUS_APPROVED]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('shop.mitra.beranda')
            ->with('success', 'Login akun demo berhasil. Anda kini melihat semua fitur portal mitra.');
    }

    public function beranda()
    {
        $user = Auth::user();
        $profil = $user->reseller;
        $settings = (new StorefrontController)->settings();
        $produkReady = Gadget::publik()->where('stock', '>', 0)->count();
        $produkTier = Gadget::publik()->whereHas('tierPrices', fn ($q) => $q->where('price', '>', 0))->count();

        return view('mitra.beranda', compact('user', 'profil', 'settings', 'produkReady', 'produkTier'));
    }

    public function priceList()
    {
        $this->pastikanAktif();

        $products = $this->ambilProduk();
        $settings = (new StorefrontController)->settings();

        return view('mitra.price-list', compact('products', 'settings'));
    }

    public function priceListCsv(Request $request)
    {
        $this->pastikanAktif();

        $products = $this->ambilProduk();

        $nama = 'price-list-mitra-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($products) {
            $keluar = fopen('php://output', 'w');
            fputcsv($keluar, [
                'SKU', 'Nama Produk', 'Kategori', 'Harga Retail',
                'Harga Mitra (Reseller)', 'Satuan', 'Stok', 'Tier Grosir / Partai',
            ]);

            foreach ($products as $p) {
                $tierBaris = $p->tierPrices
                    ->sortBy('min_qty')
                    ->map(fn ($t) => "{$t->tier_name} (min {$t->min_qty} pcs): Rp " . number_format((float) $t->price, 0, ',', '.'))
                    ->implode(' | ');

                fputcsv($keluar, [
                    $p->sku,
                    $p->nama_produk,
                    $p->kategori,
                    number_format($p->harga_aktif, 0, ',', '.'),
                    number_format($p->harga_mitra, 0, ',', '.'),
                    $p->satuan,
                    (int) $p->stock,
                    $tierBaris,
                ]);
            }

            fclose($keluar);
        }, $nama, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function priceListPrint()
    {
        $this->pastikanAktif();

        $products = $this->ambilProduk();
        $settings = (new StorefrontController)->settings();
        $tanggal = now();

        return view('mitra.price-list-cetak', compact('products', 'settings', 'tanggal'));
    }

    protected function pastikanAktif(): void
    {
        abort_unless(
            Auth::user()?->reseller?->status === ResellerProfile::STATUS_APPROVED,
            403,
            'Akun mitra Anda belum diverifikasi oleh admin.'
        );
    }

    protected function ambilProduk()
    {
        return Gadget::publik()
            ->with(['tierPrices' => fn ($q) => $q->orderBy('min_qty')])
            ->where('stock', '>', 0)
            ->orderBy('kategori')
            ->orderBy('nama_produk')
            ->get($this->publicColumns);
    }
}