# Audit Aplikasi Web Gudang

Tanggal audit: 21 September 2026

## Ringkasan

Aplikasi sudah memiliki fondasi inventory dasar:

- Login dan logout
- CRUD produk/gadget
- Perubahan stok
- Riwayat perubahan stok
- Upload foto produk
- Export data CSV
- Manajemen pengguna
- Reset password

Namun, aplikasi masih membutuhkan penguatan keamanan, konsistensi data, pengujian, dan fitur operasional gudang sebelum digunakan secara serius.

## Prioritas Tinggi

### 1. Role dan authorization belum tersedia

Semua pengguna yang sudah login dapat:

- Menambah, mengubah, dan menghapus produk
- Mengubah stok
- Export data
- Mengubah password pengguna lain
- Menghapus pengguna lain

Route hanya menggunakan middleware `auth`, tanpa policy, gate, atau role.

Referensi:

- `routes/web.php`
- `app/Http/Controllers/GadgetController.php`
- `app/Http/Controllers/UserController.php`

Rekomendasi:

- Tambahkan role `admin` dan `staff`
- Batasi manajemen user hanya untuk admin
- Batasi penghapusan produk dan perubahan konfigurasi untuk admin
- Gunakan Laravel Policy atau Gate

### 2. Perubahan stok belum aman untuk request bersamaan

Proses perubahan stok membaca nilai lama, menghitung nilai baru, lalu menyimpan hasilnya. Tanpa transaction dan row lock, dua request bersamaan dapat saling menimpa.

Akibatnya:

- Jumlah stok dapat salah
- Riwayat stok tidak sesuai
- Dua transaksi dapat tercatat dengan stok sebelum yang sama

Referensi:

- `app/Http/Controllers/GadgetController.php`, method `ubahStok`

Rekomendasi:

- Gunakan `DB::transaction()`
- Ambil produk dengan `lockForUpdate()`
- Simpan perubahan stok dan log dalam transaction yang sama

### 3. Validasi backend masih terlalu longgar

Beberapa field hanya divalidasi dengan `required`, sehingga nilai yang tidak sesuai pilihan UI masih dapat dikirim langsung melalui request.

Field yang perlu diperketat:

- `nama_produk`: `string`, `max`
- `kategori`: `string`, `max`
- `deskripsi`: `string`, `max`
- `status`: `in:Tersedia,Habis,Tidak Dijual`
- `stock`: integer, minimum nol

Referensi:

- `app/Http/Controllers/GadgetController.php`

### 4. Status dan stok dapat tidak konsisten

Data dapat tersimpan dengan kondisi seperti:

- Stok `0`, tetapi status `Tersedia`
- Stok lebih dari `0`, tetapi status `Habis`

Dashboard juga menghitung produk habis berdasarkan status, bukan berdasarkan kondisi stok aktual.

Rekomendasi:

- Tentukan satu sumber kebenaran untuk status stok
- Otomatis ubah status menjadi `Habis` ketika stok nol
- Validasi kombinasi stok dan status saat create/update
- Hitung stok habis menggunakan `stock <= 0` jika itu aturan bisnisnya

Referensi:

- `app/Http/Controllers/GadgetController.php`
- `resources/views/landing.blade.php`
- `resources/views/gadget/create.blade.php`
- `resources/views/gadget/edit.blade.php`

### 5. Operasi database dan file belum atomik

Penyimpanan produk, log stok, dan foto dilakukan dalam beberapa langkah terpisah. Jika salah satu langkah gagal, data dapat tersimpan sebagian.

Contoh:

- Produk tersimpan tetapi log stok gagal
- Database tersimpan tetapi upload foto gagal
- Foto baru tersimpan tetapi proses update database gagal

Rekomendasi:

- Gunakan transaction untuk operasi database
- Tangani kegagalan upload dengan jelas
- Hapus file baru jika transaction database gagal
- Jangan menghapus foto lama sebelum foto baru berhasil disimpan

Referensi:

- `app/Http/Controllers/GadgetController.php`, method `store` dan `update`

## Keamanan

### 6. Potensi XSS pada konfirmasi hapus user

Nama user dimasukkan langsung ke inline JavaScript pada atribut `onsubmit`.

Referensi:

- `resources/views/users/index.blade.php`

Rekomendasi:

- Gunakan `data-*` attribute dan JavaScript yang membaca nilai secara aman
- Atau gunakan modal konfirmasi tanpa menyisipkan input user ke source JavaScript

### 7. Export CSV berpotensi formula injection

Nilai dari nama produk, kategori, dan status ditulis langsung ke CSV. Nilai yang diawali `=`, `+`, `-`, atau `@` dapat dianggap formula oleh aplikasi spreadsheet.

Referensi:

- `app/Http/Controllers/GadgetController.php`, method `export`

Rekomendasi:

- Escape nilai yang diawali karakter formula
- Tambahkan prefix apostrophe sebelum menulis nilai berbahaya ke CSV

### 8. Login dan reset password perlu rate limiting

Endpoint login dan reset password perlu pembatasan percobaan untuk mencegah brute force.

Pesan reset password juga sebaiknya tidak membedakan email yang terdaftar dan tidak terdaftar.

Referensi:

- `app/Http/Controllers/LoginController.php`
- `config/auth.php`
- `routes/web.php`

### 9. Data seed default perlu diamankan

Factory atau seeder yang menggunakan password standar berisiko jika dijalankan pada lingkungan produksi.

Referensi:

- `database/seeders/DatabaseSeeder.php`
- `database/factories/UserFactory.php`

Rekomendasi:

- Pastikan seeder demo tidak dijalankan di production
- Gunakan password dari environment variable
- Hindari akun default yang mudah ditebak

## Integritas Data

### 10. Constraint database belum cukup kuat

Kolom produk masih membolehkan nilai yang terlalu bebas:

- `stock` belum menggunakan unsigned atau check constraint
- `status` masih berupa string bebas
- Integritas kategori belum dikontrol

Referensi:

- `database/migrations/2026_09_14_020913_create_products_table.php`

### 11. Relasi foto perlu diperiksa

Tabel `gadget_fotos` menggunakan kolom `id` sebagai foreign key ke produk. Relasi balik pada model foto berisiko menggunakan konvensi `gadget_id` yang tidak sesuai dengan struktur tabel.

Referensi:

- `app/Models/GadgetFoto.php`
- `app/Models/Gadget.php`
- `database/migrations/2026_09_14_030000_create_gadget_fotos_table.php`

### 12. ID produk pada edit/update belum selalu menghasilkan 404

Method `edit` dan `update` mengambil data dengan `where(...)->first()` lalu mengakses hasilnya. ID yang tidak ditemukan dapat menyebabkan error ketika objek bernilai `null`.

Rekomendasi:

- Gunakan `firstOrFail()`
- Atau gunakan route model binding

Referensi:

- `app/Http/Controllers/GadgetController.php`

### 13. Audit log perlu mempertahankan identitas pelaku

Jika user dihapus, log stok dapat kehilangan identitas orang yang melakukan perubahan. Relasi `user_id` perlu foreign key dan kebijakan penghapusan yang jelas.

Referensi:

- `database/migrations/2026_09_15_000001_create_stok_logs_table.php`
- `app/Models/StokLog.php`

## Performa dan Pengalaman Pengguna

### 14. Belum ada pagination dan pencarian server-side

Produk dan user diambil sekaligus dengan `get()`. Pencarian produk dilakukan setelah semua data dirender.

Dampak:

- Waktu loading meningkat saat data bertambah
- Penggunaan memory lebih besar
- Tabel sulit digunakan pada data besar

Rekomendasi:

- Gunakan `paginate()`
- Tambahkan pencarian dan filter di query database
- Tambahkan index untuk kolom yang sering dicari

Referensi:

- `app/Http/Controllers/GadgetController.php`
- `app/Http/Controllers/UserController.php`
- `resources/views/gadget/index.blade.php`

### 15. Form produk belum sepenuhnya konsisten

Form menyatakan semua kolom wajib, tetapi foto bersifat opsional. Field `tanggal_pembelian` juga diproses oleh controller tetapi belum terlihat pada form tambah produk.

Referensi:

- `resources/views/gadget/create.blade.php`
- `app/Http/Controllers/GadgetController.php`

## Fitur Gudang yang Masih Kurang

Untuk penggunaan gudang yang lebih lengkap, aplikasi belum memiliki:

- SKU atau kode barang
- Barcode atau QR code
- Supplier
- Lokasi rak
- Harga beli
- Nilai total aset
- Serial number
- Satuan barang
- Stok minimum
- Peringatan stok rendah
- Penyesuaian stok dengan jumlah bebas
- Alasan setiap transaksi stok
- Penerimaan barang
- Pengeluaran barang
- Retur barang
- Transfer antar lokasi
- Import Excel/CSV
- Laporan berdasarkan periode
- Filter berdasarkan kategori dan status
- Soft delete atau arsip produk

## Testing yang Perlu Ditambahkan

Test yang disarankan:

- Guest diarahkan ke halaman login
- User login dengan kredensial benar dan salah
- Staff tidak dapat mengakses manajemen user
- Admin dapat mengelola user
- Produk dapat dibuat dengan data valid
- Produk menolak status yang tidak valid
- Stok tidak boleh menjadi negatif
- Dua perubahan stok tercatat dengan benar
- Log stok menyimpan user yang melakukan perubahan
- Upload hanya menerima format dan ukuran yang valid
- Produk yang tidak ditemukan menghasilkan 404
- Export tidak menghasilkan formula berbahaya
- User tidak dapat menghapus akun sendiri

Test saat ini masih sangat minim dan test default pada `tests/Feature/ExampleTest.php` perlu disesuaikan dengan middleware autentikasi pada route utama.

## Urutan Pengerjaan yang Disarankan

1. Tambahkan role dan authorization.
2. Perbaiki transaction serta locking pada perubahan stok.
3. Perketat validasi request dan constraint database.
4. Perbaiki keamanan konfirmasi hapus dan export CSV.
5. Tambahkan pagination, pencarian, dan filter.
6. Tambahkan test feature untuk alur utama.
7. Tambahkan SKU, supplier, stok minimum, dan laporan.
8. Tambahkan modul penerimaan, pengeluaran, retur, dan transfer stok.

## Kesimpulan

Aplikasi sudah layak sebagai prototype inventory sederhana. Sebelum digunakan untuk operasional nyata, fokus utama sebaiknya pada authorization, keamanan, konsistensi stok, integritas database, dan test otomatis. Setelah fondasi tersebut stabil, fitur SKU, transaksi gudang, dan laporan dapat dikembangkan secara bertahap.
