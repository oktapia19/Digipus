<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\AppNotificationController;

/*
|--------------------------------------------------------------------------
| LANDING (GUEST)
|--------------------------------------------------------------------------
*/
Route::get('/', [BookController::class, 'home'])->name('home');
Route::get('/buku', [BookController::class, 'userBuku'])->name('buku.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login', fn () => view('login.login'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', fn () => view('login.register'))->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');

/*
|--------------------------------------------------------------------------
| USER (PEMINJAM)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard2', [AuthController::class, 'dashboard2'])->name('dashboard2');
    Route::post('/books/{book}/borrow', [BookController::class, 'borrow'])->name('books.borrow');
    Route::get('/koleksi-buku', [App\Http\Controllers\WishlistController::class, 'index'])->name('koleksi_buku.index');
    Route::post('/books/{book}/koleksi-buku', [App\Http\Controllers\WishlistController::class, 'store'])->name('koleksi_buku.store');
    Route::delete('/books/{book}/koleksi-buku', [App\Http\Controllers\WishlistController::class, 'destroy'])->name('koleksi_buku.destroy');
    Route::post('/ulasan-buku', [App\Http\Controllers\ReviewController::class, 'store'])->name('ulasan_buku.store');

    // PEMINJAMAN
    Route::get('/books/{book}/pinjam', [App\Http\Controllers\PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/books/{book}/pinjam', [App\Http\Controllers\PeminjamanController::class, 'store'])->name('peminjaman.store');

    Route::get('/riwayat', [App\Http\Controllers\PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/{peminjaman}', [App\Http\Controllers\PeminjamanController::class, 'show'])->name('peminjaman.show');
    Route::get('/peminjaman/{peminjaman}/receipt', [App\Http\Controllers\PeminjamanController::class, 'receipt'])->name('peminjaman.receipt');
    Route::get('/peminjaman/{peminjaman}/receipt-pdf', [App\Http\Controllers\PeminjamanController::class, 'receiptPdf'])->name('peminjaman.receiptPdf');
    Route::post('/peminjaman/{peminjaman}/return', [App\Http\Controllers\PeminjamanController::class, 'return'])->name('peminjaman.return');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // DASHBOARD & PROFILE
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
        Route::post('/profile/photo', [AdminController::class, 'updatePhoto'])->name('profile.photo');
        Route::get('/denda', [AdminController::class, 'denda'])->name('denda.index');

        // 🔥 BUKU PENDING (APPROVAL)
        Route::get('/books/pending', [AdminController::class, 'pendingBooks'])
            ->name('books.pending');

        Route::post('/books/{id}/approve', [AdminController::class, 'approveBuku'])
            ->name('books.approve');

        Route::post('/books/{id}/reject', [AdminController::class, 'rejectBuku'])
            ->name('books.reject');

        // ===== ADMIN BOOKS =====
        Route::get('/books', [AdminController::class, 'dataBuku'])->name('books.index');
        Route::get('/books/create', [AdminController::class, 'createBuku'])->name('books.create');
        Route::post('/books', [AdminController::class, 'storeBuku'])->name('books.store');

        Route::get('/books/{book}/edit', [AdminController::class, 'editBuku'])->name('books.edit')->whereNumber('book');
        Route::put('/books/{book}', [AdminController::class, 'updateBuku'])->name('books.update')->whereNumber('book');
        Route::delete('/books/{book}', [AdminController::class, 'deleteBuku'])->name('books.destroy')->whereNumber('book');

        Route::get('/books/{book}', [AdminController::class, 'showBuku'])->name('books.show')->whereNumber('book');

        Route::get('/books/export/pdf', [AdminController::class, 'exportPdf'])->name('books.export.pdf');
        Route::get('/books/export/pending/pdf', [AdminController::class, 'exportPendingPdf'])->name('books.export.pending.pdf');
        Route::get('/users', [AdminController::class, 'dataUser'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        Route::get('/users/export/pdf', [AdminController::class, 'exportUsersPdf'])->name('users.export.pdf');

        // ===== ADMIN PETUGAS =====
        Route::get('/petugas', [AdminController::class, 'dataPetugas'])->name('petugas.index');
        Route::get('/petugas/create', [AdminController::class, 'createPetugas'])->name('petugas.create');
        Route::post('/petugas', [AdminController::class, 'storePetugas'])->name('petugas.store');
        Route::get('/petugas/{id}/edit', [AdminController::class, 'editPetugas'])->name('petugas.edit');
        Route::put('/petugas/{id}', [AdminController::class, 'updatePetugas'])->name('petugas.update');
        Route::delete('/petugas/{id}', [AdminController::class, 'destroyPetugas'])->name('petugas.destroy');

        Route::get('/petugas/export/pdf', [AdminController::class, 'exportPetugasPdf'])->name('petugas.export.pdf');

        // ===== ADMIN KATEGORI =====
        Route::resource('kategori', \App\Http\Controllers\Admin\KategoriBukuController::class);
            // admin confirm & reject peminjaman
            Route::post('/peminjaman/{peminjaman}/confirm', [App\Http\Controllers\PeminjamanController::class, 'confirm'])->name('peminjaman.confirm');
            Route::post('/peminjaman/{peminjaman}/reject', [App\Http\Controllers\PeminjamanController::class, 'reject'])->name('peminjaman.reject');
            Route::post('/peminjaman/{peminjaman}/return-confirm', [App\Http\Controllers\PeminjamanController::class, 'returnConfirm'])->name('peminjaman.returnConfirm');
            Route::post('/peminjaman/verify-by-code', [App\Http\Controllers\PeminjamanController::class, 'verifyByCode'])->name('peminjaman.verifyByCode');
    });

/*
|--------------------------------------------------------------------------
| PETUGAS
|--------------------------------------------------------------------------
*/
Route::middleware('auth:petugas')
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {

        Route::get('/dashboard', [PetugasController::class, 'dashboard'])
            ->name('dashboard');
        Route::get('/denda', [PetugasController::class, 'denda'])
            ->name('denda.index');

        Route::get('/books', [PetugasController::class, 'books'])
            ->name('books.index');

        Route::get('/books/create', [PetugasController::class, 'create'])
            ->name('books.create');

        Route::post('/books', [PetugasController::class, 'store'])
            ->name('books.store');

        Route::get('/books/{book}/edit', [PetugasController::class, 'edit'])
            ->name('books.edit');

        Route::put('/books/{book}', [PetugasController::class, 'update'])
            ->name('books.update');

        Route::delete('/books/{book}', [PetugasController::class, 'destroy'])
            ->name('books.destroy');

        Route::get('/books/{book}', [PetugasController::class, 'show'])
            ->name('books.show');

        // petugas users (read + export)
        Route::get('/users', [PetugasController::class, 'users'])
            ->name('users.index');
        Route::get('/users/export/pdf', [PetugasController::class, 'exportUsersPdf'])
            ->name('users.export.pdf');

        Route::get('/profile', [PetugasController::class, 'profile'])
            ->name('profile');
        Route::post('/profile/photo', [PetugasController::class, 'updatePhoto'])
            ->name('profile.photo');
        Route::post('/profile/update', [PetugasController::class, 'updateProfile'])
            ->name('profile.update');

        // petugas confirm & reject peminjaman
        Route::post('/peminjaman/{peminjaman}/confirm', [App\Http\Controllers\PeminjamanController::class, 'confirm'])->name('peminjaman.confirm');
        Route::post('/peminjaman/{peminjaman}/reject', [App\Http\Controllers\PeminjamanController::class, 'reject'])->name('peminjaman.reject');
        Route::post('/peminjaman/{peminjaman}/return-confirm', [App\Http\Controllers\PeminjamanController::class, 'returnConfirm'])->name('peminjaman.returnConfirm');
        Route::post('/peminjaman/verify-by-code', [App\Http\Controllers\PeminjamanController::class, 'verifyByCode'])->name('peminjaman.verifyByCode');
    });

/*
|--------------------------------------------------------------------------
| API (untuk fetch peminjaman detail)
|--------------------------------------------------------------------------
*/
Route::get('/api/peminjaman/{peminjaman}', function (\App\Models\Peminjaman $peminjaman) {
    $peminjaman->load('user', 'book');
    return $peminjaman->toJson();
});

Route::get('/notifications/{notification}/go', [AppNotificationController::class, 'go'])
    ->name('notifications.go');
Route::post('/notifications/{notification}/read', [AppNotificationController::class, 'read'])
    ->name('notifications.read');
Route::post('/notifications/read-all', [AppNotificationController::class, 'readAll'])
    ->name('notifications.readAll');
Route::delete('/notifications/{notification}', [AppNotificationController::class, 'destroy'])
    ->name('notifications.destroy');
Route::post('/notifications/clear-all', [AppNotificationController::class, 'destroyAll'])
    ->name('notifications.destroyAll');
Route::post('/notifications/clear-all/ajax', [AppNotificationController::class, 'destroyAllAjax'])
    ->name('notifications.destroyAllAjax');
