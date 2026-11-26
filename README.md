# BrewPOS

Aplikasi Point of Sale (POS), Kitchen Display System (KDS), dan pemesanan mandiri lewat QR meja (*self-ordering*) untuk kafe serta restoran. Dibangun di atas Laravel 13, Livewire 4, dan Tailwind CSS 4.

## Daftar Isi

- [Alur Kerja Sistem](#alur-kerja-sistem)
- [Fitur Utama](#fitur-utama)
- [Keamanan, Konkurensi, dan Reliabilitas](#keamanan-konkurensi-dan-reliabilitas)
- [Matriks Hak Akses dan Rute](#matriks-hak-akses-dan-rute)
- [Tech Stack](#tech-stack)
- [Skema Database](#skema-database)
- [Struktur Direktori](#struktur-direktori)
- [Instalasi Lokal](#instalasi-lokal)
- [Akun Demo](#akun-demo)
- [Pengujian dan Format Kode](#pengujian-dan-format-kode)
- [Lisensi](#lisensi)

## Alur Kerja Sistem

BrewPOS menangani dua jalur pemesanan yang bermuara ke antrian dapur dan siklus meja yang sama:

```text
[1] Pesanan Kasir (POS)
    Kasir input menu -> Pembayaran (Tunai / QRIS / Transfer) -> Cetak struk thermal
                                                                     |
                                                                     v
[2] Pesanan Pelanggan (QR Meja, tanpa login)                  Antrian Dapur (KDS)
    Scan QR -> Pilih menu & varian -> Checkout                pending -> cooking -> ready
       |                                                                               |
       v                                                                               v
    Masuk notifikasi Kasir (pending_confirmation)                             Disajikan (served)
    Kasir verifikasi pembayaran / tolak (stok kembali) -----> pending         atau selesai (takeaway)
                                                                                       |
                                                                                       v
                                                                              Kasir kosongkan meja
                                                                              (order -> completed,
                                                                               meja -> available)
```

Saat sebuah meja berstatus terisi (`occupied`) dan masih memiliki pesanan aktif dalam 12 jam terakhir, pelanggan di meja yang sama tetap dapat memindai QR dari perangkat lain. Sistem menandai pesanan berikutnya secara otomatis sebagai `[Pesanan Tambahan / Add-on]` pada sesi meja tersebut tanpa menimpa pesanan utama.

## Fitur Utama

### Kasir POS
- Pencarian menu berdasarkan nama atau SKU, filter kategori, dan pemilih varian (seperti suhu, ukuran, gula, atau tingkat pedas) dengan tambahan harga per opsi.
- Dukungan tipe pesanan `dine_in` (dengan pemilih meja yang menampilkan status sesi aktif) dan `takeaway`.
- Perhitungan diskon manual, pajak dinamis sesuai pengaturan toko, nominal uang cepat (*quick money*), dan hitung kembalian otomatis.
- Tiga metode pembayaran: Tunai, QRIS statis toko, dan Transfer Bank dengan detail rekening dari pengaturan.
- Struk thermal siap cetak langsung dari browser (`window.print()`) dengan logo, alamat, kontak, dan catatan footer toko.
- Modal khusus **Pesanan Masuk dari QR Meja** dengan notifikasi suara (Web Audio API) saat ada pesanan baru, tombol konfirmasi pembayaran ke dapur, serta tombol tolak pesanan yang otomatis mengembalikan stok dan status meja.

### Pemesanan Mandiri Pelanggan (QR Meja)
- Antarmuka *mobile-first* yang dapat diakses pelanggan langsung melalui URL unik `/order/{token}` tanpa perlu membuat akun atau login.
- Kustomisasi varian menu dan catatan khusus per item, serta ringkasan keranjang mengambang di bagian bawah layar.
- Modal konfirmasi pesanan dengan pilihan **Bayar di Kasir** atau **Payment Gateway Online** (mode simulasi lunas otomatis untuk pengujian alur kerja).
- Halaman pelacakan status pesanan secara berkala (`/order/{token}/status/{invoice}`) yang menampilkan progres 5 tahap: Order, Antrian, Masak, Siap, dan Saji.
- Deteksi sesi meja aktif dan penandaan otomatis untuk pesanan tambahan (*add-on*).

### Kitchen Display System (Antrian Dapur)
- Tampilan antrian pesanan untuk staf dapur dan barista dengan filter status (`Semua Aktif`, `Menunggu`, `Dimasak`, `Siap Saji`, `Disajikan`, dan `Selesai`) serta pencarian nomor transaksi atau nama pelanggan.
- Pembaruan status berurutan dari `pending` ke `cooking`, `ready`, lalu `served` (untuk `dine_in`, meja tetap `occupied` sampai tamu selesai) atau `completed` (untuk `takeaway`).

### Manajemen Meja dan QR Code
- CRUD meja beserta kapasitas, status (`available`, `occupied`, `reserved`), dan token QR unik otomatis (`tbl_...`) saat meja dibuat.
- Tampilan ringkasan sesi tamu aktif, status dapur, dan jumlah pesanan tambahan pada kartu meja tanpa kueri N+1.
- Pratinjau QR Code, tombol salin tautan (dengan fallback untuk lingkungan HTTP LAN), sakelar aktif/nonaktif pemesanan mandiri per meja, halaman cetak kartu meja (`/tables/{table}/print-qr`), dan aksi **Selesai & Kosongkan Meja** yang menutup semua pesanan aktif di meja tersebut.

### Manajemen Menu Produk dan Varian
- CRUD produk dengan generator SKU otomatis per kategori (`CF-`, `NC-`, `BK-`, `MC-`, `SN-`), kontrol stok, dan sakelar ketersediaan.
- Pengelola grup varian dengan preset cepat (Suhu, Ukuran, Gula) dan harga tambahan per opsi.
- Unggah foto produk 1:1 dengan pratinjau langsung serta kompresi otomatis ke format WebP.

### Riwayat Transaksi dan Laporan Penjualan
- Daftar transaksi berpaginasi dengan filter pencarian, status pembayaran, status pesanan, rentang tanggal, pratinjau detail, dan cetak ulang struk.
- Dashboard laporan penjualan dengan filter periode (`Hari Ini`, `Minggu Ini`, `Bulan Ini`, `Semua`, dan rentang kustom), grafik tren penjualan/pesanan serta distribusi metode pembayaran menggunakan Chart.js.
- Ekspor laporan penjualan ke file Excel (`.xls`) dan tampilan laporan siap cetak (`/reports/print`).

### Pengaturan Toko dan Manajemen Pengguna
- Tiga tab pengaturan tersimpan di tabel `settings` dengan cache permanen: profil toko dan struk (nama, tagline, alamat, telepon, email, logo, footer), pajak dan biaya layanan, serta informasi rekening bank dan gambar QRIS statis.
- Manajemen akun staf (`admin`, `cashier`, `kitchen`) dengan pencarian, filter peran, dan aktivasi/nonaktivasi akun. Akun yang sudah memiliki riwayat transaksi akan dinonaktifkan alih-alih dihapus permanen agar relasi invoice tetap utuh.

## Keamanan, Konkurensi, dan Reliabilitas

| Aspek | Implementasi di Kode |
| :--- | :--- |
| Anti manipulasi harga (*price tampering*) | Pada `Cashier::processPayment` dan `CustomerOrder::submitOrder`, subtotal, tambahan harga varian, pajak, dan total tagihan dihitung ulang dari data master database di sisi server. Nilai harga dari klien diabaikan. |
| Pencegahan *overselling* | Kuantitas pesanan dikonsolidasikan per `product_id` (termasuk jika satu produk dipesan dengan beberapa varian), lalu baris produk dikunci dengan `lockForUpdate()` di dalam transaksi database sebelum validasi dan pengurangan stok. |
| Pencegahan *double-submit* | Checkout kasir, pengiriman pesanan QR, serta konfirmasi dan penolakan pesanan masuk dilindungi dengan `Cache::lock()`. |
| Autentikasi dan otorisasi | Login memakai `RateLimiter` (maksimal 5 percobaan gagal per kombinasi email dan IP), pengecekan status akun aktif, regenerasi sesi, serta `RoleMiddleware` di rute terautentikasi. |
| Optimasi gambar otomatis | `App\Services\ImageOptimizer` mengubah ukuran gambar unggahan secara proporsional (maksimal lebar 800px untuk produk/QRIS dan 600px untuk logo) dan mengonversinya ke WebP lewat PHP GD, dengan fallback ke penyimpanan standar jika GD tidak tersedia. |
| Ketahanan koneksi (*offline resilience*) | `BrewRealtimeManager` di `resources/js/app.js` memantau event jaringan browser (`online`/`offline`) dan fokus tab (`visibilitychange`), menampilkan status koneksi di sidebar, serta mengirim event `refresh-realtime` ke komponen Livewire saat koneksi kembali aktif. |

## Matriks Hak Akses dan Rute

| Rute | Komponen / Controller | Publik | Admin | Cashier | Kitchen |
| :--- | :--- | :---: | :---: | :---: | :---: |
| `GET /order/{token}` | `CustomerOrder` | Ya | Ya | Ya | Ya |
| `GET /order/{token}/status/{invoice}` | `CustomerOrderStatus` | Ya | Ya | Ya | Ya |
| `GET /login` | `Login` (middleware `guest`) | Ya | - | - | - |
| `GET /` | `Cashier` | - | Ya | Ya | - |
| `GET /tables` | `TableList` | - | Ya | Ya | - |
| `GET /tables/{table}/print-qr` | `TablePrintController@printQr` | - | Ya | Ya | - |
| `GET /kitchen` | `KitchenOrders` | - | Ya | - | Ya |
| `GET /orders` | `OrderList` | - | Ya | Ya | Ya |
| `GET /products` | `ProductList` | - | Ya | - | - |
| `GET /reports` | `ReportIndex` | - | Ya | - | - |
| `GET /reports/print` | `ReportPrintController@printReport` | - | Ya | - | - |
| `GET /users` | `UserList` | - | Ya | - | - |
| `GET /settings` | `SettingIndex` | - | Ya | - | - |

## Tech Stack

| Lapisan | Teknologi | Versi di Proyek |
| :--- | :--- | :--- |
| Bahasa | PHP | `^8.3` |
| Framework Backend | Laravel | `^13.17` |
| UI Reaktif | Livewire (dengan Alpine.js bawaan) | `^4.4` |
| Styling | Tailwind CSS (`@tailwindcss/vite`) | `^4.0` |
| Bundler Frontend | Vite | `^8.0` |
| Grafik Laporan | Chart.js | CDN (`chart.js`) |
| Database | MySQL (produksi/lokal), SQLite `:memory:` (testing) | - |
| Pengujian & Linter | PHPUnit, Laravel Pint | `^12.5`, `^1.27` |

## Skema Database

| Tabel | Peran Utama |
| :--- | :--- |
| `users` | Akun staf dengan kolom `role` (`admin`, `cashier`, `kitchen`) dan `is_active`. |
| `categories` | Kategori menu (`name`, `slug`, `icon`, `is_active`). |
| `products` | Daftar menu beserta `sku` unik, `price`, `stock`, `image`, dan `is_available`. |
| `product_variants` | Opsi varian per produk (`variant_group`, `name`, `extra_price`). |
| `tables` | Data meja dine-in (`table_number`, `capacity`, `status`, `qr_token` unik, `is_active`). |
| `orders` | Header transaksi (`invoice_number` unik, `order_source` `pos`/`qr_table`, `order_type`, `payment_method`, `payment_status`, `order_status`, rincian nominal, dan catatan). |
| `order_items` | Rincian item per transaksi (snapshot `product_name`, `unit_price`, `quantity`, `subtotal`, dan `notes` varian). |
| `settings` | Pasangan `key`/`value` terkelompok (`store`, `tax`, `payment`) untuk konfigurasi toko. |
| `sessions`, `cache`, `cache_locks`, `jobs` | Tabel bawaan Laravel untuk sesi, cache, atomic lock, dan antrian. |

## Struktur Direktori

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── ReportPrintController.php
│   │   └── TablePrintController.php
│   └── Middleware/
│       └── RoleMiddleware.php
├── Livewire/
│   ├── Auth/Login.php
│   ├── Customer/
│   │   ├── CustomerOrder.php
│   │   └── CustomerOrderStatus.php
│   ├── Kitchen/KitchenOrders.php
│   ├── Order/OrderList.php
│   ├── Pos/Cashier.php
│   ├── Product/ProductList.php
│   ├── Report/ReportIndex.php
│   ├── Setting/SettingIndex.php
│   ├── Table/TableList.php
│   └── User/UserList.php
├── Models/
│   ├── Category.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Product.php
│   ├── ProductVariant.php
│   ├── Setting.php
│   ├── Table.php
│   └── User.php
└── Services/
    └── ImageOptimizer.php
resources/
├── css/app.css
├── js/app.js
└── views/
    ├── components/layouts/     # app.blade.php, customer.blade.php, guest.blade.php
    ├── livewire/               # Template Livewire per modul
    ├── reports/                # Template cetak & ekspor Excel laporan
    └── tables/                 # Template cetak kartu QR meja
tests/Feature/
├── CustomerOrderCheckoutTest.php
├── ProductManagementTest.php
├── SecurityAndConcurrencyAuditTest.php
├── SettingManagementTest.php
└── TableLifecycleTest.php
```

## Instalasi Lokal

### Prasyarat

- PHP 8.3 atau lebih baru (ekstensi `gd` disarankan agar konversi gambar WebP aktif)
- Composer
- Node.js dan npm
- MySQL / MariaDB (atau SQLite)

### Langkah Instalasi

1. Clone repositori dan masuk ke direktori proyek:

```bash
git clone https://github.com/R16-Code/MinPro-SistemPOS.git
cd MinPro-SistemPOS
```

2. Install dependensi PHP dan JavaScript:

```bash
composer install
npm install
```

3. Salin file konfigurasi lingkungan dan buat application key:

```bash
cp .env.example .env
php artisan key:generate
```

4. Sesuaikan koneksi database di `.env` (contoh untuk MySQL):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_pos
DB_USERNAME=root
DB_PASSWORD=
```

5. Jalankan migrasi, seeder data awal, dan tautan penyimpanan publik:

```bash
php artisan migrate --seed
php artisan storage:link
```

6. Build aset frontend dan jalankan server pengembangan:

```bash
npm run build
php artisan serve
```

Untuk mode pengembangan dengan hot-reload Vite, gunakan `composer run dev` atau jalankan `npm run dev` dan `php artisan serve` secara terpisah.

### Menguji Scan QR Meja dari HP (Jaringan LAN)

Agar QR Code yang dicetak dari halaman **Manajemen Meja** dapat dibuka dari kamera HP pada Wi-Fi yang sama, jalankan server Laravel dengan binding ke semua antarmuka jaringan, lalu buka dashboard menggunakan alamat IP lokal komputer (misalnya `http://192.168.1.10:8000/tables`):

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Akun Demo

Setelah menjalankan `php artisan migrate --seed`, akun berikut tersedia untuk pengujian setiap peran:

| Peran | Nama | Email | Password |
| :--- | :--- | :--- | :--- |
| Admin / Owner | Admin Cafe | `admin@pos.com` | `password` |
| Kasir | Budi Kasir | `kasir@pos.com` | `password` |
| Dapur / Barista | Dapur Barista | `kitchen@pos.com` | `password` |

Seeder juga membuat 5 kategori menu, daftar produk beserta varian, dan 6 meja awal (`Meja 01` sampai `Meja 05`, serta `VIP 1`) lengkap dengan token QR masing-masing.

## Pengujian dan Format Kode

Suite pengujian berjalan di SQLite `:memory:` sesuai konfigurasi `phpunit.xml`:

```bash
php artisan test
```

Atau jalankan file pengujian tertentu:

```bash
php artisan test tests/Feature/CustomerOrderCheckoutTest.php
php artisan test tests/Feature/SecurityAndConcurrencyAuditTest.php
php artisan test tests/Feature/TableLifecycleTest.php
```

Untuk merapikan format kode PHP menggunakan Laravel Pint:

```bash
vendor/bin/pint --dirty
```

## Lisensi

Proyek ini dirilis di bawah lisensi [MIT](https://opensource.org/licenses/MIT).
