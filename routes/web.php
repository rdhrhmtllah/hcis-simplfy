<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BscKategoriKpiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect('/bsc-kategori') : view('login');
});

Route::get('/login', [AuthController::class, 'login'])
    ->name('login')
    ->middleware('guest');

Route::get('/mulai/27738', [AuthController::class, 'loginRe'])
    ->name('loginRe')
    ->middleware('guest');

Route::post('/prosesLogin', [AuthController::class, 'prosesLogin'])->name('prosesLogin');

Route::middleware(['auth'])->group(function () {
    Route::get('/homepage', fn () => redirect('/bsc-kategori'))->name('homepage.index');

    Route::get('/bsc-kategori', [BscKategoriKpiController::class, 'index'])->name('bsc-kategori.index');
    Route::get('/api/v1/bsc-kategori/current', [BscKategoriKpiController::class, 'getDataCurrent']);
    Route::post('/api/v1/bsc-kategori/store', [BscKategoriKpiController::class, 'store']);
    Route::put('/api/v1/bsc-kategori/update/{Id_Master_Bsc_Kategori}', [BscKategoriKpiController::class, 'update']);
    Route::post('/api/v1/bsc-kategori/destroy', [BscKategoriKpiController::class, 'destroy']);

    Route::post('/homepage/notifications/read-all', fn () => response()->json(['success' => true]));
    Route::get('/user-profile', fn () => redirect('/bsc-kategori'))->name('profileUser.index');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/getTime', function (Request $request) {
    $time = now('Asia/Jakarta');

    return response()->json([
        'time' => $time->format('Y-m-d\TH:i:sP'),
        'timezone' => 'Asia/Jakarta',
    ]);
})->name('getTime');
