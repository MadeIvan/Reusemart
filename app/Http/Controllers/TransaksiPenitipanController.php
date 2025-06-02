<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiPenitipan;
use App\Models\DetailTransaksiPenitipan;
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
}
