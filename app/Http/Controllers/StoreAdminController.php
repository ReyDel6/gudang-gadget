<?php

namespace App\Http\Controllers;

use App\Models\Gadget;
use App\Models\StoreBanner;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class StoreAdminController extends Controller
{
    public function banners()
    {
        $banners = StoreBanner::orderBy('order_position')->get();

        return view('store.admin.banners', compact('banners'));
    }

    public function bannerStore(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'subtitle' => ['nullable', 'string', 'max:180'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'cta_link' => ['nullable', 'string', 'max:255'],
            'order_position' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = 'thumbnails/no-image.jpg';
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
            $imagePath = str_replace('public/', '', $imagePath);
        }

        StoreBanner::create([
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'image_path' => $imagePath,
            'cta_link' => $data['cta_link'] ?? null,
            'order_position' => $data['order_position'] ?? 0,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return redirect()->route('store.banner.index')->with('success', 'Banner promo ditambahkan.');
    }

    public function bannerUpdate(Request $request, $id)
    {
        $banner = StoreBanner::findOrFail($id);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'subtitle' => ['nullable', 'string', 'max:180'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'cta_link' => ['nullable', 'string', 'max:255'],
            'order_position' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'cta_link' => $data['cta_link'] ?? null,
            'order_position' => $data['order_position'] ?? 0,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
            $payload['image_path'] = str_replace('public/', '', $imagePath);
        }
        $banner->update($payload);

        return redirect()->route('store.banner.index')->with('success', 'Banner diperbarui.');
    }

    public function bannerDestroy($id)
    {
        StoreBanner::findOrFail($id)->delete();

        return redirect()->route('store.banner.index')->with('success', 'Banner dihapus.');
    }

    public function settings()
    {
        $view = $this->settingsForView();

        return view('store.admin.settings', $view);
    }

    public function settingsStore(Request $request)
    {
        $rules = [
            'store_name' => ['required', 'string', 'max:100'],
            'whatsapp_number' => ['required', 'regex:/^[0-9+ ]+$/u', 'max:20'],
            'store_address' => ['nullable', 'string', 'max:255'],
            'jam_operasional' => ['nullable', 'string', 'max:255'],
            'maps_embed' => ['nullable', 'string', 'max:1000'],
            'store_photo' => ['nullable', 'string', 'max:255'],
            'tags_line' => ['nullable', 'string', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'tiktok_url' => ['nullable', 'url', 'max:255'],
            'payment_transfer_enabled' => ['nullable', 'in:0,1'],
            'payment_transfer_info' => ['nullable', 'string', 'max:1000'],
            'payment_cod_enabled' => ['nullable', 'in:0,1'],
            'payment_store_enabled' => ['nullable', 'in:0,1'],
        ];
        $data = $request->validate($rules);

        foreach ($data as $key => $value) {
            StoreSetting::set($key, $value);
        }

        return redirect()->route('store.settings')->with('success', 'Profil toko diperbarui.');
    }

    public function toggleGadget(Request $request, $id)
    {
        $data = $request->validate([
            'field' => ['required', 'in:is_published,is_featured'],
        ]);

        $gadget = Gadget::findOrFail($id);
        $current = (bool) $gadget->{$data['field']};
        $gadget->update([$data['field'] => ! $current]);

        $label = $data['field'] === 'is_published' ? 'publikasi produk' : 'status unggulan produk';

        return back()->with('success', "Status {$label} diperbarui.");
    }

    public static function defaultSettings(): array
    {
        return [
            'store_name' => ['label' => 'Nama Toko', 'type' => 'text', 'hint' => 'Muncul di navbar, footer, dan template chat WhatsApp.'],
            'whatsapp_number' => ['label' => 'Nomor WhatsApp (CS)', 'type' => 'text', 'hint' => 'Format internasional tanpa tanda +, contoh: 6281234567890.'],
            'store_address' => ['label' => 'Alamat Toko', 'type' => 'text', 'hint' => 'Ditampilkan di footer & halaman kontak.'],
            'jam_operasional' => ['label' => 'Jam Operasional', 'type' => 'text', 'hint' => 'Contoh: Senin–Sabtu 09.00–21.00.'],
            'maps_embed' => ['label' => 'Embed Google Maps', 'type' => 'textarea', 'hint' => 'URL iframe embed maps (share → embed).'],
            'store_photo' => ['label' => 'Foto Etalase Toko', 'type' => 'text', 'hint' => 'Path/URL gambar, contoh: storage/foto-toko.jpg. Kosongkan jika belum ada.'],
            'tags_line' => ['label' => 'Tagline Etalase', 'type' => 'text', 'hint' => 'Contoh: Original, Garansi Resmi.'],
            'instagram_url' => ['label' => 'Instagram', 'type' => 'url', 'hint' => 'Tautan profil (kosongkan jika tidak ada).'],
            'facebook_url' => ['label' => 'Facebook', 'type' => 'url', 'hint' => 'Tautan profil (kosongkan jika tidak ada).'],
            'tiktok_url' => ['label' => 'TikTok', 'type' => 'url', 'hint' => 'Tautan profil (kosongkan jika tidak ada).'],
            'payment_transfer_enabled' => ['label' => 'Pembayaran Transfer Bank', 'type' => 'select', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif'], 'hint' => 'Tampilkan metode transfer di checkout.'],
            'payment_transfer_info' => ['label' => 'Info Rekening Transfer', 'type' => 'textarea', 'hint' => 'Contoh: BCA 1234567890 a.n. Gudang Gadget / Mandiri 9876543210 a.n. Gudang Gadget.'],
            'payment_cod_enabled' => ['label' => 'Pembayaran COD (Bayar di Tempat)', 'type' => 'select', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif'], 'hint' => 'Tampilkan metode COD di checkout.'],
            'payment_store_enabled' => ['label' => 'Bayar di Toko', 'type' => 'select', 'options' => ['1' => 'Aktif', '0' => 'Nonaktif'], 'hint' => 'Tampilkan metode bayar langsung di toko fisik.'],
        ];
    }

    // Re-export helper yang dipakai view.
    public function settingsForView(): array
    {
        $fields = static::defaultSettings();
        $values = collect($fields)->mapWithKeys(fn ($f, $key) => [$key => StoreSetting::get($key, '')])->all();

        return ['fields' => $fields, 'values' => $values];
    }
}