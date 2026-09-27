# Product Requirement Document (PRD): Peningkatan Katalog Publik & Storefront (Storefront Revamp)

**Nama Proyek:** Peningkatan Tampilan & Konversi Storefront Publik (Storefront UI/UX Enhancement)  
**Aplikasi Induk:** Sistem Manajemen Inventaris Gudang Gadget  
**Status:** Approved / In Progress  
**Versi:** 2.0  
**Tanggal Terakhir Diperbarui:** 27 September 2026  

---

## 1. Ringkasan Eksekutif & Sasaran Bisnis

### 1.1 Latar Belakang
Storefront publik telah memiliki fondasi katalog, filter multi-kriteria, sinkronisasi stok gudang secara *real-time*, dan integrasi pesan WhatsApp. Namun, berdasarkan audit tampilan dan pengalaman pengguna (UX), masih terdapat celah pada aksesibilitas perangkat mobile, elemen pembangun kepercayaan (*trust badges*), kontrol navigasi, dan kemudahan interaksi di halaman detail produk.

### 1.2 Sasaran Utama (Key Objectives)
- **Mobile-First Experience:** Menjamin navigasi lancar pada smartphone dengan menyediakan *Mobile Drawer Menu* dan *Bottom Sheet Filter*.
- **Meningkatkan Konversi Penjualan (*Trust & Conversion*):** Menampilkan *Trust Badges* (garansi, keaslian unit, toko fisik/COD, tukar tambah) dan opsi pertanyaan cepat WhatsApp.
- **Interaktivitas Detail Produk:** Menyediakan fitur salin/bagikan link (*Share Button*), kartu informasi metode pembayaran, dan harga promo (harga coret).
- **Kontrol UI yang Fleksibel:** Menambahkan tombol navigasi manual (Prev/Next) pada hero banner slider.

---

## 2. Pengguna & Skenario Penggunaan (User Personas)

| Persona | Perangkat Utama | Kebutuhan & Ekspektasi |
| :--- | :--- | :--- |
| **Calon Pembeli Mobile (80% Traffic)** | Smartphone (Android/iOS) | Navigasi menu yang mudah dibuka, filter produk tanpa harus scroll terlalu panjang, serta tombol chat WA yang langsung terhubung. |
| **Calon Pembeli Desktop/Laptop** | Laptop / PC | Tampilan katalog yang leluasa, galeri foto detail, perbandingan spesifikasi, dan kejelasan lokasi toko fisik. |
| **Admin / Pengelola Toko** | Admin Dashboard | Kemudahan mengatur banner, mengunggah multi-foto produk, menentukan status promo/harga coret, dan menerima lead WA yang terstruktur. |

---

## 3. Rincian Kebutuhan Fitur (Detailed Requirements)

### FR-1: Navigasi & Tata Letak Responsif (Layout & Mobile Nav)
- **FR-1.1 (Hamburger Menu):** Di layar mobile (< 768px), navbar menampilkan tombol hamburger yang membuka *slide-over drawer* berisi tautan:
  - Beranda (`/shop`)
  - Katalog Lengkap (`/shop/katalog`)
  - Produk Unggulan (`/shop#unggulan`)
  - Info Lokasi & Jam Buka Toko
  - Tombol Cepat Chat CS WhatsApp
- **FR-1.2 (Sticky Quick Action Bar di Mobile):** Bar bawah fleksibel pada halaman detail produk yang selalu menampilkan tombol **"Pesan via WhatsApp"** saat pengguna menggulir layar (*sticky bottom CTA*).

### FR-2: Beranda & Pembangun Kepercayaan (Homepage & Trust Section)
- **FR-2.1 (Hero Slider Navigasi Manual):**
  - Tombol panah **Prev** dan **Next** melayang di sisi kiri & kanan banner.
  - Indikator pagination dots yang responsif.
  - Fitur auto-play (6 detik) yang otomatis jeda (*pause on hover/touch*).
- **FR-2.2 (Section Keunggulan Toko / Value Proposition):**
  Grid 4 pilar kepercayaan yang diletakkan tepat di bawah Hero Slider:
  1. 🛡️ **100% Original & Bergaransi** (Garansi toko & resmi terjamin).
  2. 🏬 **Toko Fisik Jelas** (Bisa cek unit langsung & bayar di tempat / COD).
  3. 🔄 **Layanan Tukar Tambah** (Terima trade-in gadget lama ke baru).
  4. ⚡ **Pengiriman Cepat & Aman** (Packing kayu, bubble wrap tebal, asuransi penuh).
- **FR-2.3 (Section Kunjungi Toko Kami):**
  Menampilkan peta lokasi interaktif (Google Maps embed), foto etalase toko, alamat lengkap, dan jam operasional.

### FR-3: Katalog & Filter Cerdas Mobile-Friendly (`/shop/katalog`)
- **FR-3.1 (Mobile Filter Trigger):**
  - Pada layar mobile, sembunyikan sidebar filter panjang dan gantikan dengan tombol ringkas **"🔘 Filter & Urutkan"**.
  - Saat diklik, membuka pop-up / *drawer modal* untuk memilih Kategori, Brand, Rentang Harga, Kondisi, dan opsi *Ready Stock*.
- **FR-3.2 (Visual Badge Diskon / Harga Coret):**
  - Jika terdapat diskon promo, tampilkan harga coret (misal: ~~Rp 12.000.000~~) bersanding dengan harga jual aktif (**Rp 10.999.000**) dan badge persentase hemat (misal: `-8%`).
- **FR-3.3 (Indikator Filter Aktif):**
  - Menampilkan *badge chip* filter yang sedang aktif dengan tombol silang (x) untuk hapus filter secara parsial.

### FR-4: Halaman Detail Produk & Konversi (`/shop/produk/{id}`)
- **FR-4.1 (Galeri Foto):**
  - Foto utama ukuran besar dengan rasio aspek persegi (*1:1*).
  - *(Opsional/Pengembangan lanjutan)* Dukungan thumbnail galeri multi-sudut (Depan, Belakang, Sisi Samping, Layar Menyala).
- **FR-4.2 (Tombol Bagikan / Share):**
  - Tombol **"Bagikan Produk"** yang menyediakan aksi:
    - Salin tautan ke clipboard (*Copy Link*) dengan toast notifikasi `"Link berhasil disalin!"`.
    - Bagikan langsung ke WhatsApp / Telegram.
- **FR-4.3 (Kartu Metode Pembayaran yang Diterima):**
  - Menampilkan logo/ikon metode pembayaran: Tunai di Toko, Transfer Bank (BCA, Mandiri, BRI, BNI), QRIS (GoPay, OVO, Dana), Mesin EDC Kartu Debit/Kredit, dan Opsi Cicilan/PayLater.
- **FR-4.4 (Opsi Pertanyaan Cepat WhatsApp):**
  Selain tombol utama pesan barang, sediakan tombol opsi tanya spesifik:
  - 💬 *"Tanya Kondisi Bodi & Battery Health"*
  - 💬 *"Tanya Estimasi Tukar Tambah"*
  - 💬 *"Tanya Jadwal Kunjungan ke Toko"*

---

## 4. Kebutuhan Non-Fungsional (NFR)

| Kategori | Parameter Kebutuhan |
| :--- | :--- |
| **Responsivitas & Fluiditas** | Seluruh animasi modal, drawer, dan slider menggunakan CSS transition halus tanpa *layout shift* (CLS < 0.1). |
| **Mobile Performance** | Ukuran script JS minimal (menggunakan Vanilla JavaScript / Alpine.js ringan). |
| **Aksesibilitas (A11y)** | Kontras warna teks memenuhi standar WCAG AA, tombol memiliki label `aria-label` yang jelas. |
| **Keamanan & Data Leak Prevention** | Halaman publik bebas dari kebocoran data HPP (harga beli modal), data supplier, atau log pergerakan stok internal. |

---

## 5. Rencana Tahapan Implementasi (Action Plan)

```
[ Tahap 1: Header, Navbar & Mobile Drawer ]
├── Update layouts/store.blade.php
├── Tambahkan Hamburger Button & Drawer Mobile Menu
└── Perbaiki styling navigasi & tombol kontak

[ Tahap 2: Peningkatan Beranda (Homepage) ]
├── Tambahkan kontrol panah Prev/Next pada Hero Slider
├── Tambahkan Section "4 Keunggulan Toko" (Trust Badges)
└── Integrasi Section Lokasi Toko & Google Maps yang lebih estetik

[ Tahap 3: Peningkatan Katalog & Filter Mobile ]
├── Implementasi Mobile Filter Bottom Sheet/Modal di store/katalog.blade.php
├── Tambahkan Badges Diskon / Harga Coret
└── Rapikan grid kartu produk & tombol aksi cepat

[ Tahap 4: Halaman Detail Produk & Fitur Interaksi ]
├── Tambahkan Tombol Share Link (Salin URL ke Clipboard)
├── Tambahkan Card Informasi Metode Pembayaran
├── Tambahkan Opsi Pertanyaan Cepat WhatsApp (Trade-in / Kondisi)
└── Testing menyeluruh di berbagai resolusi layar (Mobile, Tablet, Desktop)
```

---

## 6. Kriteria Keberhasilan (Definition of Done)
1. Pengunjung melalui smartphone dapat mengakses menu navigasi dengan mulus melalui tombol hamburger.
2. Pengunjung mobile dapat memfilter produk tanpa harus menggulir halaman yang terlalu panjang.
3. Bagian *Trust Badges* muncul rapi di beranda dan memperkuat citra profesional toko.
4. Tombol *Share* di halaman detail produk dapat menyalin URL dengan feedback visual instan.
5. Pesan WhatsApp yang dihasilkan mencantumkan link produk dan konteks pertanyaan secara akurat.
