<?php

/*
|--------------------------------------------------------------------------
| Dokumentasi API — Kelas, Siswa, Kartu Pelajar
|--------------------------------------------------------------------------
| Base URL (lokal): http://localhost:8000/api
| Format: JSON. Semua response sukses GET = 200, POST create = 201,
| validasi gagal = 422, data tidak ditemukan = 404.
|
| Relasi:
| - Kelas (1) -> Siswa (N)
| - Siswa (1) -> Kartu Pelajar (1)
|
|--------------------------------------------------------------------------
| KELAS
|--------------------------------------------------------------------------
| GET    /api/kelas          List semua kelas + siswas.kartuPelajar
|   Response 200: [{ id, nama_kelas, created_at, updated_at,
|     siswas: [{ id, nama, id_kelas, kartu_pelajar: {...} }] }]
|
| POST   /api/kelas          Buat kelas baru
|   Body: { "nama_kelas": "X RPL 1" }              // required, string, max:255
|   Response 201: { id, nama_kelas, ... }
|   cURL:
|   curl -X POST http://localhost:8000/api/kelas \
|     -H "Content-Type: application/json" \
|     -d '{"nama_kelas":"X RPL 1"}'
|
| GET    /api/kelas/{id}     Detail 1 kelas + siswas.kartuPelajar
|   Response 200: { id, nama_kelas, ..., siswas: [...] } | 404 jika tidak ada
|
| PUT    /api/kelas/{id}     Update kelas (bisa juga PATCH)
|   Body: { "nama_kelas": "XI RPL 1" }             // required, string, max:255
|   Response 200: { id, nama_kelas, ... }
|
| DELETE /api/kelas/{id}     Hapus kelas (cascade: siswa & kartunya ikut terhapus)
|   Response 200: { "message": "Kelas dihapus" }
|
|--------------------------------------------------------------------------
| SISWA
|--------------------------------------------------------------------------
| GET    /api/siswa          List semua siswa + kelas + kartuPelajar
|   Response 200: [{ id, nama, id_kelas, kelas: {...}, kartu_pelajar: {...} }]
|
| POST   /api/siswa          Buat siswa baru
|   Body: { "nama": "Budi Santoso", "id_kelas": 1 }
|     - nama: required, string, max:255
|     - id_kelas: required, harus ada di tabel kelas (exists:kelas,id)
|   Response 201: { id, nama, id_kelas, kelas: {...}, kartu_pelajar: null }
|   cURL:
|   curl -X POST http://localhost:8000/api/siswa \
|     -H "Content-Type: application/json" \
|     -d '{"nama":"Budi Santoso","id_kelas":1}'
|
| GET    /api/siswa/{id}     Detail 1 siswa + kelas + kartuPelajar
|
| PUT    /api/siswa/{id}     Update siswa (bisa juga PATCH, field optional)
|   Body: { "nama": "Budi Pratama", "id_kelas": 2 }
|   Response 200: { id, nama, ... }
|
| DELETE /api/siswa/{id}     Hapus siswa (cascade: kartu pelajar ikut terhapus)
|   Response 200: { "message": "Siswa dihapus" }
|
|--------------------------------------------------------------------------
| KARTU PELAJAR
|--------------------------------------------------------------------------
| GET    /api/kartu-pelajar          List semua kartu + siswa.kelas
|   Response 200: [{ id, nomor_kartu, id_siswa, siswa: { id, nama, kelas: {...} } }]
|
| POST   /api/kartu-pelajar          Buat kartu baru (1 siswa = 1 kartu)
|   Body: { "nomor_kartu": "KP-2026-000001", "id_siswa": 1 }
|     - nomor_kartu: required, string, max:255, unique
|     - id_siswa: required, exists:siswas,id, unique (belum punya kartu)
|   Response 201: { id, nomor_kartu, id_siswa, siswa: {...} }
|   cURL:
|   curl -X POST http://localhost:8000/api/kartu-pelajar \
|     -H "Content-Type: application/json" \
|     -d '{"nomor_kartu":"KP-2026-000001","id_siswa":1}'
|
| GET    /api/kartu-pelajar/{id}     Detail 1 kartu + siswa.kelas
|
| PUT    /api/kartu-pelajar/{id}     Update kartu (field optional, tetap unique)
|   Body: { "nomor_kartu": "KP-2026-000002", "id_siswa": 1 }
|
| DELETE /api/kartu-pelajar/{id}     Hapus kartu
|   Response 200: { "message": "Kartu pelajar dihapus" }
|
|--------------------------------------------------------------------------
| GURU
|--------------------------------------------------------------------------
| GET    /api/guru                   List semua guru
|   Response 200: { success: true, message: "...", data: [...] }
|
| POST   /api/guru                   Tambah guru baru
|   Body: {
|     "nama": "Pak Ahmad",          // required, string, max:255
|     "nik": "3201012345670001",    // required, string, max:255, unique
|     "email": "ahmad@sekolah.sch.id", // required, string, max:255, unique
|     "no_hp": "081234567890",      // required, string, max:255, unique
|     "password": "password123",    // required, string, max:255
|     "foto": "ahmad.jpg",          // nullable, string, max:255
|     "keahlian": "Teknik Informatika" // required, in:Teknik Informatika,Akuntansi,Administrasi Bisnis,Desain Grafis
|   }
|   Response 201: { success: true, message: "...", data: {...} }
|   cURL:
|   curl -X POST http://localhost:8000/api/guru \
|     -H "Content-Type: application/json" \
|     -d '{"nama":"Pak Ahmad","nik":"3201012345670001","email":"ahmad@sekolah.sch.id","no_hp":"081234567890","password":"password123","keahlian":"Teknik Informatika"}'
|
| GET    /api/guru/{id}              Detail 1 guru
|   Response 200: { success: true, message: "...", data: {...} } | 404 jika tidak ada
|
| PUT    /api/guru/{id}              Update guru (bisa juga PATCH, field optional)
|   Body: { "nama": "Pak Ahmad M.Kom", "keahlian": "Teknik Informatika" }
|   Response 200: { success: true, message: "...", data: {...} }
|
| DELETE /api/guru/{id}              Hapus guru
|   Response 200: { success: true, message: "Data guru berhasil dihapus!" }
|
*/

use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\KartuPelajarController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\SiswaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// CRUD Kelas: GET|POST /kelas, GET|PUT|PATCH|DELETE /kelas/{kela}
Route::apiResource('kelas', KelasController::class);

// CRUD Siswa: GET|POST /siswa, GET|PUT|PATCH|DELETE /siswa/{siswa}
Route::apiResource('siswa', SiswaController::class);

// CRUD Kartu Pelajar: GET|POST /kartu-pelajar, GET|PUT|PATCH|DELETE /kartu-pelajar/{kartu_pelajar}
Route::apiResource('kartu-pelajar', KartuPelajarController::class);

// CRUD Guru: GET|POST /guru, GET|PUT|PATCH|DELETE /guru/{guru}
Route::apiResource('guru', GuruController::class);
