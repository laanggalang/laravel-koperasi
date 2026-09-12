<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// 📊 Dashboard Utama: Bisa diakses semua role yang sudah login (Admin, Ketua, Bendahara, Staff, Anggota)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // 👤 Pengaturan Profil Akun
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * 👥 KELOMPOK HAK AKSES 1: KELOLA DATA ANGGOTA (CRUD)
     * Hanya diizinkan untuk: Admin/Super Admin
     */
    Route::middleware('role:admin')->group(function () {
        Route::resource('/members', MemberController::class);
    });

    /**
     * 💰 KELOMPOK HAK AKSES 2: KELOLA SIMPANAN & ANGSURAN
     * Hanya diizinkan untuk: Admin, Bendahara, dan Staff
     */
    Route::middleware('role:admin,bendahara,staff')->group(function () {
        Route::resource('/savings', SavingController::class);
        Route::resource('/installments', InstallmentController::class);
    });

    /**
     * 📉 KELOMPOK HAK AKSES 3: PERSETUJUAN KREDIT / LOANS
     * Karena di tabel utama loans/index butuh dibaca oleh banyak role, rute internalnya kita pecah:
     */
    
    // Fitur Ajukan & Simpan Pinjaman Baru: Hanya Admin (Sesuai matriks CRUD Admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
        Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    });

    // Fitur Lihat Daftar & Detail Pinjaman: Diizinkan untuk Admin, Ketua, Bendahara, dan Staff
    Route::middleware('role:admin,ketua,bendahara,staff')->group(function () {
        Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
        // Catatan: Jika ada tombol edit/hapus/persetujuan di controller, bungkus route methodnya dengan middleware ketua/admin di sini nanti
    });
});

require __DIR__.'/auth.php';