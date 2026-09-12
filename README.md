# Koperasi Laravel

Starter aplikasi manajemen koperasi berbasis Laravel + MySQL.

## Fitur
- Login admin/petugas (menggunakan Laravel Breeze setelah install)
- Dashboard
- Anggota
- Simpanan: pokok, wajib, sukarela
- Pinjaman dengan bunga flat
- Angsuran
- Status pinjaman
- Struktur database dengan migration + seeder

## Instalasi

```bash
composer create-project laravel/laravel koperasi
cd koperasi
```

Salin file dari paket ini ke project Laravel hasil `composer create-project`.

Konfigurasi `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=koperasi
DB_USERNAME=root
DB_PASSWORD=
```

Lalu:

```bash
php artisan migrate --seed
php artisan serve
```

Untuk autentikasi, install Breeze:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run build
php artisan migrate
```

## Catatan
Versi awal menggunakan:
- PHP 8.2+
- Laravel 12.x
- MySQL 8+
- Blade
- Bootstrap CDN

Skema bunga default: flat. Simpanan default: pokok, wajib, sukarela.
