<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\PenitipLoginController;
use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Password;




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
    return view('PegawaiLogin');
});
Route::get('/home', function () {
    return view('home');
});

// routes/web.php
// Route::get('/getBarang/{id}', [BarangController::class, 'show']);

Route::get('/getBarang/{id}', function () {
    return view('detailBarang');
});


// Route::post('penitip/login', [PenitipLoginController::class, 'login']);

Route::get('/organisasi/register', function () {
    return view('Register.registerorganisasi');
});

// Route::get('/pegawai/login', function () {
//     return view('loginPegawai');
// });

Route::get('/organisasi', function () {
    return view('showOrganisasi');
});

Route::get('/pembeli/register', function () {
    return view('Register.registerPembeli');
});

Route::get('/UsersLogin', function () {
    return view('Login.UsersLogin');
});


Route::get('/PegawaiLogin', function () {
    return view('Login.PegawaiLogin');
});
Route::get('/pegawai/PenitipData', function () {
    return view('Pegawai.CsView');
});

Route::get('/pembeli/alamat', function () {
    return view('Pembeli.showAlamat');
});

Route::get('/penitip/dashboard', function () {
    return view('Penitip.dashboardPenitip');
});

// Route::get('/penitip/profile', function () {
//     return view('profilePenitip');
// });

Route::get('/penitip/register', function () {
    return view('Register.registerPenitip');
});


Route::get('/penitip/history', function () {
    return view('Penitip.historyPenitip');
});

Route::get('/lupa-password', function () {
    return view('forgotPassword');
});
Route::get('/OrganisasiMain', function () {
    return view('OrganisasiView');
});
Route::post('/lupa-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
});
 
//     $status = Password::sendResetLink(
//         $request->only('email')
//     );
 
//     return $status === Password::ResetLinkSent
//         ? back()->with(['status' => __($status)])
//         : back()->withErrors(['email' => __($status)]);
// })->middleware('guest')->name('password.email');

Route::get('/lupa-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

// Route::get('/pegawaiView', function () {
//     return view('pegawaiView');
// });

Route::get('/penitip/profile', function () {
    return view('Penitip.profilePenitip');
});

Route::get('/OrganisasiMain', function () {
    return view('OrganisasiView');
});

Route::get('/keranjang', function () {
    return view('Pembeli.keranjang');
});
Route::get('/checkout', function () {
    return view('Pembeli.checkout');
});
Route::get('/gudangview', function () {
    return view('gudangView');
});

// routes/web.php
// Route::get('/getBarang/{id}', [BarangController::class, 'show'])->name('product.show');




