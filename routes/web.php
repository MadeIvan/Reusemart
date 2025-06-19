<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
// use App\Http\Controllers\RequestDonasiController;
use App\Http\Controllers\TransaksiDonasiController;
use App\Http\Controllers\TransaksiPenitipanController;
use Illuminate\Support\Facades\Password;

use App\Http\Controllers\TransaksiPembelianController;




use App\Http\Controllers\RequestDonasiController;
use App\Models\TransaksiPembelian;


use App\Services\FCMService;

// Route::get('/', function () {
//     return view('Pegawai.Merch');
// });
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
Route::get('/Pegawailogin', function () {
    return view('Login.loginPegawai');
});
// Code 2:
Route::get('/pegaawilogin', function () {
    return view('Login.loginPegawai');
});

Route::get('/pegawai/PenitipData', function () {
    return view('Pegawai.CsView');
});
Route::get('/Pegawai/TopSeller', function () {
    return view('Pegawai.TopSeller');
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


Route::get('/pegawaiview', function () {

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




Route::get('/pembeli/HistoryPembeli', function () {
    return view('Pembeli.HistoryPembeli');
});


Route::get('/pembeli/MyProfile', function () {
    return view('Pembeli.profilePembeli');
});

Route::get('/merchandise', function () {
    return view('Pegawai.Merch');
});

Route::get('/requestDonasi', function () {
    return view('Owner.laporanReqDonasi');
});
Route::get('/laporanRequestDonasi/pdf', [RequestDonasiController::class, 'notaReqPdf'])->name('nota.pdf.laporanrequestdonasi');



Route::get('/pegawai/penjualankategori', function () {
    return view('Pegawai.Owner.PenjualanKategori');
});

Route::get('/laporan-per-kategori-barang', [TransaksiPembelianController::class, 'laporanPerKategoriBarang']);

Route::get('/laporan-penitipan-habis', [\App\Http\Controllers\TransaksiPenitipanController::class, 'laporanPenitipanHabis']);
// Route::get('/products', [BarangController::class, 'index']);

Route::get('/pegawai/laporan-per-kategori-barang', [TransaksiPembelianController::class, 'laporanPerKategoriBarang'])->name('pegawai.laporanPerKategoriBarang');

Route::get('/pegawai/penitipanhabis', function () {
    return view('Pegawai.Owner.showPenitipanHabis');
});
Route::get('/pegawai/laporan-penitipan-habis', [TransaksiPenitipanController::class, 'laporanPenitipanHabis'])->name('pegawai.laporanPenitipanHabis');

Route::get('/laporanStok', function () {
    return view('Owner.LaporanStok');
});
Route::get('/laporanStok/pdf', [BarangController::class, 'notaReqPdf'])->name('nota.pdf.barang');
// Route::get('/laporanStok/pdf', [BarangController::class, 'notaReqPdf'])->name('nota.pdf.laporanrequestdonasi');
// Route::get('/laporanRequestDonasi/pdf', [RequestDonasiController::class, 'notaReqPdf'])->name('nota.pdf.laporanrequestdonasi');
Route::get('/laporanKomisi', function () {
    return view('Owner.Komisi');
});
Route::get('/laporanKomisi/pdf', [BarangController::class, 'notaReqPdf2'])->name('nota.pdf.laporanKomisi');
// Route::get('/products', [BarangController::class, 'index']);

Route::get('/laporanPenjualan', function () {
    return view('Owner.Penjualan');
});
Route::get('/laporanPenjualan/pdf', [TransaksiPembelianController::class, 'notaReqPdf'])->name('nota.pdf.laporanPenjualan');


