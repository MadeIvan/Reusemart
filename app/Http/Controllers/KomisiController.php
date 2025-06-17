<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Models\TransaksiPembelian;
use App\Models\TransaksiPenitipan;
use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\Dompet;
use App\Models\Komisi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KomisiController extends Controller
{

public function komisiReuseMart($noNota)
{
    DB::beginTransaction();
    $log = [];
    try {
        // Step 1: Find all detail transaksi pembelian with this noNota
        $details = \App\Models\DetailTransaksiPembelian::with('barang')->where('noNota', $noNota)->get();

        if ($details->isEmpty()) {
            $log[] = "Tidak ada detail transaksi pembelian ditemukan untuk noNota: $noNota";
            return response()->json(['status' => false, 'message' => 'Detail transaksi pembelian tidak ditemukan', 'log' => $log], 404);
        }

        // Step 2: Find the correct hunter (pegawai) assigned to this transaction
        $transaksiPembelian = \App\Models\TransaksiPembelian::where('noNota', $noNota)->first();
        $pegawaiHunter = null;
        if ($transaksiPembelian && $transaksiPembelian->idPegawai3) {
            $pegawaiHunter = Pegawai::where('idPegawai', $transaksiPembelian->idPegawai3)->first();
            $log[] = $pegawaiHunter
                ? "Hunter ditemukan: idPegawai={$pegawaiHunter->idPegawai}"
                : "Hunter dengan idPegawai={$transaksiPembelian->idPegawai3} tidak ditemukan";
        } else {
            $log[] = "Tidak ada hunter yang ditugaskan pada transaksi ini";
        }

        // Step 3: Find admin pegawai
        $pegawaiAdmin = Pegawai::where('idJabatan', 1)->first();
        $log[] = $pegawaiAdmin
            ? "Admin ditemukan: idPegawai={$pegawaiAdmin->idPegawai}"
            : "Admin tidak ditemukan";

        foreach ($details as $detail) {
            $barang = $detail->barang;
            if (!$barang) {
                $log[] = "Barang tidak ditemukan untuk detail ID: {$detail->id}";
                continue;
            }

            // Find detail transaksi penitipan for this barang
            $detailPenitipan = \App\Models\DetailTransaksiPenitipan::where('idBarang', $barang->idBarang)->first();
            if (!$detailPenitipan) {
                $log[] = "DetailTransaksiPenitipan tidak ditemukan untuk barang ID: {$barang->idBarang}";
                continue;
            }

            // Find transaksi penitipan for this detail penitipan
            $transaksiPenitipan = \App\Models\TransaksiPenitipan::where('idTransaksiPenitipan', $detailPenitipan->idTransaksiPenitipan)->first();
            if (!$transaksiPenitipan) {
                $log[] = "TransaksiPenitipan tidak ditemukan untuk detailPenitipan ID: {$detailPenitipan->idTransaksiPenitipan}";
                continue;
            }

            // Find penitip who owns this barang
            $penitip = Penitip::where('idPenitip', $transaksiPenitipan->idPenitip)->first();
            if (!$penitip) {
                $log[] = "Penitip tidak ditemukan untuk transaksiPenitipan ID: {$transaksiPenitipan->idTransaksiPenitipan}";
                continue;
            }

            // Find dompet penitip
            $dompetPenitip = $penitip->idDompet ? Dompet::find($penitip->idDompet) : null;
            if (!$dompetPenitip) {
                $log[] = "Dompet penitip tidak ditemukan untuk penitip ID: {$penitip->idPenitip}";
                continue;
            }

            $hargaBarang = $detail->hargaBarang ?? $barang->hargaBarang ?? 0;
            $tanggalPenitipan = Carbon::parse($transaksiPenitipan->tanggalPenitipan);
            $tanggalPembelian = $transaksiPembelian ? Carbon::parse($transaksiPembelian->tanggalWaktuPembelian) : now();
            $hariPenitipan = $tanggalPenitipan->diffInDays($tanggalPembelian);

            // Default komisi
            $adminKomisi = 0;
            $hunterKomisi = 0;
            $penitipKomisi = 0;

            // Step 5: Tentukan komisi
            if ($pegawaiHunter) {
                if ($hariPenitipan > 30) {
                    $adminKomisi = $hargaBarang * 0.30;
                    $hunterKomisi = ($hargaBarang * 0.70) * 0.05;
                    $penitipKomisi = $hargaBarang * 0.70;
                    $log[] = "Hunter ada, penitipan > 30 hari: adminKomisi=$adminKomisi, hunterKomisi=$hunterKomisi, penitipKomisi=$penitipKomisi";
                } elseif ($hariPenitipan < 7) {
                    $adminKomisi = $hargaBarang * 0.20;
                    $hunterKomisi = ($hargaBarang * 0.20) * 0.05;
                    $penitipKomisi = $hargaBarang * 0.80 + (($hargaBarang * 0.20) * 0.1);
                    $log[] = "Hunter ada, penitipan < 7 hari: adminKomisi=$adminKomisi, hunterKomisi=$hunterKomisi, penitipKomisi=$penitipKomisi";
                } else {
                    $adminKomisi = $hargaBarang * 0.20;
                    $hunterKomisi = ($hargaBarang * 0.20) * 0.05;
                    $penitipKomisi = $hargaBarang * 0.80;
                    $log[] = "Hunter ada, penitipan 7-30 hari: adminKomisi=$adminKomisi, hunterKomisi=$hunterKomisi, penitipKomisi=$penitipKomisi";
                }
            } else {
                if ($hariPenitipan > 30) {
                    $adminKomisi = $hargaBarang * 0.30;
                    $penitipKomisi = $hargaBarang * 0.70;
                    $log[] = "Tanpa hunter, penitipan > 30 hari: adminKomisi=$adminKomisi, penitipKomisi=$penitipKomisi";
                } elseif ($hariPenitipan < 7) {
                    $adminKomisi = $hargaBarang * 0.20;
                    $penitipKomisi = $hargaBarang * 0.80 + (($hargaBarang * 0.20) * 0.1);
                    $log[] = "Tanpa hunter, penitipan < 7 hari: adminKomisi=$adminKomisi, penitipKomisi=$penitipKomisi";
                } else {
                    $adminKomisi = $hargaBarang * 0.20;
                    $penitipKomisi = $hargaBarang * 0.80;
                    $log[] = "Tanpa hunter, penitipan 7-30 hari: adminKomisi=$adminKomisi, penitipKomisi=$penitipKomisi";
                }
            }

            // Step 6: Update dompet admin
            if ($pegawaiAdmin && $pegawaiAdmin->idDompet) {
                $dompetAdmin = Dompet::find($pegawaiAdmin->idDompet);
                if ($dompetAdmin) {
                    $oldSaldo = $dompetAdmin->saldo;
                    $dompetAdmin->saldo += $adminKomisi;
                    $dompetAdmin->save();
                    $log[] = "Dompet admin (id: {$dompetAdmin->idDompet}) saldo: $oldSaldo + $adminKomisi = {$dompetAdmin->saldo}";
                } else {
                    $log[] = "Dompet admin tidak ditemukan";
                }
            } else {
                $log[] = "Pegawai admin tidak ditemukan";
            }

            // Step 7: Update dompet hunter (jika ada)
            if ($pegawaiHunter && $pegawaiHunter->idDompet && $hunterKomisi > 0) {
                $dompetHunter = Dompet::find($pegawaiHunter->idDompet);
                if ($dompetHunter) {
                    $oldSaldo = $dompetHunter->saldo;
                    $dompetHunter->saldo += $hunterKomisi;
                    $dompetHunter->save();
                    $log[] = "Dompet hunter (id: {$dompetHunter->idDompet}) saldo: $oldSaldo + $hunterKomisi = {$dompetHunter->saldo}";
                } else {
                    $log[] = "Dompet hunter tidak ditemukan";
                }
            }

            // Step 8: Update dompet penitip
            if ($dompetPenitip) {
                $oldSaldo = $dompetPenitip->saldo;
                $dompetPenitip->saldo += $penitipKomisi;
                $dompetPenitip->save();
                $log[] = "Dompet penitip (id: {$dompetPenitip->idDompet}, idPenitip: {$penitip->idPenitip}) saldo: $oldSaldo + $penitipKomisi = {$dompetPenitip->saldo}";
            } else {
                $log[] = "Dompet penitip tidak ditemukan untuk penitip ID: {$penitip->idPenitip}";
            }

            // Step 9: Log barang and penitip
            $log[] = [
                'idBarang' => $barang->idBarang,
                'idPenitip' => $penitip->idPenitip,
                'hariPenitipan' => $hariPenitipan,
                'adminKomisi' => $adminKomisi,
                'hunterKomisi' => $hunterKomisi,
                'penitipKomisi' => $penitipKomisi,
            ];

            // Step 10: Create Komisi record for this barang (without idBarang)
            $maxId = Komisi::max(\DB::raw('CAST(idKomisi AS UNSIGNED)'));
            $newId = ($maxId !== null && $maxId !== '') ? strval($maxId + 1) : "1";

            $komisi = new Komisi();
            $komisi->idKomisi = $newId;
            $komisi->noNota = $noNota;
            $komisi->idBarang = $barang->idBarang; // Do NOT set idBarang
            $komisi->komisiMart = $adminKomisi;
            $komisi->komisiHunter = $hunterKomisi;
            $komisi->komisiPenitip = $penitipKomisi;
            $komisi->save();

            $log[] = "Komisi created: idKomisi=$newId, noNota=$noNota, komisiMart=$adminKomisi, komisiHunter=$hunterKomisi, komisiPenitip=$penitipKomisi";
        }

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Komisi berhasil dihitung, dibagikan, dan dicatat.',
            'log' => $log
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        $log[] = "ERROR: " . $e->getMessage();
        return response()->json([
            'status' => false,
            'message' => 'Gagal menghitung komisi: ' . $e->getMessage(),
            'log' => $log
        ], 500);
    }
}

    public function store(Request $request)
    {
        $request->validate([
            'noNota' => 'required|string|max:255',
            'idBarang' => 'required|string|max:255',
            'komisiMart' => 'required|numeric',
            'komisiHunter' => 'required|numeric',
            'komisiPenitip' => 'required|numeric',
        ]);

        try {
$maxId = Komisi::max(\DB::raw('CAST(idKomisi AS UNSIGNED)'));
$newId = ($maxId !== null && $maxId !== '') ? strval($maxId + 1) : "1";

            $komisi = new Komisi();
            $komisi->idKomisi = $newId;
            $komisi->noNota = $request->noNota;
            $komisi->idBarang = $request->idBarang;
            $komisi->komisiMart = $request->komisiMart;
            $komisi->komisiHunter = $request->komisiHunter;
            $komisi->komisiPenitip = $request->komisiPenitip;
            $komisi->save();

            return response()->json([
                "status" => true,
                "message" => "Komisi created successfully",
                "data" => $komisi
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Failed to create komisi: " . $e->getMessage(),
                "data" => null
            ], 500);
        }
        
    }

    public function index()
    {
        try {
            $komisi = Komisi::all();
            return response()->json([
                'status' => true,
                'data' => $komisi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve komisi: ' . $e->getMessage()
            ], 500);
        }
    }


}