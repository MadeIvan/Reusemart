<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AlamatController;
use App\Http\Middleware\PembeliMiddleware;
use App\Http\Middleware\JabatanMiddleware;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DiskusiController;
use App\Http\Controllers\DompetController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\reqdonasiController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\RequestDonasiController;
use App\Http\Controllers\TransaksiDonasiController;
use App\Http\Controllers\TransaksiPenitipanController;
use App\Http\Controllers\TransaksiPembelianController;
use App\Http\Controllers\PointRedemptionController;
use App\Http\Controllers\ImagesBarangController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ClaimMerchandiseController;
use App\Http\Controllers\MerchandiseController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
     return response()->json($request->user());
});

// === Penitip ===
Route::post('/penitip/login', [PenitipController::class, 'login']);
Route::get('/penitip', [PenitipController::class, 'getAllPenitip']);
Route::post('/penitip/register', [PenitipController::class, 'register']);
Route::put('/penitip/update/{id}', [PenitipController::class, 'updatePenitip']);
Route::delete('/penitip/delete/{id}', [PenitipController::class, 'deletePenitip']);
Route::get('/check-nik', [PenitipController::class, 'checkNIK']);
Route::put('/ganti-password/{id}', [PenitipController::class, 'changePassword']);

// === Dompet ===
Route::get('/dompet', [DompetController::class, 'getAllDompet']); 
Route::get('/dompet/{id}', [DompetController::class, 'getDompetById']);
Route::post('/dompet', [DompetController::class, 'createDompet']);
Route::put('/dompet/{id}', [DompetController::class, 'updateDompet']);
Route::delete('/dompet/{id}', [DompetController::class, 'deleteDompet']);

// === Organisasi ===
Route::post('/organisasi/register', [OrganisasiController::class, 'register']);
Route::post('/organisasi/login', [OrganisasiController::class, 'login']);
Route::get('/check-email-username', [OrganisasiController::class, 'checkEmailUsername']);

// === Pembeli ===
Route::post('/pembeli/register', [PembeliController::class, 'register']);
Route::post('/pembeli/login', [PembeliController::class, 'login']);
Route::get('/check-email-username', [PembeliController::class, 'checkEmailUsername']);

// === Pegawai ===
Route::post('/pegawai/register', [PegawaiController::class, 'register']);
Route::post('/pegawai/login', [PegawaiController::class, 'login']);
Route::get('/pegawai', [PegawaiController::class, 'index']);
Route::put('/pegawai/update/{id}', [PegawaiController::class, 'update']);
Route::delete('/pegawai/{id}', [PegawaiController::class, 'softDelete']);
Route::put('/pegawai/reset-password/{id}', [PegawaiController::class, 'resetPassword']);
Route::get('/jabatan', [JabatanController::class, 'index']);
Route::get('/pegawai-showkurir', [PegawaiController::class, 'showKurir']);


// === Authenticated groups ===
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
});

// === Role-based and resource routes ===
Route::middleware(['auth:pegawai','role:2'])->group(function () {
    Route::get('/organisasi', [OrganisasiController::class, 'index']);
    Route::get('/organisasi/search', [OrganisasiController::class, 'show']);
    Route::put('/organisasi/update/{id}', [OrganisasiController::class, 'update']);
    Route::delete('/organisasi/delete/{id}', [OrganisasiController::class, 'destroy']);
    Route::put('/pegawai/reset-password/{id}', [PegawaiController::class, 'resetPassword']);
    Route::post('/buat-diskusi/{id}', [DiskusiController::class, 'store']);
});

Route::middleware(['auth:pegawai','role:5'])->group(function () {
    Route::get('/verifikasi', [TransaksiPembelianController::class, 'getNotConfirmed']);
    Route::post('/verifikasi/{id}', [TransaksiPembelianController::class, 'terimaPembayaran']);    
    Route::post('/tolak-verifikasi/{id}', [TransaksiPembelianController::class, 'tolakVerifikasi']); 
});
Route::middleware(['auth:sanctum', 'auth.pembeli'])->group(function () {
    Route::post('/pembeli/buat-alamat', [AlamatController::class, 'store']);
    Route::get('/pembeli/alamat', [AlamatController::class, 'index']);
    Route::get('/pembeli/alamat/search', [AlamatController::class, 'show']);
    Route::put('/pembeli/alamat/update/{id}', [AlamatController::class, 'update']);
    Route::delete('/pembeli/alamat/delete/{id}', [AlamatController::class, 'delete']);
    Route::put('/pembeli/alamat/set-default/{id}', [AlamatController::class, 'setAsDefault']);
    Route::get('/alamatUtama', [AlamatController::class, 'getUtama']);
    
    Route::post('/buat-diskusi/{id}', [DiskusiController::class, 'store']);
    Route::get('/pembeli/getData', [PembeliController::class, 'getData']);
    Route::put('/updatePoin', [PembeliController::class, 'updatePoin']);
    Route::post('/tambah-keranjang/{id}', [PembeliController::class, 'addToCart']);
    Route::get('/keranjang', [PembeliController::class, 'getCart']);
    Route::delete('/hapus-keranjang/{id}', [PembeliController::class, 'removeFromCart']);
    Route::delete('/hapus-keranjang', [PembeliController::class, 'removeAllCart']);
    Route::post('/checkout', [TransaksiPembelianController::class, 'store']);
    Route::get('/getData', [TransaksiPembelianController::class, 'getDataTerbaru']);
    Route::post('/buktiBayar/{id}', [TransaksiPembelianController::class, 'buktiBayar']);
    Route::post('/batalkanPesanan/{id}', [TransaksiPembelianController::class, 'canceled']);
    Route::get('/pembeli/poin', [PembeliController::class, 'getPoin']);
});

Route::middleware(['auth:penitip'])->group(function () {
    Route::get('/penitip/dashboard', [PenitipController::class, 'show']);
    Route::get('/penitip/profile', [PenitipController::class, 'myData']);
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/penitip/history', [PenitipController::class, 'loadBarang']);
});

// === Barang, Diskusi, RequestDonasi, Donasi, etc. ===
Route::get('/getBarang',[BarangController::class, 'index']);
Route::get('/getBarang/{id}', [BarangController::class, 'show']);
Route::get('/diskusi/{id}',[DiskusiController::class, 'getByBarang']);

Route::get('/donasi',[RequestDonasiController::class,'index']);
Route::get('/barang/available',[BarangController::class,'getAvailableBarang']);
Route::post('/transaksi-donasi', [TransaksiDonasiController::class, 'store']);
Route::get('/transaksi-donasi', [TransaksiDonasiController::class, 'index']);
Route::post('/transaksi-donasi', [TransaksiDonasiController::class, 'store']);
Route::get('/transaksi-donasi/{id}', [TransaksiDonasiController::class, 'show']);
Route::put('/transaksi-donasi/{id}', [TransaksiDonasiController::class, 'update']);
Route::delete('/transaksi-donasi/{id}', [TransaksiDonasiController::class, 'destroy']);

Route::middleware('auth:organisasi')->group(function () {
    Route::get('/reqdonasi', [reqdonasiController::class, 'index']);
    Route::post('/create/reqdonasi', [reqdonasiController::class, 'store']);
    Route::put('/reqdonasi/{id}', [reqDonasiController::class, 'update']);
    Route::delete('/reqdonasi/{id}', [reqDonasiController::class, 'destroy']);
});

// === Penitipan & Pembelian ===
Route::get('/transaksiPenitipan', [TransaksiPenitipanController::class, 'index']);
Route::post('/addTransaksiPenitipan',[TransaksiPenitipanController::class, 'store']);
Route::get('/barang-penjadwalan', [TransaksiPembelianController::class, 'showPenjadwalan']);
Route::put('/barang-penjadwalan/{noNota}/jadwal', [TransaksiPembelianController::class, 'updatePenjadwalan']);
Route::get('/barang-titipNota',[TransaksiPembelianController::class,'showfornota']);

Route::get('/showAllTransaksi',[TransaksiPembelianController::class,'index']);

Route::get('/nota-pembelian-pdf/{idTransaksiPenitipan}', [TransaksiPembelianController::class, 'notaPembelianPdf'])->name('nota.pembelian.pdf');
Route::put('/transaksi-pembelian/{noNota}/status', [TransaksiPembelianController::class, 'updateStatus']);

Route::get('/showAllTransaksi', [TransaksiPembelianController::class, 'showAllTransaksiPembeli']);




Route::get('/point-redemptions', [PointRedemptionController::class, 'index']);
Route::post('/point-redemptions', [PointRedemptionController::class, 'store']);
Route::delete('/point-redemptions/{id}', [PointRedemptionController::class, 'destroy']);

Route::get('/transaksiPenitipan', [TransaksiPenitipanController::class, 'index']);
Route::post('/addTransaksiPenitipan',[TransaksiPenitipanController::class, 'store']);
Route::get('/pegawaiGethunters', [PegawaiController::class, 'getHunters']);
Route::get('/getpenitip', [PenitipController::class, 'getPenitip']);
Route::post('/barang',[BarangController::class,'store']);
Route::get('/indexall',[BarangController::class,'indexall']);

Route::post('/addimages', [ImagesBarangController::class, 'store']);
Route::get('/generate-idbarang', [BarangController::class, 'generateIdBarang']);
Route::get('/nota-penitipan/{id}/pdf', [TransaksiPenitipanController::class, 'notaPenitipanPdf']);
Route::get('/barang/simple/{idBarang}', [BarangController::class, 'showIdPenitipAndBarang']);
Route::post('/rating',[RatingController::class,'store']);
Route::get('/rating/average/{idTarget}', [RatingController::class, 'getAverageRating']);
Route::get('/livecode/{id}', [TransaksiPenitipanController::class, 'getallbyid']);

Route::get('/add30/{id}', [TransaksiPenitipanController::class, 'add30']);
Route::get('/getClaim', [ClaimMerchandiseController::class, 'index']);
Route::get('/getMerch', [MerchandiseController::class, 'index']);
Route::put('/saveClaim/{id}', [ClaimMerchandiseController::class, 'update']);

