<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiPenitipan;
use App\Models\DetailTransaksiPenitipan;
use Carbon\Carbon;
class TransaksiPenitipanController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'idPegawai1' => 'required|exists:pegawai,idPegawai',
        'idPegawai2' => 'nullable|exists:pegawai,idPegawai',
        'idPenitip' => 'required|exists:penitip,idPenitip',
        // 'tanggalPenitipan' => now(),
        'totalHarga' => 'nullable|numeric|min:0',
        'idBarang' => 'required|exists:barang,idBarang'
    ]);

    // Optional: Check if this barang is already in a penitipan
    if (DetailTransaksiPenitipan::where('idBarang', $request->idBarang)->exists()) {
        return response()->json([
            'status' => false,
            'message' => 'Barang already associated with a Transaksi Penitipan'
        ], 400);
    }

    DB::beginTransaction();
    try {
        // Generate new TP ID
        $last = TransaksiPenitipan::orderBy('idTransaksiPenitipan', 'desc')->first();
        $lastNumber = $last ? (int) str_replace('TP', '', $last->idTransaksiPenitipan) : 0;
        $newIdTP = 'TP' . ($lastNumber + 1);
        while (TransaksiPenitipan::where('idTransaksiPenitipan', $newIdTP)->exists()) {
            $lastNumber++;
            $newIdTP = 'TP' . $lastNumber;
        }

        $tanggalMulai = new \DateTime($request->tanggalPenitipan);
        $tanggalSelesai = (clone $tanggalMulai)->modify('+30 days');

        // Create transaksiPenitipan
        $transaksi = TransaksiPenitipan::create([
            'idTransaksiPenitipan' => $newIdTP,
            'idPegawai1' => $request->idPegawai1,
            'idPegawai2' => $request->idPegawai2,
            'idPenitip' => $request->idPenitip,
            'tanggalPenitipan' => now(),
            'tanggalPenitipanSelesai' => now()->addDays(30),
            'totalHarga' => $request->totalHarga,
        ]);

        // Link to 1 barang
        DetailTransaksiPenitipan::create([
            'idTransaksiPenitipan' => $newIdTP,
            'idBarang' => $request->idBarang
        ]);

        DB::commit();
        return response()->json([
            'status' => true,
            'message' => 'Transaksi Penitipan created successfully',
            'data' => $transaksi
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Transaction failed: ' . $e->getMessage()
        ], 500);
    }
}



public function getAllByPenitip($idPenitip)
{
    // Eager load imagesbarang for each barang
    $transaksi = \App\Models\TransaksiPenitipan::with([
        'detailTransaksiPenitipan.barang.imagesbarang'
    ])
    ->where('idPenitip', $idPenitip)
    ->orderBy('tanggalPenitipan', 'desc')
    ->get();

    // Map the response to include all images for each barang (from ImagesBarang table)
    $result = $transaksi->map(function($tp) {
        $tpArr = $tp->toArray();
        $tpArr['detailTransaksiPenitipan'] = collect($tp->detailTransaksiPenitipan)->map(function($detail) {
            $barang = $detail->barang;
            // If images are in the related imagesbarang table
            $images = [
                'image1' => $barang->imagesbarang->image1 ?? null,
                'image2' => $barang->imagesbarang->image2 ?? null,
                'image3' => $barang->imagesbarang->image3 ?? null,
                'image4' => $barang->imagesbarang->image4 ?? null,
                'image5' => $barang->imagesbarang->image5 ?? null,
            ];
            $detailArr = $detail->toArray();
            $detailArr['imagesBarang'] = $images;
            return $detailArr;
        });
        unset($tpArr['detailTransaksiPenitipan']);
        return $tpArr;
    });

    return response()->json([
        'status' => true,
        'message' => 'Get successful',
        'data' => $result
    ], 200);
}


    public function perpanjangPenitipan($idTransaksiPenitipan)
    {
        $transaksi = TransaksiPenitipan::find($idTransaksiPenitipan);
        if (!$transaksi) {
            return response()->json([
                'status' => false,
                'message' => 'Transaksi penitipan tidak ditemukan.'
            ], 404);
        }

        // Tambah 30 hari dari tanggal hari ini
        $newDate = Carbon::now()->addDays(30)->toDateString();
        $transaksi->tanggalPenitipanSelesai = $newDate;
        $transaksi->save();

        return response()->json([
            'status' => true,
            'message' => 'Tanggal penitipan selesai berhasil diperpanjang.',
            'tanggalPenitipanSelesai' => $newDate
        ]);
    }


    
public function laporanPenitipanHabis(Request $request)
{
    $now = now()->toDateString();

    // Ambil semua transaksi penitipan yang sudah habis masa penitipannya
    $transaksis = \App\Models\TransaksiPenitipan::with([
        'detailTransaksiPenitipan.barang',
        'penitip'
    ])
    ->where('tanggalPenitipanSelesai', '<', $now)
    ->get();

    $rows = [];
    foreach ($transaksis as $tp) {
        foreach ($tp->detailTransaksiPenitipan as $detail) {
            $barang = $detail->barang;
            if (!$barang) continue;
            $rows[] = [
                'kode_produk' => $barang->idBarang,
                'nama_produk' => $barang->namaBarang,
                'id_penitip' => $tp->penitip->idPenitip ?? '',
                'nama_penitip' => $tp->penitip->namaPenitip ?? '',
                'tanggal_masuk' => \Carbon\Carbon::parse($tp->tanggalPenitipan)->format('d/m/Y'),
                'tanggal_akhir' => \Carbon\Carbon::parse($tp->tanggalPenitipanSelesai)->format('d/m/Y'),
                'batas_ambil' => \Carbon\Carbon::parse($tp->tanggalPenitipanSelesai)->addDays(7)->format('d/m/Y'),
            ];
        }
    }

    // Untuk API
    if ($request->wantsJson()) {
        return response()->json([
            'tanggalCetak' => now()->format('d F Y'),
            'data' => $rows,
        ]);
    }

    // Untuk PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('nota.pdf.laporanPenitipanHabis', [
        'rows' => $rows,
        'tanggalCetak' => now()->format('d F Y'),
    ]);
    return $pdf->stream('laporan-penitipan-habis.pdf');
}
public function apiPenitipanHabis(Request $request)
{
    $now = now()->toDateString();

    // Ambil semua transaksi penitipan yang sudah habis masa penitipannya
    $transaksis = \App\Models\TransaksiPenitipan::with([
        'detailTransaksiPenitipan.barang',
        'penitip'
    ])
    ->where('tanggalPenitipanSelesai', '<', $now)
    ->get();

    $rows = [];
    foreach ($transaksis as $tp) {
        foreach ($tp->detailTransaksiPenitipan as $detail) {
            $barang = $detail->barang;
            if (!$barang) continue;
            $rows[] = [
                'kode_produk' => $barang->idBarang,
                'nama_produk' => $barang->namaBarang,
                'id_penitip' => $tp->penitip->idPenitip ?? '',
                'nama_penitip' => $tp->penitip->namaPenitip ?? '',
                'tanggal_masuk' => \Carbon\Carbon::parse($tp->tanggalPenitipan)->format('d/m/Y'),
                'tanggal_akhir' => \Carbon\Carbon::parse($tp->tanggalPenitipanSelesai)->format('d/m/Y'),
                'batas_ambil' => \Carbon\Carbon::parse($tp->tanggalPenitipanSelesai)->addDays(7)->format('d/m/Y'),
            ];
        }
    }

    return response()->json([
        'tanggalCetak' => now()->format('d F Y'),
        'data' => $rows,
    ]);
}
}
