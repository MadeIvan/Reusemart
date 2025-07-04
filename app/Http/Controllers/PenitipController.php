<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Penitip;
use App\Models\dompets;
use App\Models\TransaksiPenitipan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; 
use App\Http\Controllers\DompetController;
// use App\Http\Models\TransaksiPenitipan;
use Illuminate\Support\Facades\DB;

class PenitipController extends Controller
{
    // Login function for Penitip
    public function login(Request $request)
    {

        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $penitip = Penitip::whereRaw('BINARY username = ?', [$request->username])->first();

        if (!$penitip) {
            return response()->json(['message' => 'User Tidak ditemukan'], 404);
        }
        if (!Hash::check($request->password, $penitip->password)) {
            return response()->json(['message' => 'Username atau Password Salah'], 401);
        }

        $token = $penitip->createToken('Personal Access Token')->plainTextToken;
        
        return response()->json([
            'message' => 'Login successful',
            'penitip' => [
                'idPenitip' => $penitip->idPenitip,
                'username' => $penitip->username,
                'namaPenitip' => $penitip->namaPenitip,
                'idTopeSeller' => $penitip->idTopeSeller,
                'idDompet' => $penitip->idDompet,
                'token' => $token
            ],
        ]);
    }
    public function getData(Request $request)    
    {
        // $penitip = $request->user();
        $penitip = auth('penitip')->user()->load('dompet');
        
        return response()->json([
            "status" => true,
            "message" => "User retrieved successfully",
            "data" => $penitip
        ]);
    }
    // Get all penitip data
    public function getAllPenitip()
    {
        $penitips = Penitip::all();

        return response()->json([
            'status' => 'success',
            'data' => $penitips
        ]);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:penitip,username|max:255',
            'password' => 'required|string|min:6',
            'namaPenitip' => 'required|string|max:255',
            'nik' => 'required|string|size:16',
            "email"=>'required|string'
        ]);

        if ($validator->fails()) {
    return response()->json([
        'message' => 'Validation failed',
        'errors' => $validator->errors()
    ], 400);
}

        try {

            $lastPenitip = DB::select("SELECT MAX(CAST(SUBSTRING(idPenitip, 2) AS UNSIGNED)) AS last_id FROM penitip");
            $lastPenitip = $lastPenitip[0]->last_id;
            $newId = $lastPenitip ? 'T' . ($lastPenitip + 1) : 'T1';
            // \Log::info("Created new penitip ID: {$newId}");
            $dompet = (new DompetController)->createDompetPenitip(null);
            $idDompet = (string) $dompet->idDompet;

            // \Log::info("Created new dompet with ID: {$idDompet}");
            $penitip = Penitip::create([
                'idPenitip' => $newId,
                'username' => $request->username,
                'password' => Hash::make($request->password), 
                'namaPenitip' => $request->namaPenitip,
                'nik' => $request->nik,
                'idTopeSeller' => null,
                'idDompet' => $idDompet,
            ]);
            \Log::info("{$penitip->idPenitip},{$dompet->saldo}, {$idDompet}");
            $dompet=(new DompetController)->updateDompet($penitip->idPenitip, $dompet->saldo, $idDompet);
            return response()->json([
                'message' => 'Penitip registered successfully!',
                'penitip' => $penitip,
                'status' => true
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    ///////////////////[REGISTER PENITIP]
    /////////////////[GET PENITIP ID]////////////////////
    public function getPenitipById($id)
    {
        $penitip = Penitip::find($id);

        if (!$penitip) {
            return response()->json([
                'message' => 'Penitip not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $penitip
        ]);
    }


    ////////////////[UPDATE PENITIP ID]////////////////////
    public function updatePenitip(Request $request, $id){
    $penitip = Penitip::whereRaw('BINARY idPenitip = ?', [$id])->first();

    // Find the penitip record by its 

    // Check if the penitip record exists
    if (!$penitip) {
        return response()->json([
            'message' => 'Penitip not found'
        ], 404);
    }

    // Validate the input data
    $validated = $request->validate([
        'username' => 'nullable|string|unique:penitip,username,' . $penitip->idPenitip . ',idPenitip|max:255',
        'namaPenitip' => 'nullable|string|max:255',
        'nik' => 'nullable|string|size:16',
    ]);

    // Only update the fields that can be updated
    $penitip->update($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'Penitip updated successfully!',
        'data' => $penitip
    ]);
}

    ////////////////////[DELETE PENITIP ID]////////////////////

public function deletePenitip($id){
    $penitip = Penitip::find($id);


    if (!$penitip) {
        return response()->json([
            'message' => 'Penitip not found'
        ], 404);
    }

    $penitip->deleted_at = now();
    $penitip->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Penitip deleted successfully'
    ]);
}

    public function myData(Request $request)    
    {
        // $penitip = $request->user();
$penitip = auth('penitip')->user()->load('dompet');

        return response()->json([
            "status" => true,
            "message" => "User retrieved successfully",
            "data" => $penitip
        ]);
    }

public function loadBarang(Request $request)
{
    $penitip = auth('penitip')->user();

    // Eager load imagesbarang for each barang, and only barang with status 'Terjual'
    $penitipan = \App\Models\TransaksiPenitipan::with([
        'detailTransaksiPenitipan.barang' => function ($query) {
            $query->where('statusBarang', 'Terjual');
        },
        'detailTransaksiPenitipan.barang.imagesbarang'
    ])
    ->where('idPenitip', $penitip->idPenitip)
    ->get();

    // Map the response to include all images for each barang
    $result = $penitipan->map(function($tp) {
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
        return $tpArr;
    });

    return response()->json([
        "status" => true,
        "message" => "Barang penitipan berhasil dimuat",
        "data" => $result
    ]);
}

    public function getPenitip()
{
    $penitips = Penitip::all(['idPenitip', 'username']);

    return response()->json([
        'status' => true,
        'data' => $penitips
    ]);
}
public function getTopPenitipByMonth(Request $request)
{
    $validated = $request->validate([
        'month' => 'required|string|min:1|max:12',
        'year' => 'nullable|integer|min:2000|max:2100',
    ]);

    $month = $validated['month'];
    $year = $validated['year'] ?? now()->year;

    $topPenitip = DB::table('barang')
        ->join('detailTransaksiPenitipan', 'barang.idBarang', '=', 'detailTransaksiPenitipan.idBarang')
        ->join('transaksiPenitipan', 'detailTransaksiPenitipan.idTransaksiPenitipan', '=', 'transaksiPenitipan.idTransaksiPenitipan')
        ->join('penitip', 'transaksiPenitipan.idPenitip', '=', 'penitip.idPenitip')
        ->join('detailTransaksiPembelian', 'barang.idBarang', '=', 'detailTransaksiPembelian.idBarang')
        ->join('transaksiPembelian', 'detailTransaksiPembelian.noNota', '=', 'transaksiPembelian.noNota')
        ->where('barang.statusBarang', 'Terjual')
        ->whereMonth('transaksiPembelian.tanggalWaktuPelunasan', $month)
        ->whereYear('transaksiPembelian.tanggalWaktuPelunasan', $year)
        ->select(
            'penitip.idPenitip',
            'penitip.namaPenitip',
            DB::raw('COUNT(barang.idBarang) as totalBarangTerjual')
        )
        ->groupBy('penitip.idPenitip', 'penitip.namaPenitip')
        ->orderByDesc('totalBarangTerjual')
        ->limit(1)
        ->first();

    if (!$topPenitip) {
        return response()->json([
            'month' => $month,
            'year' => $year,
            'message' => 'Tidak ada penitip yang menjual barang di bulan ini.'
        ], 404);
    }


    $totalPendapatan = DB::table('barang')
        ->join('detailTransaksiPenitipan', 'barang.idBarang', '=', 'detailTransaksiPenitipan.idBarang')
        ->join('transaksiPenitipan', 'detailTransaksiPenitipan.idTransaksiPenitipan', '=', 'transaksiPenitipan.idTransaksiPenitipan')
        ->join('penitip', 'transaksiPenitipan.idPenitip', '=', 'penitip.idPenitip')
        ->join('detailTransaksiPembelian', 'barang.idBarang', '=', 'detailTransaksiPembelian.idBarang')
        ->join('transaksiPembelian', 'detailTransaksiPembelian.noNota', '=', 'transaksiPembelian.noNota')
        ->where('barang.statusBarang', 'Terjual')
        ->whereMonth('transaksiPembelian.tanggalWaktuPelunasan', $month)
        ->whereYear('transaksiPembelian.tanggalWaktuPelunasan', $year)
        ->where('penitip.idPenitip', $topPenitip->idPenitip)
        ->select('transaksiPembelian.totalHarga', 'transaksiPembelian.noNota')
        ->distinct()
        ->get()
        ->sum('totalHarga');

    return response()->json([
        'month' => $month,
        'year' => $year,
        'idPenitip' => $topPenitip->idPenitip,
        'totalPendapatan' => $totalPendapatan,
        'topPenitip' => $topPenitip
    ]);
}
}




