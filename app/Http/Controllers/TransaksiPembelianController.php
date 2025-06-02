<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\TransaksiPembelian;
use App\Models\DetailTransaksiPembelian;
use App\Models\Barang;
use App\Models\Pembeli;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\FCMService;

class TransaksiPembelianController extends Controller
{
    // ===== Methods from code 2 (TransaksiPembelianController) =====
    public function showPenjadwalan(){
    $pembelians = TransaksiPembelian::with([
        'detailTransaksiPembelian.barang',
        'pegawai',
        'pegawai2',
        'pegawai3',
        'pembeli',
        'pembeli.alamat',
        
    ])
    ->where('status', 'Lunas Belum Dijadwalkan')
    ->get();

    return response()->json($pembelians);
}
    public function showfornota(){
    $pembelians = TransaksiPembelian::with([
        'detailTransaksiPembelian.barang',
        'pegawai',
        'pegawai2',
        'pegawai3',
        'pembeli',
        'pembeli.alamat',
        
    ])
    ->where('status', 'Lunas Siap')
    ->get();

    return response()->json($pembelians);
}


    public function store(Request $request){
        try {
            \Log::info('Data masuk:', $request->all());

            $validated = $request->validate([
                'idAlamat' => 'nullable|string',
                'tanggalWaktuPembelian' => 'required|date_format:Y-m-d H:i:s',
                'totalHarga' => 'required|numeric',
                'id_barang' => 'required|array',
                'id_barang.*' => 'string',
                'sisaPoin' => 'nullable|numeric',
            ]);

            // Cek apakah pembeli sudah login
            $pembeli = auth('pembeli')->user();
            if (!$pembeli) {
                return response()->json([
                    "status" => false,
                    "message" => "Pembeli Belum Login",
                    "data" => null,
                ], 400);
            }
            
            \Log::info('sisaPoin value:', ['value' => $request->input('sisaPoin'), 'type' => gettype($request->input('sisaPoin'))]);

            DB::beginTransaction();
            $yearMonth = date('Y.m');

            // $last = TransaksiPembelian::where('noNota', 'like', "$yearMonth.%")
            //     ->orderBy('noNota', 'desc')
            //     ->first();
            $last = TransaksiPembelian::orderByRaw("CAST(SUBSTRING_INDEX(noNota, '.', -1) AS UNSIGNED) DESC")
                ->first();

            $lastNumber = 0;
            if ($last) {
                $parts = explode('.', $last->noNota);
                $lastNumber = intval(end($parts));
            }
            $newId = $yearMonth . '.' . ($lastNumber + 1);

            \Log::info("ID yang akan digunakan sebagai noNota:", [$newId]);

            // Simpan transaksi utama
            if($validated['totalHarga'] === 0){
                $transaksiPembelian = TransaksiPembelian::create([
                    'noNota' => $newId,
                    'idPembeli' => $pembeli->idPembeli,
                    'idAlamat' => $validated['idAlamat'],
                    'tanggalWaktuPembelian' => $validated['tanggalWaktuPembelian'],
                    'status' => "LUNAS BELUM DIJADWALKAN",
                    'totalHarga' => $validated['totalHarga']
                ]);
                
            }else{
                $transaksiPembelian = TransaksiPembelian::create([
                    'noNota' => $newId,
                    'idPembeli' => $pembeli->idPembeli,
                    'idAlamat' => $validated['idAlamat'],
                    'tanggalWaktuPembelian' => $validated['tanggalWaktuPembelian'],
                    'status' => "Menunggu Pembayaran",
                    'totalHarga' => $validated['totalHarga']
                ]);
            }

            \Log::info("transaksiPembelian", [$transaksiPembelian]);

            $idBarangs = $validated['id_barang'];
            // Simpan detail barang
            foreach ($validated['id_barang'] as $idBarang) {
                \Log::info("ID Barang:", [$idBarang]);
                

                DB::table('detailtransaksipembelian')->insert([
                    'noNota' => $newId,
                    'idBarang' => $idBarang
                ]);

                DB::table('barang')->where('idBarang', $idBarang)->update([
                    'statusBarang' => 'Terjual'
                ]);
            }

            DB::table('pembeli')->where('idPembeli', $pembeli->idPembeli)->update([
                'poin' => $validated['sisaPoin']
            ]);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Transaksi berhasil dibuat",
                "data" => $transaksiPembelian
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error saat simpan transaksi: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            return response()->json([
                "status" => false,
                "message" => "Terjadi kesalahan: " . $e->getMessage(),
                "data" => null
            ], 500);
        }
    }
// In TransaksiPembelianController.php
// public function updatePenjadwalan(Request $request, $noNota)
// {
//     $transaksi = TransaksiPembelian::where('noNota', $noNota)->first();

//     if (!$transaksi) {
//         return response()->json(['status' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
//     }

//     $transaksi->status = $request->input('status');
    
//     // If a kurir is selected, assign to idPegawai3 (assuming this is the kurir column)
//     if ($request->has('idKurir')) {
//         $transaksi->idPegawai3 = $request->input('idKurir');
//     }
//     // (Optional) If you want to store kurirNama
//     // $transaksi->kurirNama = $request->input('kurirNama');
    
//     // (Optional) If you want to store scheduled date
//     $transaksi->tanggalPengirimanPengambilan = $request->input('tanggalKirim');

//     $transaksi->save();

//     return response()->json(['status' => true, 'message' => 'Penjadwalan berhasil disimpan']);
// }

public function updatePenjadwalan(Request $request, $noNota)
{
    $transaksi = TransaksiPembelian::with([
        'detailTransaksiPembelian.barang.detailTransaksiPenitipan.transaksiPenitipan.penitip',
        'pembeli'
    ])->where('noNota', $noNota)->first();

    if (!$transaksi) {
        return response()->json(['status' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
    }

    // Simpan id kurir sebelumnya sebelum diubah
    $kurirLama = $transaksi->idPegawai3;

    // Update status dan tanggal pengiriman
    $transaksi->status = $request->input('status');
    $transaksi->tanggalPengirimanPengambilan = $request->input('tanggalKirim');

    // Update kurir jika dikirimkan
    if ($request->has('idKurir')) {
        $transaksi->idPegawai3 = $request->input('idKurir');
    }

    $transaksi->save();

    // === Ambil data terkait ===
    $detailBarang = $transaksi->detailTransaksiPembelian->first();
    $barang = $detailBarang?->barang;
    $penitip = $barang?->detailTransaksiPenitipan?->transaksiPenitipan?->penitip;
    $pembeli = $transaksi->pembeli;
    $kurir = \App\Models\Pegawai::find($transaksi->idPegawai3);

    // Cek apakah barang diantar atau diambil
    $isDiantar = !is_null($transaksi->idAlamat);
    $tanggal = \Carbon\Carbon::parse($transaksi->tanggalPengirimanPengambilan)->format('d M Y');

    // === Kirim notifikasi ke PENITIP ===
    if ($penitip && $penitip->fcm_token) {
        $pesanPenitip = $isDiantar
            ? "Barang titipan Anda akan segera dikirim ke pembeli oleh kurir pada tanggal $tanggal."
            : "Barang titipan Anda akan segera diambil oleh pembeli pada tanggal $tanggal.";

        app(FCMService::class)->sendNotification(
            $penitip->fcm_token,
            'Jadwal Pengiriman Barang',
            $pesanPenitip
        );
    }

    // === Kirim notifikasi ke PEMBELI ===
    if ($pembeli && $pembeli->fcm_token) {
        $pesanPembeli = $isDiantar
            ? "Barang Anda akan segera diantar oleh kurir pada tanggal $tanggal."
            : "Silakan ambil barang Anda pada tanggal $tanggal.";

        app(FCMService::class)->sendNotification(
            $pembeli->fcm_token,
            'Jadwal Pengambilan/Pengiriman',
            $pesanPembeli
        );
    }

    // === Kirim notifikasi ke KURIR ===
    if ($isDiantar && $kurir && $kurir->fcm_token) {
        app(FCMService::class)->sendNotification(
            $kurir->fcm_token,
            'Tugas Pengiriman Baru',
            "Segera antarkan barang ke pembeli pada tanggal $tanggal."
        );
    }

    // === Notifikasi tambahan jika kurir baru ditentukan ===
    if (is_null($kurirLama) && $kurir) {
        if ($penitip && $penitip->fcm_token) {
            app(FCMService::class)->sendNotification(
                $penitip->fcm_token,
                'Kurir Ditugaskan',
                'Kurir telah ditugaskan untuk mengirimkan barang titipan Anda.'
            );
        }

        if ($pembeli && $pembeli->fcm_token) {
            app(FCMService::class)->sendNotification(
                $pembeli->fcm_token,
                'Kurir Ditugaskan',
                'Kurir telah ditugaskan. Barang Anda akan segera dikirimkan.'
            );
        }
    }

    return response()->json(['status' => true, 'message' => 'Penjadwalan berhasil disimpan']);
}



public function notaPenjualanPdf($noNota)
{
    $transaksi = TransaksiPembelian::with([
        'detailTransaksiPembelian.barang',
        'pembeli.alamat',
        'pegawaiQc', // Add the relationships you need
        // ...add kurir, etc.
    ])->where('noNota', $noNota)->firstOrFail();

    // You may need to prepare extra values, e.g., points, discount, etc.

    return Pdf::loadView('pdf.nota_penjualan', compact('transaksi'))
        ->stream("nota-penjualan-{$noNota}.pdf"); // use ->download() if you want forced download
}

 public function getDataTerbaru(Request $request){
        $pembeli = auth('pembeli')->user();
            if (!$pembeli) {
                return response()->json([
                    "status" => false,
                    "message" => "Pembeli Belum Login",
                    "data" => null,
                ], 400);
            }
        $yearMonth = date('Y.m');
        $last = TransaksiPembelian::where('noNota', 'like', "$yearMonth.%")
                ->orderBy('noNota', 'desc')
                ->first();

        return response()->json([
            "status" => true,
            "message" => "Berhasil mendapatkan No Nota terbaru",
            "data" => $last
        ]);
    }

    public function buktiBayar(Request $request, $id){
        try {
            $validated = $request->validate([
                'tanggalWaktuPelunasan' => 'required|date_format:Y-m-d H:i:s',
                'buktiPembayaran' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $pembeli = auth('pembeli')->user();
            if (!$pembeli) {
                return response()->json([
                    "status" => false,
                    "message" => "Pembeli Belum Login",
                    "data" => null,
                ], 400);
            }

            // Upload file bukti pembayaran
            $file = $request->file('buktiPembayaran');
            $path = $file->store('buktiPembayaran', 'public');

            $transaksi = TransaksiPembelian::where('noNota', $id)->first();

            if ($transaksi) {
                $transaksi->buktiPembayaran = $path;
                $transaksi->status = 'Menunggu Verifikasi';
                $transaksi->tanggalWaktuPelunasan = $validated['tanggalWaktuPelunasan'];
                $transaksi->save();

                return response()->json([
                    "status" => true,
                    "message" => "Berhasil mengunggah bukti pembayaran",
                    "data" => $transaksi
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Transaksi tidak ditemukan",
                    "data" => null
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Terjadi kesalahan: " . $e->getMessage(),
                "data" => null
            ], 500);
        }
    }


    public function canceled(Request $request, $id){
        try{
            ///////////cek pembeli///////////
            $pembeli = auth('pembeli')->user();
            if (!$pembeli) {
                return response()->json([
                    "status" => false,
                    "message" => "Pembeli Belum Login",
                    "data" => null,
                ], 400);
            }

            $validated = $request->validate([
                'poinAwal' => 'required|numeric',
                'id_barang' => 'required|array',
                'id_barang.*' => 'string',
                'status' => 'required|string',
            ]);

            //////////////////////ubah ke sebelum transaksi/////////////////////
            DB::beginTransaction();
            $idBarangs = $validated['id_barang'];
            // Simpan detail barang
            foreach ($validated['id_barang'] as $idBarang) {
                \Log::info("ID Barang:", [$idBarang]);

                DB::table('barang')->where('idBarang', $idBarang)->update([
                    'statusBarang' => 'Tersedia'
                ]);
            }

            DB::table('pembeli')->where('idPembeli', $pembeli->idPembeli)->update([
                'poin' => $validated['poinAwal']
            ]);

            DB::commit();

            $transaksi = TransaksiPembelian::where('noNota', $id)->first();
            $transaksi->status = $validated['status'];
            $transaksi->save();
            return response()->json([
                "status" => true,
                "message" => "Pesanan dibatalkan",
                "data" => $transaksi
            ], 200);
        }catch (Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Terjadi kesalahan: " . $e->getMessage(),
                "data" => null
            ], 500);
        }
    }

    public function getNotConfirmed(){
        try{
            $data = TransaksiPembelian::where('status', 'Menunggu Verifikasi')->get();
            return response()->json([
                "status" => true,
                "message" => "Get successful",
                "data" => $data
            ], 200);
        }catch(Exception $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 400);
        }
    }

    public function terimaPembayaran(Request $request, $id){
        try{
            $pegawai = auth('pegawai')->user();
            if (!$pegawai) {
                return response()->json([
                    "status" => false,
                    "message" => "Pegawai Belum Login",
                    "data" => null,
                ], 400);
            }

            $idPegawai = $pegawai->idPegawai;

            $validated = $request->validate([
                'status' => 'required|string',
            ]);

            $transaksi = TransaksiPembelian::where('noNota', $id)->first();
            $transaksi->status = $validated['status'];
            $transaksi->idPegawai1 = $idPegawai;
            $transaksi->save();

           if ($validated['status'] === 'LUNAS BELUM DIJADWALKAN') {
                $detailBarang = $transaksi->detailTransaksiPembelian()->first();
                if ($detailBarang) {
                    $barang = $detailBarang->barang;
                    $penitip = $barang?->detailTransaksiPenitipan?->transaksiPenitipan?->penitip;   
                    \Log::info('FCM Token Penitip: ' . $penitip);

                    if ($penitip && $penitip->fcm_token) {
                        app(FCMService::class)->sendNotification(
                            $penitip->fcm_token,
                            'Barang Terjual!',
                            'Selamat! Barang Anda telah berhasil terjual.'
                        );
                        \Log::info('FCM Token Penitip: ' . $penitip->fcm_token);
                    } else {
                        \Log::info('Penitip tidak ditemukan atau token kosong');
                    }
                }
            }

            return response()->json([
                "status" => true,
                "message" => "Update successful",
                "data" => $transaksi
            ], 200);
        }catch(Exception $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 400);
        }
    }

public function tolakVerifikasi(Request $request, $noNota)

    {
        try {
            $pegawai = auth('pegawai')->user();
            if (!$pegawai) {
                return response()->json([
                    "status" => false,
                    "message" => "Pegawai belum login",
                    "data" => null,
                ], 400);
            }

            $transaksi = TransaksiPembelian::where('noNota', $noNota)->first();
            if (!$transaksi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Transaksi tidak ditemukan.'
                ]);
            }

            $pembeli = Pembeli::where('idPembeli', $transaksi->idPembeli)->first();
            $alamat = $transaksi->idAlamat;

            $detailBarang = DetailTransaksiPembelian::where('noNota', $noNota)->get();
            $totalHarga = 0;

            foreach ($detailBarang as $detail) {
                \Log::info("Detail ID Barang: " . $detail->idBarang);
                $barang = Barang::where('idBarang', $detail->idBarang)->first();

                if ($barang) {
                    \Log::info("Barang ditemukan: " . $barang->idBarang . " | Harga: " . $barang->hargaBarang);
                    $totalHarga += $barang->hargaBarang;
                    $barang->update([
                        'statusBarang' => 'Tersedia'
                    ]);
                }
            }

            // $poinAkhir = $pembeli->poin;
            // $poinAwal = $poinAkhir;


           if(($alamat !== null && $totalHarga >= 1500000) || ($alamat === null && $totalHarga >= 1500000)){

                $poinBelanja = $totalHarga / 10000;
                $poinBonus = $poinBelanja * 0.2;
                $selisihHarga = abs($totalHarga - $transaksi->totalHarga);
                $poinTukar = $selisihHarga / 100;
                $poinAkhir = $pembeli->poin;
                \Log::info("poinAkhir: " . $pembeli->poin);
    
                $poinAwal = $poinAkhir - $poinBonus - $poinBelanja + $poinTukar;
                
                $pembeli->poin = $poinAwal;
                $pembeli->save();
                $transaksi->status = 'Dibatalkan (Bukti Tidak Valid)';
                $transaksi->save();
                \Log::info("=== DEBUG ===");
                \Log::info("poinAwal: $poinAwal");
                \Log::info("poinBelanja: $poinBelanja");
                \Log::info("poinBonus: $poinBonus");
                \Log::info("poinTukar: $poinTukar");
            }else if(($alamat === null && $totalHarga < 1500000)){
                if($totalHarga >= 500000){
                    $poinBelanja = $totalHarga / 10000;
                    $poinBonus = $poinBelanja * 0.2;
                    $selisihHarga = abs(($transaksi->totalHarga)  -  $totalHarga);
                    $poinTukar = $selisihHarga / 100;
                    $poinAkhir = $pembeli->poin;
                    \Log::info("=== DEBUG ===");
                    \Log::info("poinAkhir: " . $pembeli->poin);

                    $poinAwal = $poinAkhir - $poinBonus - $poinBelanja + $poinTukar;
                    
                    $pembeli->poin = $poinAwal;
                    $pembeli->save();
                    $transaksi->status = 'Dibatalkan (Bukti Tidak Valid)';
                    $transaksi->save();
                }else{
                    $poinBelanja = $totalHarga / 10000;
                    $selisihHarga = abs($totalHarga - ($transaksi->totalHarga));
                    $poinTukar = $selisihHarga / 100;
                    $poinAkhir = $pembeli->poin;
                    \Log::info("=== DEBUG ===");
                    \Log::info("poinAkhir: " . $pembeli->poin);
    
                    $poinAwal = $poinAkhir - $poinBelanja + $poinTukar;
                    
                    $pembeli->poin = $poinAwal;
                    $pembeli->save();
                    $transaksi->status = 'Dibatalkan (Bukti Tidak Valid)';
                    $transaksi->save();
                }
                //($alamat !== null && $totalHarga < 1500000) || 
                \Log::info("poinAwal: $poinAwal");
                \Log::info("totalHargaBarang: " .  $totalHarga);
                \Log::info("totalHarga: $transaksi->totalHarga");
                \Log::info("selisihHarga:  $selisihHarga");
                \Log::info("poinAkhir: " . $pembeli->poin);
                \Log::info("poinBelanja: $poinBelanja");
                \Log::info("poinTukar: $poinTukar");
            }else if($alamat !== null && $totalHarga < 1500000){
                if($totalHarga >= 500000){
                    $poinBelanja = $totalHarga / 10000;
                    $poinBonus = $poinBelanja * 0.2;
                    $selisihHarga = abs($totalHarga - ($transaksi->totalHarga));
                    $poinTukar = $selisihHarga / 100;
                    $poinAkhir = $pembeli->poin;
                    \Log::info("=== DEBUG ===");
                    \Log::info("poinAkhir: " . $pembeli->poin);

                    $poinAwal = $poinAkhir - $poinBonus - $poinBelanja + $poinTukar;
                    
                    $pembeli->poin = $poinAwal;
                    $pembeli->save();
                    $transaksi->status = 'Dibatalkan (Bukti Tidak Valid)';
                    $transaksi->save();
                }else{
                    $poinBelanja = $totalHarga / 10000;
                    $selisihHarga = abs($totalHarga - ($transaksi->totalHarga));
                    $poinTukar = $selisihHarga / 100;
                    $poinAkhir = $pembeli->poin;
                    \Log::info("=== DEBUG ===");
                    \Log::info("poinAkhir: " . $pembeli->poin);
    
                    $poinAwal = $poinAkhir - $poinBelanja + $poinTukar;
                    
                    $pembeli->poin = $poinAwal;
                    $pembeli->save();
                    $transaksi->status = 'Dibatalkan (Bukti Tidak Valid)';
                    $transaksi->save();
                }
                //($alamat !== null && $totalHarga < 1500000) || 
                \Log::info("poinAwal: $poinAwal");
                \Log::info("totalHargaBarang: " .  $totalHarga);
                \Log::info("totalHarga: $transaksi->totalHarga");
                \Log::info("selisihHarga:  $selisihHarga");
                \Log::info("poinAkhir: " . $pembeli->poin);
                \Log::info("poinBelanja: $poinBelanja");
                \Log::info("poinTukar: $poinTukar");
            }


            $transaksi->status = 'Dibatalkan (Bukti Tidak Valid)';
            $transaksi->idPegawai1 = $pegawai->idPegawai;
            $transaksi->save();

            return response()->json([
                'status' => true,
                'message' => 'Rollback berhasil tanpa kolom tambahan.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 500);
        }
    }
}
