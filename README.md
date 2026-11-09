# Manajemen Logistic

Manajemen Logistic adalah aplikasi web untuk mengelola inventaris, peminjaman, pengembalian, dan laporan stok secara terpusat.

## Fitur Utama

- Dashboard sesuai peran pengguna.
- Manajemen item, unit, kondisi, dan stok.
- Proses peminjaman barang consumable dan returnable.
- Proses pengembalian dengan pencatatan kondisi barang.
- Laporan peminjaman bulanan dalam format PDF.
- Autentikasi, registrasi, verifikasi email, reset password, dan pengelolaan profil.
- Tampilan responsif untuk perangkat mobile, tablet, dan desktop.

## Teknologi

- PHP dan Laravel
- MySQL atau MariaDB
- Blade, Tailwind CSS, Alpine.js, dan Vite
- PHPUnit untuk pengujian aplikasi

## Persyaratan

- PHP sesuai versi yang ditentukan di `composer.json`
- Composer
- Node.js dan npm
- MySQL atau MariaDB

## Instalasi

1. Clone repository dan masuk ke direktori proyek.
2. Install dependency PHP:

   ```bash
   composer install
   ```

3. Install dependency frontend dan build asset:

   ```bash
   npm install
   npm run build
   ```

4. Salin `.env.example` menjadi `.env`, lalu sesuaikan koneksi database dan konfigurasi aplikasi.

   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

5. Jalankan migrasi dan seeder:

   ```bash
   php artisan migrate --seed
   ```

## Menjalankan Aplikasi

Untuk development, jalankan server Laravel:

```bash
php artisan serve
```

Kemudian buka `http://127.0.0.1:8000` di browser.

## Pengujian

Jalankan seluruh test dengan perintah berikut:

```bash
php artisan test
```

## Branding

Nama aplikasi: **Manajemen Logistic**

Logo aplikasi tersedia di [public/images/logo-manajemen-logistic.svg](public/images/logo-manajemen-logistic.svg).

## Lisensi

Aplikasi ini dikembangkan untuk kebutuhan internal pengelolaan logistik.
