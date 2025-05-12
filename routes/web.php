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
Route::get('/UsersLogin', function () {
    return view('UsersLogin');
});

Route::get('/pegawai/PegawaiLogin', function () {
    return view('PegawaiLogin');
});
Route::get('/pegawai/PenitipData', function () {
    return view('Pegawai.CsView');
});
// Route::post('penitip/login', [PenitipLoginController::class, 'login']);