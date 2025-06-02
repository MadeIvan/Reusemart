<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\TransaksiPembelianController;
 

Route::get('/', function () {
    return view('utama');
});
Route::get('/home', function () {
    return view('home');
});

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

// Code 1:
Route::get('/PegawaiLogin', function () {
    return view('Login.loginPegawai');
});
// Code 2:
// Route::get('/PegawaiLogin', function () {
//     return view('Login.PegawaiLogin');
// });

Route::get('/pegawai/PenitipData', function () {
    return view('Pegawai.CsView');
});

Route::get('/pegawaidata', function () {
    return view('Pegawai.MainPegawai');
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

Route::get('/lupa-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

// Route::get('/pegawaiView', function () {
//     return view('pegawaiView');
// });

Route::get('/penitip/profile', function () {
    return view('Penitip.profilePenitip');
});
Route::get('/keranjang', function () {
    return view('Pembeli.keranjang');
});
Route::get('/checkout', function () {
    return view('Pembeli.checkout');
});
Route::get('/pembayaran/{noNota}', function () {
    return view('Pembeli.pembayaran');
});

Route::get('/verifikasi', function () {
    return view('Pegawai.verifikasiPembayaran');
});

Route::get('/nota-penitipan/${noNota}', function () {
    return view('nota.pdf.nota_pembelian');
});

// PegawaiGudang special views (only code 1)
Route::get('/pegawai/gudangview', function () {
    return view('PegawaiGudang.gudangView');
});
Route::get('/pegawai/penjadwalan', function () {
    return view('PegawaiGudang.penjadwalanBarang');
});
Route::get('/nota-pembelian-pdf/{noNota}', [TransaksiPembelianController::class, 'notaPembelianPdf']);

Route::get('/pegawai/ViewNota', function () {
    return view('PegawaiGudang.NotaPembelian');
});


// Route::get('/products', [BarangController::class, 'index']);
