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


public function sumTotalHargaPerMonth()
    {
        // Fetch and group the transactions by month, using tanggalWaktuPelunasan
        $result = TransaksiPembelian::select(
                DB::raw('MONTH(tanggalWaktuPelunasan) as month'),
                DB::raw('YEAR(tanggalWaktuPelunasan) as year'),
                DB::raw('SUM(totalHarga) as total_sum')
            )
            ->whereYear('tanggalWaktuPelunasan', 2025)
            ->groupBy(DB::raw('YEAR(tanggalWaktuPelunasan), MONTH(tanggalWaktuPelunasan)'))
            ->orderBy(DB::raw('YEAR(tanggalWaktuPelunasan), MONTH(tanggalWaktuPelunasan)'))
            ->get();

        // Return the results in JSON format
        return response()->json($result);
    }

public function notaReqPdf()
{
    $result = TransaksiPembelian::select(
                DB::raw('MONTH(tanggalWaktuPelunasan) as month'),
                DB::raw('YEAR(tanggalWaktuPelunasan) as year'),
                DB::raw('SUM(totalHarga) as total_sum')
            )
            ->whereYear('tanggalWaktuPelunasan', 2025)
            ->groupBy(DB::raw('YEAR(tanggalWaktuPelunasan), MONTH(tanggalWaktuPelunasan)'))
            ->orderBy(DB::raw('YEAR(tanggalWaktuPelunasan), MONTH(tanggalWaktuPelunasan)'))
            ->get();
            
    return Pdf::loadView('nota.pdf.laporanPenjualan', compact('result'))
            ->setPaper('a4', 'landscape')
            ->stream("Laporan Penjualan.pdf");
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

    ->whereIn('status', ['Lunas Siap Diambil', 'Lunas Siap Diantarkan','Lunas Belum Dijadwalkan'])
    ->get();

    return response()->json($pembelians);
}

public function notaPembelianPdf($idTransaksiPenitipan)
{
    $transaksi = \App\Models\TransaksiPembelian::with([
        'detailTransaksiPembelian.barang',
        'pegawai',
        'pegawai2',
        'pegawai3',
        'pembeli',
        'pembeli.alamat',
        'pointRedemption'
        
    ])->where('noNota', $idTransaksiPenitipan)->firstOrFail();
    // return response()->json([
    //     'status' => true,
    //     'message' => 'Data transaksi berhasil diambil',
    //     'data' => $transaksi
    // ]);

    return Pdf::loadView('nota.pdf.nota_pembelian', compact('transaksi'))
        ->stream("nota-pembelian-{$idTransaksiPenitipan}.pdf");
}



public function getDibayar(){
    $pembelians = TransaksiPembelian::with([
        'detailTransaksiPembelian.barang',
        'pegawai',
        'pegawai2',
        'pegawai3',
        'pembeli',
        'pembeli.alamat',
    ])
    ->whereIn('status', ['Menunggu Verifikasi'])
    ->get();

    return response()->json($pembelians);
    
}



    public function store(Request $request){
        try {
            // \Log::info('Data masuk:', $request->all());

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
            
            // \Log::info('sisaPoin value:', ['value' => $request->input('sisaPoin'), 'type' => gettype($request->input('sisaPoin'))]);

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

            // \Log::info("ID yang akan digunakan sebagai noNota:", [$newId]);

            // Simpan transaksi utama
            if($validated['totalHarga'] === 0){
                $transaksiPembelian = TransaksiPembelian::create([
                    'noNota' => $newId,
                    'idPembeli' => $pembeli->idPembeli,
                    'idAlamat' => $validated['idAlamat'],
                    'tanggalWaktuPembelian' => $validated['tanggalWaktuPembelian'],
                    'status' => "Lunas Belum Dijadwalkan",
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

            // \Log::info("transaksiPembelian", [$transaksiPembelian]);

            $idBarangs = $validated['id_barang'];
            // Simpan detail barang
            foreach ($validated['id_barang'] as $idBarang) {
                // \Log::info("ID Barang:", [$idBarang]);
                

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
            // \Log::error('Error saat simpan transaksi: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

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
                // \Log::info("ID Barang:", [$idBarang]);

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

           if ($validated['status'] === 'Lunas Belum Dijadwalkan') {
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
                // \Log::info("Detail ID Barang: " . $detail->idBarang);
                $barang = Barang::where('idBarang', $detail->idBarang)->first();

                if ($barang) {
                    // \Log::info("Barang ditemukan: " . $barang->idBarang . " | Harga: " . $barang->hargaBarang);
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
                // \Log::info("poinAkhir: " . $pembeli->poin);
    
                $poinAwal = $poinAkhir - $poinBonus - $poinBelanja + $poinTukar;
                
                $pembeli->poin = $poinAwal;
                $pembeli->save();
                $transaksi->status = 'Dibatalkan (Bukti Tidak Valid)';
                $transaksi->save();
                // \Log::info("=== DEBUG ===");
                // \Log::info("poinAwal: $poinAwal");
                // \Log::info("poinBelanja: $poinBelanja");
                // \Log::info("poinBonus: $poinBonus");
                // \Log::info("poinTukar: $poinTukar");
            }else if(($alamat === null && $totalHarga < 1500000)){
                if($totalHarga >= 500000){
                    $poinBelanja = $totalHarga / 10000;
                    $poinBonus = $poinBelanja * 0.2;
                    $selisihHarga = abs(($transaksi->totalHarga)  -  $totalHarga);
                    $poinTukar = $selisihHarga / 100;
                    $poinAkhir = $pembeli->poin;
                    // \Log::info("=== DEBUG ===");
                    // \Log::info("poinAkhir: " . $pembeli->poin);

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
                    // \Log::info("=== DEBUG ===");
                    // \Log::info("poinAkhir: " . $pembeli->poin);
    
                    $poinAwal = $poinAkhir - $poinBelanja + $poinTukar;
                    
                    $pembeli->poin = $poinAwal;
                    $pembeli->save();
                    $transaksi->status = 'Dibatalkan (Bukti Tidak Valid)';
                    $transaksi->save();
                }
                //($alamat !== null && $totalHarga < 1500000) || 
                // \Log::info("poinAwal: $poinAwal");
                // \Log::info("totalHargaBarang: " .  $totalHarga);
                // \Log::info("totalHarga: $transaksi->totalHarga");
                // \Log::info("selisihHarga:  $selisihHarga");
                // \Log::info("poinAkhir: " . $pembeli->poin);
                // \Log::info("poinBelanja: $poinBelanja");
                // \Log::info("poinTukar: $poinTukar");
            }else if($alamat !== null && $totalHarga < 1500000){
                if($totalHarga >= 500000){
                    $poinBelanja = $totalHarga / 10000;
                    $poinBonus = $poinBelanja * 0.2;
                    $selisihHarga = abs($totalHarga - ($transaksi->totalHarga));
                    $poinTukar = $selisihHarga / 100;
                    $poinAkhir = $pembeli->poin;
                    // \Log::info("=== DEBUG ===");
                    // \Log::info("poinAkhir: " . $pembeli->poin);

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
                    // \Log::info("=== DEBUG ===");
                    // \Log::info("poinAkhir: " . $pembeli->poin);
    
                    $poinAwal = $poinAkhir - $poinBelanja + $poinTukar;
                    
                    $pembeli->poin = $poinAwal;
                    $pembeli->save();
                    $transaksi->status = 'Dibatalkan (Bukti Tidak Valid)';
                    $transaksi->save();
                }
                //($alamat !== null && $totalHarga < 1500000) || 
                // \Log::info("poinAwal: $poinAwal");
                // \Log::info("totalHargaBarang: " .  $totalHarga);
                // \Log::info("totalHarga: $transaksi->totalHarga");
                // \Log::info("selisihHarga:  $selisihHarga");
                // \Log::info("poinAkhir: " . $pembeli->poin);
                // \Log::info("poinBelanja: $poinBelanja");
                // \Log::info("poinTukar: $poinTukar");
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

public function updateStatus(Request $request, $noNota)
{
    
    $transaksi = TransaksiPembelian::where('noNota', $noNota)->first();
    
    if (!$transaksi) {
        return response()->json(['status' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
    }
    $transaksi->status = $request->status;
    $transaksi->save();
    return response()->json([
        'status' => 'success',
        'message' => 'Status berhasil diperbarui',
        'data' => $transaksi,
    ]);
}
public function showAllTransaksiPembeli()
{
    try {
        $transaksi = \App\Models\TransaksiPembelian::with([
            'detailTransaksiPembelian.barang',
            'pembeli.alamat'
        ])
        ->where('status', 'Barang Diterima')
        ->orderBy('tanggalWaktuPembelian', 'desc')
        ->get();

        return response()->json([
            "status" => true,
            "message" => "Get successful",
            "data" => $transaksi
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            "status" => false,
            "message" => $e->getMessage(),
            "data" => null
        ], 500);
    }
}

public function index(){
  $barang= TransaksiPembelian::all();
    return response()->json($barang);

}


    public function getHistoryPengiriman(){
        try{
            $pegawai = auth('pegawai')->user();
            if (!$pegawai) {
                return response()->json([
                    "status" => false,
                    "message" => "Pegawai belum login",
                    "data" => null,
                ], 400);
            }
            $idPegawai = $pegawai->idPegawai;

            $history = TransaksiPembelian::with([
                'detailTransaksiPembelian.barang',
                'pembeli',
            ])
                ->where('idPegawai3', $idPegawai)
                ->where('status', 'Barang diterima')
                ->get();

            if ($history->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'History Pengiriman tidak ditemukan.',
                    'data' => []
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'History Pengiriman ditemukan.',
                'data' => $history
            ]);

        }catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 500);
        }
    }

    public function getPengiriman(){
        try{
            $pegawai = auth('pegawai')->user();
            if (!$pegawai) {
                return response()->json([
                    "status" => false,
                    "message" => "Pegawai belum login",
                    "data" => null,
                ], 400);
            }
            $idPegawai = $pegawai->idPegawai;

            $pengiriman = TransaksiPembelian::with([
                'detailTransaksiPembelian.barang',
                'pembeli.alamat',
            ])
                ->where('idPegawai3', $idPegawai)
                ->where('status', 'Lunas Siap Diantarkan')
                // ->where('')
                ->get();

            if ($pengiriman->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Jadwal Pengiriman tidak ditemukan.',
                    'data' => []
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Jadwal Pengiriman ditemukan.',
                'data' => $pengiriman
            ]);
        }catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 500);
        }
    }

    public function pengirimanDone(Request $request, $noNota){
        try{
            $pegawai = auth('pegawai')->user();
            if (!$pegawai) {
                return response()->json([
                    "status" => false,
                    "message" => "Pegawai belum login",
                    "data" => null,
                ], 400);
            }

            if (!$noNota) {
                return response()->json([
                    "status" => false,
                    "message" => "noNota tidak boleh kosong",
                    "data" => null
                ], 400);
            }

            // $transaksi = TransaksiPembelian::where('noNota', $noNota)->first();
            $transaksi = TransaksiPembelian::with('detailTransaksiPembelian.barang.detailTransaksiPenitipan.transaksiPenitipan.penitip', 'pembeli')
                ->where('noNota', $noNota)
                ->first();


            if (!$transaksi) {
                return response()->json([
                    "status" => false,
                    "message" => "Transaksi tidak ditemukan untuk noNota: $noNota",
                    "data" => null
                ], 404);
            }

            $transaksi->status = 'Barang Diterima';
            $transaksi->save();

           // === Kirim notifikasi ke semua penitip yang unik ===
            $notifiedPenitipIds = [];

            foreach ($transaksi->detailTransaksiPembelian as $detail) {
                
                $penitip = $detail->barang->detailTransaksiPenitipan?->transaksiPenitipan?->penitip;

                if ($penitip && $penitip->fcm_token && !in_array($penitip->id, $notifiedPenitipIds)) {
                    app(FCMService::class)->sendNotification(
                        $penitip->fcm_token,
                        'Barang Diterima',
                        'Barang titipan Anda telah diterima oleh pembeli.'
                    );
                    $notifiedPenitipIds[] = $penitip->id;
                }
            }

            // === Kirim notifikasi ke PEMBELI ===
            $pembeli = $transaksi->pembeli;
            if ($pembeli && $pembeli->fcm_token) {
                app(FCMService::class)->sendNotification(
                    $pembeli->fcm_token,
                    'Barang Diterima',
                    'Barang Anda telah diterima. Terima kasih telah berbelanja di toko kami.'
                );
            }

            return response()->json([
                "status" => true,
                "message" => "Status transaksi dengan noNota $noNota berhasil diperbarui menjadi 'Barang Diterima'",
                "data" => $transaksi
            ]);
        }catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 500);
        }
    }

}
