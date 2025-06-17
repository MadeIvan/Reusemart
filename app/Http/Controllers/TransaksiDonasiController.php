<?php


namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReqDonasi;
use App\Models\RequestDonasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiDonasiController extends Controller
{
    public function requestAccepted(){
        $requestDonasi = ReqDonasi::with([
            // 'transaksiDonasi.barang.detailTransaksiPenitipan.transaksiPenitipan.pegawai2',
            'transaksiDonasi.barang.detailTransaksiPenitipan.transaksiPenitipan.penitip',
            'organisasi',
        ])

        ->where('status', 'Diterima')
        ->get();

        $donasi = $requestDonasi->sortBy(function ($item) {
            return $item->transaksiDonasi->tanggalDonasi;
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $donasi
        ]);
    }


    public function laporanDonasiPdf(Request $request)
    {
        $year = $request->query('year');

        $donasi = ReqDonasi::with([
            // 'transaksiDonasi.barang.detailTransaksiPenitipan.transaksiPenitipan.pegawai2',
            'transaksiDonasi.barang.detailTransaksiPenitipan.transaksiPenitipan.penitip',
            'organisasi',
        ])->where('status', 'Diterima');

        // Filter berdasarkan tahun jika ada
        if ($year) {
            $donasi->whereHas('transaksiDonasi', function ($query) use ($year) {
                $query->whereYear('tanggalDonasi', $year);
            });
        }

        $donasi = $donasi->get()->sortByDesc(function ($item) {
            return $item->transaksiDonasi->tanggalDonasi;
        })->values();

        return Pdf::loadView('nota.pdf.laporanTransaksiDonasi', compact('donasi', 'year'))
            ->setPaper('a4', 'landscape')
            ->stream("Laporan Transaksi Donasi" . ($year ? " - $year" : "") . ".pdf");
        }

}
