<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\RequestDonasiController;
use App\Http\Controllers\TransaksiDonasiController;
use App\Http\Controllers\TransaksiPenitipanController;
use Illuminate\Support\Facades\Password;
use App\Services\FCMService;

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
Route::get('/pembeli/dashboard', function () {
    return view('Pembeli.dashboard');
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

Route::get('/pegawaiView', function () {
    return view('pegawaiView');
});

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

// PegawaiGudang special views (only code 1)
Route::get('/pegawai/gudangview', function () {
    return view('PegawaiGudang.gudangView');
});
Route::get('/pegawai/penjadwalan', function () {
    return view('PegawaiGudang.penjadwalanBarang');
});
Route::get('/nota-penjualan/{noNota}/pdf', [TransaksiPembelianController::class, 'notaPenjualanPdf']);

Route::get('/pegawai/ViewNota', function () {
    return view('PegawaiGudang.NotaPembelian');
});
Route::get('/pegawai/dibayar', function () {
    return view('Pegawai.newCode');
});
Route::get('/requestDonasi', function () {
    return view('Owner.laporanReqDonasi');
});
Route::get('/donasi', function () {
    return view('Owner.laporanDonasi');
});
Route::get('/laporanPenitip', function () {
    return view('Owner.laporanPenitip');
});

Route::get('/resetPassword', function () {
    return view('Login.resetPassword');
});

Route::get('/newPassword', function () {
    return view('Login.newPassword');
});

Route::get('/laporanRequestDonasi/pdf', [RequestDonasiController::class, 'notaReqPdf'])->name('nota.pdf.laporanrequestdonasi');
Route::get('/laporanDonasiAcc/pdf', [TransaksiDonasiController::class, 'laporanDonasiPdf'])->name('nota.pdf.laporanTransaksiDonasi');
Route::get('/laporanPenitip/pdf', [TransaksiPenitipanController::class, 'laporanTransaksiPenitipPdf'])->name('nota.pdf.laporanUntukPenitip');




// Route::get('/products', [BarangController::class, 'index']);

// routes/web.php

// Route::get('/test-fcm', function () {
//     $fcmToken = 'fqGedr4nTueD1UF_L1iOQZ:APA91bEbz2pWuk1XjpWiUT8aOD13bEpG0KToPpv98i9FC8C64ls4SEfTV9smpSkw7sq1tmV85MeDFGyHDqj8LZgQ17DWmyHY4s8wwD6CQ_TX0qKTg9BIMMA'; // Ganti dengan token FCM yang valid dari DB
//     app(FCMService::class)->sendNotification(
//         $fcmToken,
//         'Test Judul',
//         'Isi Pesan Test'
//     );

//     return 'Notifikasi test sudah dikirim, cek HP kamu.';
// });
