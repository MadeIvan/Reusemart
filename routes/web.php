<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenitipLoginController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('penitip/login', [PenitipLoginController::class, 'login']);

Route::get('/organisasi/register', function () {
    return view('registerorganisasi');
});

Route::get('/pegawai/login', function () {
    return view('loginPegawai');
});

Route::get('/organisasi', function () {
    return view('showOrganisasi');
});

Route::get('/pembeli/register', function () {
    return view('registerPembeli');
});

Route::get('/pembeli/login', function () {
    return view('loginPembeliBuatCekAja');
});

Route::get('/pembeli/alamat', function () {
    return view('showAlamat');
});

Route::get('/penitip/dashboard', function () {
    return view('dashboardPenitip');
});

Route::get('/penitip/profile', function () {
    return view('profilePenitip');
});

Route::get('/penitip/register', function () {
    return view('registerPenitip');
});

Route::get('/penitip/login', function () {
    return view('loginPenitipBuatCek');
});