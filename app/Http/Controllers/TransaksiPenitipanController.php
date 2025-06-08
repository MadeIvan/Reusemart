<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiPenitipan;
use App\Models\DetailTransaksiPenitipan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Pegawai;
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

public function add30($id){
    $tanggalMulai = new \DateTime($id);

     $tanggalSelesai = (clone $tanggalMulai)->modify('+30 days');
    return $tanggalSelesai;

}

public function getallbyid($id){
    try {
        // Try to find the barang by its ID
       
        $barang = TransaksiPenitipan::where('tanggalPenitipanSelesai', $id)->get();

        // If the barang is found, return it as JSON
        if ($barang) {
            return response()->json($barang);
        } else {
            // If the barang is not found, return a 404 error with a custom message
            return response()->json(['message' => 'Barang not found'], 404);
        }
    } catch (\Exception $e) {
        // Catch any exception and return a 500 internal server error
        return response()->json([
            'error' => 'An error occurred while fetching the product.',
            'message' => $e->getMessage(), // Include the exception message for debugging
        ], 500);
    }
}
public function notaPenitipanPdf($id)
{
    $idTransaksi=$id;
    $transaksiPenitipan = TransaksiPenitipan::with(['penitip', 'detailTransaksiPenitipan.barang'])->findOrFail($idTransaksi);
    $detailTransaksiPenitipan = $transaksiPenitipan->detailTransaksiPenitipan;
    $pegawai = Pegawai::find($transaksiPenitipan->idPegawai1); // misal idPegawaiQC untuk yang QC
    

    $pdf = Pdf::loadView('PegawaiGudang.notaPenitipan', compact('transaksiPenitipan', 'detailTransaksiPenitipan', 'pegawai'));

    return $pdf->download('nota-penitipan-' . $id . '.pdf');

    // $transaksi = \App\Models\TransaksiPenitipan::with([
    //     'detailTransaksiPenitipan.barang',
    //     'pegawai',
    //     'pegawai2',
    //     'penitip',

        
    // ])->where('idTransaksiPenitipan', $id)->firstOrFail();
    // // return response()->json([
    // //     'status' => true,
    // //     'message' => 'Data transaksi berhasil diambil',
    // //     'data' => $transaksi
    // // ]);

    // return Pdf::loadView('Pegawai.notaPenitipan', compact('transaksi'))
    //     ->download("nota-penitipan-{$id}.pdf");
}
}
