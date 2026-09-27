# Product Requirement Document (PRD): Modul Reseller & Grosiran (B2B Wholesale)

**Nama Proyek:** Modul Penjualan Grosir & Kemitraan Reseller (Wholesale & Reseller Engine)  
**Aplikasi Induk:** Sistem Manajemen Inventaris Gudang Gadget  
**Status:** Draft / Approved  
**Versi:** 1.0  
**Tanggal:** 27 September 2026  

---

## 1. Ringkasan Eksekutif (Executive Summary)

### 1.1 Latar Belakang
Selain melayani penjualan eceran (retail) melalui kasir fisik dan storefront online, gudang memiliki potensi besar untuk mendistribusikan barang dalam kuantitas banyak ke mitra toko kecil, konter HP, reseller online, dan pelaku dropship. Diperlukan modul **Reseller & Grosir** yang mampu mengatur skema harga bertingkat (*tiered pricing*), mempermudah transaksi partai besar, dan memfasilitasi kebutuhan mitra bisnis secara otomatis.

### 1.2 Tujuan & Sasaran Bisnis (Objectives)
- **Mempercepat Perputaran Stok (*High Inventory Turnover*):** Mendorong penjualan volume besar untuk mencegah penumpukan stok lama di gudang.
- **Automasi Harga Bertingkat (*Quantity-based Pricing*):** Sistem secara otomatis menerapkan potongan harga grosir sesuai jumlah unit yang dibeli tanpa perlu kalkulasi manual.
- **Memberdayakan Jaringan Reseller & Dropshipper:** Menyediakan alat bantu bagi mitra, seperti *download price list* harian dan label pengiriman khusus dropship.
- **Integrasi Penuh dengan Gudang & Kasir (POS):** Transaksi grosir langsung memotong stok gudang dan tercatat dalam pembukuan laba kotor secara akurat berdasarkan HPP.

---

## 2. Model Kemitraan & Skema Bisnis

```mermaid
flowchart TD
    A([Tipe Pembelian]) --> B{Pilih Model Transaksi}
    B -- Model 1: Grosir Otomatis per Qty --> C[Semua Pembeli (Ecer/Grosir)]
    B -- Model 2: Akun Mitra Terdaftar --> D[Reseller Terverifikasi]
    B -- Model 3: Pesanan Dropship --> E[Kirim ke Customer Akhir atas Nama Reseller]
    
    C --> F[Beli ≥ MOQ: Otomatis Diskon Grosir]
    D --> G[Login Mitra: Tampil Harga Reseller Khusus]
    E --> H[Generate Label Pengiriman Custom Reseller]
    
    F --> I[(Potong Stok Gudang & Catat Penjualan)]
    G --> I
    H --> I
```

---

## 3. Rincian Kebutuhan Fungsional (Functional Requirements)

### FR-1: Manajemen Harga Grosir Bertingkat (*Tiered Pricing Management*)
- **FR-1.1:** Admin dapat menentukan beberapa level harga untuk setiap produk gadget:
  - **Harga Eceran (Retail):** Berlaku untuk pembelian 1 – 2 unit.
  - **Harga Grosir Level 1 (Min. Qty misal ≥ 3 unit):** Potongan harga untuk pembelian sedang.
  - **Harga Grosir Level 2 / Partai (Min. Qty misal ≥ 10 unit):** Harga khusus borongan/kartonan.
- **FR-1.2:** Pengaturan batas minimum pembelian grosir (*Minimum Order Quantity* / MOQ) per kategori atau per unit produk.
- **FR-1.3:** Proteksi Margin (Safety Check): Sistem otomatis mencegah penetapan harga grosir yang lebih rendah dari HPP (*Harga Pokok Penjualan* / Harga Modal) agar toko tidak rugi.

### FR-2: Display & Interaksi di Storefront Publik (`/shop`)
- **FR-2.1 (Tabel Harga Grosir di Halaman Produk):**
  Di halaman detail gadget (`/shop/produk/{id}`), tampilkan tabel transparan:
  | Jumlah Pembelian | Harga per Unit | Hemat |
  | :--- | :--- | :--- |
  | 1 – 2 Unit | Rp 3.500.000 | - |
  | 3 – 9 Unit | Rp 3.350.000 | Rp 150.000 / unit |
  | ≥ 10 Unit (Partai) | Rp 3.200.000 | Rp 300.000 / unit |
- **FR-2.2 (Badge "Tersedia Harga Grosir"):** Pada kartu produk di katalog, sematkan badge penanda untuk barang yang memiliki harga grosir.
- **FR-2.3 (Tombol WhatsApp Khusus Order Partai):** Tombol aksi sekunder **"💬 Tanya Harga Partai / Grosir"** dengan format pesan otomatis yang langsung mencantumkan estimasi jumlah order.

### FR-3: Integrasi Transaksi Grosir di Modul Kasir (POS)
- **FR-3.1 (Auto Tier Calculation):** Saat kasir menginput kuantitas barang di keranjang belanja POS (misal diubah dari 1 menjadi 5), sistem otomatis mengubah harga satuan ke tier harga grosir yang sesuai.
- **FR-3.2 (Opsi Tipe Pelanggan di Kasir):** Kasir dapat memilih tipe transaksi:
  - `Pelanggan Umum (Retail)`
  - `Mitra Reseller / Toko Grosir` (langsung mengunci harga khusus mitra)
- **FR-3.3 (Data Identitas Mitra):** Input nama toko mitra, nomor telepon, dan catatan khusus pada nota/invoice grosir.

### FR-4: Portal Kemitraan Reseller (B2B Member Portal)
- **FR-4.1 (Registrasi & Verifikasi Mitra):**
  - Calon reseller dapat mendaftar melalui form registrasi mitra (Nama Pemilik, Nama Konter/Toko, No. HP/WA, Alamat, Foto KTP/Toko).
  - Admin melakukan verifikasi/approval di dashboard admin.
- **FR-4.2 (Mode Tampilan Khusus Reseller):**
  - Setelah login sebagai akun mitra terverifikasi, harga yang tampil di seluruh katalog storefront otomatis berubah menjadi harga reseller.
- **FR-4.3 (Download Price List Harian 1-Klik):**
  - Fitur ekspor seluruh daftar produk *Ready Stock* beserta harga reseller ke dalam format **PDF** dan **Excel/CSV** harian yang siap disebarkan reseller ke pelanggan mereka.

### FR-5: Fitur Pengiriman Dropship
- **FR-5.1:** Form input transaksi penjualan kasir/admin mendukung centang **"Kirim sebagai Dropship"**.
- **FR-5.2:** Input data:
  - *Nama Pengirim & No. HP:* Nama toko milik reseller.
  - *Nama Penerima & Alamat:* Pelanggan akhir dari reseller.
- **FR-5.3:** Cetak label resi pengiriman tanpa mencantumkan nama gudang/toko utama (menjaga privasi bisnis reseller).

---

## 4. Desain Database & Skema Data (Database Schema)

### 4.1 Tabel `gadget_tier_prices` (Skema Harga Grosir Bertingkat)
```sql
CREATE TABLE gadget_tier_prices (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    gadget_id BIGINT NOT NULL,
    min_qty INT NOT NULL DEFAULT 1,
    max_qty INT NULL,                 -- NULL berarti 'dan seterusnya'
    tier_name VARCHAR(50) NOT NULL,   -- 'Retail', 'Grosir 3pcs', 'Partai 10pcs'
    price DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (gadget_id) REFERENCES gadgets(id) ON DELETE CASCADE
);
```

### 4.2 Tambahan Kolom pada Tabel `gadgets` (Pilihan Skema Cepat/Ringan)
```sql
ALTER TABLE gadgets ADD COLUMN harga_grosir DECIMAL(15,2) NULL;
ALTER TABLE gadgets ADD COLUMN min_qty_grosir INT DEFAULT 3;
```

### 4.3 Tabel `reseller_profiles` (Data Mitra Reseller)
```sql
CREATE TABLE reseller_profiles (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    store_name VARCHAR(150) NOT NULL,
    phone_number VARCHAR(30) NOT NULL,
    address TEXT NULL,
    status ENUM('pending', 'approved', 'rejected', 'suspended') DEFAULT 'pending',
    discount_rate DECIMAL(5,2) DEFAULT 0.00, -- diskon global opsional (%)
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## 5. Rencana Tahapan Pelaksanaan (Implementation Roadmap)

```
[ Fase 1: Fondasi Harga Grosir & POS (Cepat & Berdampak Langsung) ]
├── Tambahkan kolom harga_grosir & min_qty_grosir pada tabel gadgets
├── Update CRUD Produk (Admin bisa input harga grosir & minimal qty)
└── Integrasi Kasir (POS): Auto-switch harga grosir saat qty di keranjang memenuhi syarat

[ Fase 2: Tampilan Storefront & WhatsApp Order Partai ]
├── Tampilkan Badge Grosir & Tabel Tiering Harga di halaman Detail Produk
├── Tambahkan Tombol WA "Order Partai / Grosir" dengan template pesan khusus
└── Filter Produk di Katalog: Opsi "Hanya Produk Grosir"

[ Fase 3: Portal Mitra & Fitur Dropship ]
├── Registrasi & Otorisasi Akun Reseller oleh Admin
├── Fitur 1-Klik Download Price List Harian (PDF / Excel)
└── Form Transaksi Dropship & Cetak Label Resi Pengiriman Tanpa Logo Toko Utama
```

---

## 6. Kriteria Keberhasilan (Definition of Done)
1. Perubahan kuantitas di keranjang Kasir (POS) otomatis menyesuaikan total harga ke tarif grosir jika mencapai batas minimum beli.
2. Calon pembeli online dapat melihat tabel transparansi harga grosir di halaman detail produk.
3. Link WhatsApp order partai terbuat dengan format pesan yang menyertakan jumlah partai dan harga grosir.
4. Sistem memvalidasi bahwa harga grosir tidak boleh lebih rendah dari harga modal (HPP) barang.
5. Admin dapat mengunduh daftar harga stok ready terkini untuk dibagikan ke mitra reseller.
