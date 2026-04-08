<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AlatController;
use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PeminjamController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// ADMIN
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // CRUD USER
    Route::get('/admin/users', [UserController::class, 'users'])->name('admin.kelola_user');
    Route::post('/admin/users/store', [UserController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/admin/users/{id}', [UserController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [UserController::class, 'destroyUser'])->name('admin.users.destroy');

    // CRUD ALAT
    Route::get('/admin/alat', [AlatController::class, 'alat'])->name('admin.alat');
    Route::post('/admin/alat/store', [AlatController::class, 'storeAlat'])->name('admin.alat.store');
    Route::put('/admin/alat/{id}', [AlatController::class, 'update'])->name('admin.alat.update');
    Route::delete('/admin/alat/{id}', [AlatController::class, 'destroy'])->name('admin.alat.destroy');

    // CRUD PEMINJAMAN
    Route::get('/admin/peminjaman', [PeminjamanController::class, 'peminjaman'])->name('admin.peminjaman');
    Route::post('/admin/peminjaman/store', [PeminjamanController::class, 'storePeminjaman'])->name('admin.peminjaman.store');
    Route::patch('/admin/peminjaman/verifikasi/{id}', [PeminjamanController::class, 'verifikasiPeminjaman'])->name('admin.peminjaman.verifikasi');
    Route::delete('/admin/peminjaman/{id}', [PeminjamanController::class, 'destroyPeminjaman'])->name('admin.peminjaman.destroy');
    Route::patch('/admin/peminjaman/update/{id}', [PeminjamanController::class, 'updatePeminjaman'])->name('admin.peminjaman.update');
    Route::patch('/admin/peminjaman/kembalikan/{id}', [PeminjamanController::class, 'kembalikanPeminjaman'])->name('admin.peminjaman.kembalikan');
    
    Route::get('/admin/pengembalian', [PeminjamanController::class, 'pengembalian'])->name('admin.pengembalian');
});

// PETUGAS
Route::middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/petugas/dashboard', [PetugasController::class, 'index'])->name('petugas.dashboard');

    //Menyetujui Peminjaman
    Route::get('/petugas/menyetujui_peminjaman', [PetugasController::class, 'menyetujuiPeminjaman'])->name('petugas.menyetujui_peminjaman');
    Route::patch('/petugas/peminjaman/{id}/proses', [PetugasController::class, 'prosesPersetujuanPinjam'])->name('petugas.pinjam.proses');

    //Menyetujui Pengembalian
    Route::get('/petugas/menyetujui_pengembalian', [PetugasController::class, 'menyetujuiPengembalian'])->name('petugas.menyetujui_kembali');
    Route::patch('/petugas/pengembalian/{id}/konfirmasi', [PetugasController::class, 'prosesKonfirmasiKembali'])->name('petugas.kembali.proses');
    
    Route::get('/petugas/laporan', [PetugasController::class, 'cetakLaporan'])->name('petugas.laporan');
    Route::get('/petugas/laporan/pdf', [PetugasController::class, 'exportPdf'])->name('petugas.laporan.pdf');
});

// PEMINJAM
Route::middleware(['auth', 'role:peminjam'])->group(function () {
    Route::get('/peminjam/dashboard', [PeminjamController::class, 'index'])->name('peminjam.dashboard');

    //Peminjaman Alat
    Route::get('/peminjam/ajukan/{id}', [PeminjamController::class, 'ajukanPeminjaman'])->name('peminjam.ajukan');
    Route::post('/peminjam/pengajuan/store', [PeminjamController::class, 'storePeminjaman'])->name('peminjam.pengajuan.store');

    Route::get('/peminjam/pengembalian', [PeminjamController::class, 'kembali'])->name('peminjam.kembali');
    Route::put('/peminjam/kembali/{id}', [PeminjamController::class, 'prosesKembali'])->name('peminjam.proses_kembali');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');