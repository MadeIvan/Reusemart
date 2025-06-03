<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Models\TransaksiPembelian;
use App\Models\TransaksiPenitipan;
use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\Dompet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KomisiController extends Controller
{
    public function komisiReuseMart($noNota)
    {
        DB::beginTransaction();
        try {
            $transaksiPembelian = TransaksiPembelian::with([
                'detailTransaksiPembelian.barang.detailTransaksiPenitipan.transaksiPenitipan',
                'pegawai3', // kurir
            ])->where('noNota', $noNota)->first();

            if (!$transaksiPembelian) {
                return response()->json(['status' => false, 'message' => 'Transaksi Pembelian tidak ditemukan'], 404);
            }

            $totalHarga = $transaksiPembelian->totalHarga;
            $pegawaiKurir = $transaksiPembelian->pegawai3; // Pegawai kurir (idJabatan=4)
            $pegawaiAdmin = Pegawai::where('idJabatan', 1)->first();
            $kurir = Pegawai::where('idJabatan', 6)->first();

            $log = [];

            foreach ($transaksiPembelian->detailTransaksiPembelian as $detail) {
                $barang = $detail->barang;
                if (!$barang) continue;

                $tp = $barang->detailTransaksiPenitipan->first()->transaksiPenitipan ?? null;
                if (!$tp) continue;

                $tanggalPenitipan = Carbon::parse($tp->tanggalPenitipan);
                $tanggalPembelian = Carbon::parse($transaksiPembelian->tanggalWaktuPembelian);
                $hariPenitipan = $tanggalPenitipan->diffInDays($tanggalPembelian);

                $penitip = Penitip::where('idPenitip', $tp->idPenitip)->first();
                $dompetPenitip = Dompet::find($penitip->idDompet ?? null);

                // Default komisi
                $adminKomisi = 0;
                $kurirKomisi = 0;
                $penitipKomisi = 0;

                if ($pegawaiKurir) {
                    if ($hariPenitipan > 30) {
                        $adminKomisi = $totalHarga * 0.30;
                        $kurirKomisi = ($totalHarga * 0.70) * 0.05;
                        $penitipKomisi = $totalHarga * 0.70;
                    } elseif ($hariPenitipan < 7) {
                        $adminKomisi = $totalHarga * 0.20;
                        $kurirKomisi = ($totalHarga * 0.20) * 0.05;
                        $penitipKomisi = $totalHarga * 0.80 + (($totalHarga * 0.20) * 0.1);
                    } else {
                        $adminKomisi = $totalHarga * 0.20;
                        $kurirKomisi = ($totalHarga * 0.20) * 0.05;
                        $penitipKomisi = $totalHarga * 0.80;
                    }
                } else {
                    if ($hariPenitipan > 30) {
                        $adminKomisi = $totalHarga * 0.30;
                        $penitipKomisi = $totalHarga * 0.70;
                    } elseif ($hariPenitipan < 7) {
                        $adminKomisi = $totalHarga * 0.20;
                        $penitipKomisi = $totalHarga * 0.80 + (($totalHarga * 0.20) * 0.1);
                    } else {
                        $adminKomisi = $totalHarga * 0.20;
                        $penitipKomisi = $totalHarga * 0.80;
                    }
                }

                // Update admin dompet
                if ($pegawaiAdmin && $pegawaiAdmin->idDompet) {
                    $dompetAdmin = Dompet::find($pegawaiAdmin->idDompet);
                    if ($dompetAdmin) {
                        $dompetAdmin->saldo += $adminKomisi;
                        $dompetAdmin->save();
                    }
                }

                // Update kurir dompet
                if ($pegawaiKurir && $pegawaiKurir->idDompet && $kurirKomisi > 0) {
                    $dompetKurir = Dompet::find($pegawaiKurir->idDompet);
                    if ($dompetKurir) {
                        $dompetKurir->saldo += $kurirKomisi;
                        $dompetKurir->save();
                    }
                }

                // Update penitip dompet
                if ($dompetPenitip) {
                    $dompetPenitip->saldo += $penitipKomisi;
                    $dompetPenitip->save();
                }

                $log[] = [
                    'barang' => $barang->idBarang,
                    'hariPenitipan' => $hariPenitipan,
                    'adminKomisi' => $adminKomisi,
                    'kurirKomisi' => $kurirKomisi,
                    'penitipKomisi' => $penitipKomisi,
                ];
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Komisi berhasil dihitung dan dibagikan.',
                'log' => $log
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghitung komisi: ' . $e->getMessage()
            ], 500);
        }
    }
}