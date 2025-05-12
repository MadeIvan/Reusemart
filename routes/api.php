<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AlamatController;
use App\Http\Middleware\PembeliMiddleware;
use App\Http\Middleware\JabatanMiddleware;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\BarangController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



// Route::aliasMiddleware('pembeli', PembeliMiddleware::class);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::resource('organisasi', OrganisasiController::class);

Route::post('/organisasi/register', [OrganisasiController::class, 'register']);
Route::post('/organisasi/login', [OrganisasiController::class, 'login']);
Route::get('/check-email-username', [OrganisasiController::class, 'checkEmailUsername']);

Route::post('/pembeli/register', [PembeliController::class, 'register']);
Route::post('/pembeli/login', [PembeliController::class, 'login']);
Route::get('/check-email-username', [PembeliController::class, 'checkEmailUsername']);

Route::post('/pegawai/register', [PegawaiController::class, 'register']);
Route::post('/pegawai/login', [PegawaiController::class, 'login']);
Route::put('/pegawai/update/{id}', [PegawaiController::class, 'update']);
Route::get('/pegawai', [PegawaiController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
});

Route::middleware(['auth:pegawai','role:2'])->group(function () {
    Route::get('/organisasi', [OrganisasiController::class, 'index']);
    Route::get('/organisasi/search', [OrganisasiController::class, 'show']);
    Route::put('/organisasi/update/{id}', [OrganisasiController::class, 'update']);
    Route::delete('/organisasi/delete/{id}', [OrganisasiController::class, 'destroy']);
});

Route::middleware(['auth:pembeli'])->group(function () {
    Route::post('/pembeli/alamat', [AlamatController::class, 'store']);
    Route::get('/pembeli/alamat/', [AlamatController::class, 'index']);
    Route::get('/pembeli/alamat/search', [AlamatController::class, 'show']);
    Route::put('/pembeli/alamat/update/{id}', [AlamatController::class, 'update']);
    Route::delete('/pembeli/alamat/delete/{id}', [AlamatController::class, 'delete']);
    Route::put('/pembeli/alamat/set-default/{id}', [AlamatController::class, 'setAsDefault']);
});

// Route::get('/pembeli/alamat', [AlamatController::class, 'index'])->middleware(['pembeli']);

Route::get('/getBarang',[BarangController::class, 'index']);
Route::get('/getBarang/{id}', [BarangController::class, 'show']);

