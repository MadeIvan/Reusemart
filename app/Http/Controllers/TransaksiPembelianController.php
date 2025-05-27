<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\TransaksiPembelian;
use App\Models\DetailTransaksiPembelian;
use App\Models\Barang;
use Exception;

class TransaksiPembelianController extends Controller
{
    public function store(Request $request){
        try {
            $validated = $request->validate([
                'idAlamat' => 'required|string',
                'tanggalWaktuPembelian' => 'required|date_format:Y-m-d H:i:s',
                'totalHarga' => 'required|numeric',
                'id_barang' => 'required|array',
                'id_barang.*' => 'string',
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

            DB::beginTransaction();

            // Generate noNota
            // $last = TransaksiPembelian::orderBy('noNota', 'desc')->first();
            // $lastNumber = 0;
            // $newId = date('Y.m.') . ($lastNumber + 1);
            $yearMonth = date('Y.m');

            $last = TransaksiPembelian::where('noNota', 'like', "$yearMonth.%")
                ->orderBy('noNota', 'desc')
                ->first();

            $lastNumber = 0;
            if ($last) {
                $parts = explode('.', $last->noNota);
                $lastNumber = intval(end($parts));
            }
            $newId = $yearMonth . '.' . ($lastNumber + 1);

            // Simpan transaksi utama
            $transaksiPembelian = TransaksiPembelian::create([
                'noNota' => $newId,
                'idPembeli' => $pembeli->idPembeli,
                'idAlamat' => $validated['idAlamat'],
                'tanggalWaktuPembelian' => $validated['tanggalWaktuPembelian'],
                'status' => "Menunggu Pembayaran",
                'totalHarga' => $validated['totalHarga']
            ]);


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

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Transaksi berhasil dibuat",
                "data" => $transaksiPembelian
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => "Terjadi kesalahan: " . $e->getMessage(),
                "data" => null
            ], 500);
        }
    }
}
