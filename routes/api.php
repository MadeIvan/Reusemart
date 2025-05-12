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


Route::post('penitip/login', [PenitipController::class, 'login']);
Route::get('/penitip', [PenitipController::class, 'getAllPenitip']);
Route::post('/penitip/register', [PenitipController::class, 'register']);
Route::put('/penitip/update/{id}', [PenitipController::class, 'updatePenitip']);
Route::delete('/penitip/delete/{id}', [PenitipController::class, 'deletePenitip']);


Route::get('/dompet', [DompetController::class, 'getAllDompet']); 
Route::get('/dompet/{id}', [DompetController::class, 'getDompetById']);
Route::post('/dompet', [DompetController::class, 'createDompet']);
Route::put('/dompet/{id}', [DompetController::class, 'updateDompet']);
Route::delete('/dompet/{id}', [DompetController::class, 'deleteDompet']);



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
Route::get('/pegawai', [PegawaiController::class, 'index']);
Route::put('/pegawai/update/{id}', [PegawaiController::class, 'update']);
Route::delete('/pegawai/{id}', [PegawaiController::class, 'softDelete']);


Route::post('/penitip/login', [PenitipController::class, 'login']);
Route::post('/penitip/register', [PenitipController::class, 'register']);
Route::get('/check-nik', [PenitipController::class, 'checkNIK']);
// Route::post('/pembeli/lupa-password', [ForgotPasswordPembeliController::class, 'sendResetLinkEmail']);
Route::put('/ganti-password/{id}', [PenitipController::class, 'changePassword']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
});

Route::middleware(['auth:pegawai','role:2'])->group(function () {
    Route::get('/organisasi', [OrganisasiController::class, 'index']);
    Route::get('/organisasi/search', [OrganisasiController::class, 'show']);
    Route::put('/organisasi/update/{id}', [OrganisasiController::class, 'update']);
    Route::delete('/organisasi/delete/{id}', [OrganisasiController::class, 'destroy']);
    Route::put('/pegawai/reset-password/{id}', [PegawaiController::class, 'resetPassword']);
    // Route::get('/organisasi', [PegawaiController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'auth.pembeli'])->group(function () {
    Route::post('/pembeli/buat-alamat', [AlamatController::class, 'store']);
    Route::get('/pembeli/alamat', [AlamatController::class, 'index']);
    Route::get('/pembeli/alamat/search', [AlamatController::class, 'show']);
    Route::put('/pembeli/alamat/update/{id}', [AlamatController::class, 'update']);
    Route::delete('/pembeli/alamat/delete/{id}', [AlamatController::class, 'delete']);
    Route::put('/pembeli/alamat/set-default/{id}', [AlamatController::class, 'setAsDefault']);
    Route::post('/pembeli/buat-diskusi/{id}', [DiskusiController::class, 'store']);

});

Route::middleware(['auth:penitip'])->group(function () {
    Route::get('/penitip/dashboard', [PenitipController::class, 'show']);
    Route::get('/penitip/profile', [PenitipController::class, 'myData']);
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/penitip/history', [PenitipController::class, 'loadBarang']);
// 
});

// Route::get('/pembeli/alamat', [AlamatController::class, 'index'])->middleware(['pembeli']);

Route::get('/getBarang',[BarangController::class, 'index']);
Route::get('/getBarang/{id}', [BarangController::class, 'show']);
Route::get('/diskusi/{id}',[DiskusiController::class, 'getByBarang']);


// Route::middleware('auth:sanctum')->post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store']);
// Route::post('/reset-password', [NewPasswordController::class, 'store']);
// Route::middleware('auth:sanctum')->get('/email/verify-status', EmailVerificationPromptController::class);
// Route::middleware(['auth:sanctum', 'signed'])->get('/email/verify/{id}/{hash}', VerifyEmailController::class)
//      ->name('verification.verify');
// Route::middleware('auth:sanctum')->put('/password/update', [PasswordController::class, 'update']);

