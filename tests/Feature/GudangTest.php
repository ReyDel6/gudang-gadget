<?php

namespace Tests\Feature;

use App\Models\Gadget;
use App\Models\StokLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class GudangTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->admin()->create();
    }

    protected function staff(): User
    {
        return User::factory()->create();
    }

    protected function makeGadget(array $overrides = []): Gadget
    {
        return Gadget::create(array_merge([
            'nama_produk' => 'Gadget Uji',
            'kategori' => 'SmartPhone',
            'stock' => 5,
            'status' => 'Tersedia',
            'satuan' => 'pcs',
        ], $overrides));
    }

    public function test_guest_di_redirect_ke_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/gadget')->assertRedirect(route('login'));
    }

    public function test_login_berhasil_dan_gagal(): void
    {
        $admin = $this->admin();

        $this->post(route('login'), ['email' => $admin->email, 'password' => 'salah'])
            ->assertSessionHasErrors('email');

        $this->post(route('login'), ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('landing'));
    }

    public function test_staff_tidak_bisa_akses_manajemen_pengguna(): void
    {
        $staff = $this->staff();

        $this->actingAs($staff)->get(route('user.index'))->assertForbidden();
        $this->actingAs($staff)->delete(route('user.destroy', $this->admin()->id))->assertForbidden();
    }

    public function test_admin_bisa_akses_manajemen_pengguna(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('user.index'))->assertOk();
    }

    public function test_admin_tidak_bisa_hapus_diri_sendiri(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->delete(route('user.destroy', $admin->id))
            ->assertSessionHasErrors();
    }

    public function test_admin_bisa_hapus_pengguna_staff(): void
    {
        $admin = $this->admin();
        $staff = $this->staff();

        $this->actingAs($admin)->delete(route('user.destroy', $staff->id))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
    }

    public function test_admin_tidak_bisa_hapus_admin_saat_hanya_satu(): void
    {
        $admin = $this->admin();
        $staff = $this->staff();

        $this->actingAs($admin)->delete(route('user.destroy', $admin->id))
            ->assertSessionHasErrors();
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin']);

        $this->actingAs($admin)->delete(route('user.destroy', $staff->id))->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin']);
    }

    public function test_staff_tidak_bisa_arsipkan_produk(): void
    {
        $gadget = $this->makeGadget();
        $this->actingAs($this->staff())
            ->delete(route('gadget.destroy', $gadget->id))
            ->assertForbidden();
    }

    public function test_admin_bisa_membuat_produk_dengan_log_stok_awal(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('gadget.store'), [
            'nama_produk' => 'Produk Baru',
            'kategori' => 'Laptop',
            'stock' => 8,
            'status' => 'Tersedia',
            'satuan' => 'unit',
        ])->assertRedirect(route('gadget.index'));

        $this->assertDatabaseHas('products', ['nama_produk' => 'Produk Baru', 'stock' => 8]);
        $this->assertDatabaseHas('stok_logs', ['tipe' => StokLog::TIPE_STOK_AWAL, 'perubahan' => 8, 'user_name' => $admin->name]);
    }

    public function test_status_tidak_valid_ditolak(): void
    {
        $this->actingAs($this->admin())->post(route('gadget.store'), [
            'nama_produk' => 'Produk',
            'kategori' => 'Laptop',
            'stock' => 1,
            'status' => 'StatusLuarBiasa',
        ])->assertSessionHasErrors('status');
    }

    public function test_stok_tidak_boleh_negatif(): void
    {
        $gadget = $this->makeGadget(['stock' => 2]);
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('mutasi.store'), [
            'gadget_id' => $gadget->id,
            'jenis' => 'Pengeluaran',
            'qty' => 5,
            'alasan' => 'Kirim customer',
        ])->assertSessionHasErrors('stok');

        $this->assertDatabaseHas('products', ['id' => $gadget->id, 'stock' => 2]);
    }

    public function test_mutasi_penerimaan_menambah_stok(): void
    {
        $gadget = $this->makeGadget(['stock' => 2]);
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('mutasi.store'), [
            'gadget_id' => $gadget->id,
            'jenis' => 'Penerimaan',
            'qty' => 3,
            'alasan' => 'Restock dari supplier',
        ])->assertRedirect(route('mutasi.index'));

        $this->assertDatabaseHas('products', ['id' => $gadget->id, 'stock' => 5]);
        $this->assertDatabaseHas('stok_logs', [
            'gadget_id' => $gadget->id,
            'tipe' => StokLog::TIPE_PENERIMAAN,
            'perubahan' => 3,
            'user_name' => $admin->name,
        ]);
    }

    public function test_upload_foto_format_tidak_valid_ditolak(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('gadget.store'), [
            'nama_produk' => 'Produk Foto',
            'kategori' => 'Laptop',
            'stock' => 1,
            'status' => 'Tersedia',
            'foto' => UploadedFile::fake()->create('dokumen.txt', 1),
        ])->assertSessionHasErrors('foto');
    }

    public function test_produk_tidak_ditemukan_mengembalikan_404(): void
    {
        $this->actingAs($this->admin())->get(route('gadget.show', 999999))->assertNotFound();
    }

    public function test_export_csv_melindungi_formula_injection(): void
    {
        $this->admin();
        $this->makeGadget(['nama_produk' => '=SUM(A1:A2)', 'kategori' => 'Laptop']);

        $response = $this->actingAs(User::where('role', 'admin')->first())
            ->get(route('gadget.export'));

        $response->assertOk();
        $this->assertStringContainsString("'=SUM(A1:A2)", $response->getContent());
    }

    public function test_transfer_mencatat_log_tipe_transfer(): void
    {
        $gadget = $this->makeGadget(['lokasi_rak' => 'RAK-A1']);
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('gadget.transfer', $gadget->id), [
            'lokasi_rak_tujuan' => 'RAK-B2',
        ])->assertRedirect();

        $this->assertDatabaseHas('products', ['id' => $gadget->id, 'lokasi_rak' => 'RAK-B2']);
        $this->assertDatabaseHas('stok_logs', ['gadget_id' => $gadget->id, 'tipe' => StokLog::TIPE_TRANSFER]);
    }
}