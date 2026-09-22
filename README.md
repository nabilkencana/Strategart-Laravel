# Laravel API — Kelas, Siswa, Kartu Pelajar, Guru

REST API untuk data sekolah dengan relasi dan resource:

- **Kelas (1) → Siswa (N)**: 1 kelas punya banyak siswa
- **Siswa (1) → Kartu Pelajar (1)**: 1 siswa punya 1 kartu pelajar
- **Guru**: Manajemen data pengajar/guru beserta bidang keahlian

Stack: Laravel 13, PHP ^8.3, SQLite (default), Sanctum.

## 1. Prasyarat

- PHP >= 8.3 (`php -v`)
- Composer (`composer -V`)
- Git (opsional)

## 2. Instalasi & Menjalankan

```sh
# 1. Masuk ke folder project
cd belajar-api-laravel

# 2. Install dependency
composer install

# 3. Copy env (jika belum ada .env)
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Buat file SQLite jika belum ada (default DB_CONNECTION=sqlite)
touch database/database.sqlite

# 6. Migrasi + seeder (6 kelas, 50 siswa nama Indonesia, 50 kartu pelajar)
php artisan migrate:fresh --seed

# 7. Jalankan server
php artisan serve
# API tersedia di: http://localhost:8000/api
```

Perintah berguna lain:

```sh
# Migrasi saja
php artisan migrate

# Seeder saja (aman dijalankan berulang, pakai firstOrCreate / top-up 50)
php artisan db:seed

# Seeder per tabel
php artisan db:seed --class=KelasSeeder
php artisan db:seed --class=SiswaSeeder
php artisan db:seed --class=KartuPelajarSeeder

# Lihat daftar route API
php artisan route:list --path=api

# Jalankan test
php artisan test
```

## 3. Konfigurasi Database

Default memakai SQLite (`.env`):

```
DB_CONNECTION=sqlite
```

Untuk MySQL, ubah `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravelapi
DB_USERNAME=root
DB_PASSWORD=
```

Lalu jalankan `php artisan migrate:fresh --seed`.

Faker locale untuk nama Indonesia:

```
APP_FAKER_LOCALE=id_ID
```

## 4. Daftar Endpoint

Base URL: `http://localhost:8000/api`

### Kelas

| Method | URL | Keterangan |
|--------|-----|------------|
| GET | `/api/kelas` | List semua kelas + siswa + kartu |
| POST | `/api/kelas` | Buat kelas |
| GET | `/api/kelas/{id}` | Detail kelas |
| PUT/PATCH | `/api/kelas/{id}` | Update kelas |
| DELETE | `/api/kelas/{id}` | Hapus kelas |

### Siswa

| Method | URL | Keterangan |
|--------|-----|------------|
| GET | `/api/siswa` | List semua siswa + kelas + kartu |
| POST | `/api/siswa` | Buat siswa |
| GET | `/api/siswa/{id}` | Detail siswa |
| PUT/PATCH | `/api/siswa/{id}` | Update siswa |
| DELETE | `/api/siswa/{id}` | Hapus siswa |

### Kartu Pelajar

| Method | URL | Keterangan |
|--------|-----|------------|
| GET | `/api/kartu-pelajar` | List semua kartu + siswa + kelas |
| POST | `/api/kartu-pelajar` | Buat kartu |
| GET | `/api/kartu-pelajar/{id}` | Detail kartu |
| PUT/PATCH | `/api/kartu-pelajar/{id}` | Update kartu |
| DELETE | `/api/kartu-pelajar/{id}` | Hapus kartu |

### Guru

| Method | URL | Keterangan |
|--------|-----|------------|
| GET | `/api/guru` | List semua data guru |
| POST | `/api/guru` | Tambah data guru baru |
| GET | `/api/guru/{id}` | Detail data guru |
| PUT/PATCH | `/api/guru/{id}` | Update data guru |
| DELETE | `/api/guru/{id}` | Hapus data guru |

#### Struktur Data Guru:
- `nama`: string, wajib, max:255
- `nik`: string, wajib, max:255, unique
- `email`: string (email), wajib, max:255, unique
- `no_hp`: string, wajib, max:255, unique
- `password`: string, wajib, max:255
- `foto`: string, opsional/nullable, max:255
- `keahlian`: enum wajib (`Teknik Informatika`, `Akuntansi`, `Administrasi Bisnis`, `Desain Grafis`)

Dokumentasi lengkap tiap endpoint (parameter, body JSON, contoh response) ada di komentar atas file `routes/api.php`.

## 5. Contoh Request (cURL)

### Kelas
```sh
# Buat kelas
curl -X POST http://localhost:8000/api/kelas \
  -H "Content-Type: application/json" \
  -d '{"nama_kelas":"X RPL 1"}'
```

### Siswa
```sh
# List siswa
curl http://localhost:8000/api/siswa

# Buat siswa (id_kelas harus ada di tabel kelas)
curl -X POST http://localhost:8000/api/siswa \
  -H "Content-Type: application/json" \
  -d '{"nama":"Budi Santoso","id_kelas":1}'

# Update siswa
curl -X PUT http://localhost:8000/api/siswa/1 \
  -H "Content-Type: application/json" \
  -d '{"nama":"Budi Pratama"}'
```

### Kartu Pelajar
```sh
# Buat kartu pelajar (id_siswa harus ada & belum punya kartu)
curl -X POST http://localhost:8000/api/kartu-pelajar \
  -H "Content-Type: application/json" \
  -d '{"nomor_kartu":"KP-2026-000001","id_siswa":1}'

# Hapus kartu
curl -X DELETE http://localhost:8000/api/kartu-pelajar/1
```

### Guru
```sh
# List semua guru
curl http://localhost:8000/api/guru

# Tambah guru baru
curl -X POST http://localhost:8000/api/guru \
  -H "Content-Type: application/json" \
  -d '{
    "nama": "Pak Ahmad",
    "nik": "3201012345670001",
    "email": "ahmad@sekolah.sch.id",
    "no_hp": "081234567890",
    "password": "password123",
    "foto": "ahmad.jpg",
    "keahlian": "Teknik Informatika"
  }'

# Detail guru
curl http://localhost:8000/api/guru/1

# Update data guru (PUT / PATCH)
curl -X PUT http://localhost:8000/api/guru/1 \
  -H "Content-Type: application/json" \
  -d '{
    "nama": "Pak Ahmad M.Kom",
    "keahlian": "Teknik Informatika"
  }'

# Hapus guru
curl -X DELETE http://localhost:8000/api/guru/1
```
