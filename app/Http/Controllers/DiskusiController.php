<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Barang;
use App\Models\Diskusi;
use App\Models\Pembeli;
use App\Models\Pegawai;

class DiskusiController extends Controller
{
    public function store(Request $request, $idBarang) {
        $request->validate([
            'pesandiskusi'=> 'required|string|max:255',
        ]);

        /////////////////cek id barang
        $barang = Barang::find($idBarang);

        if(!$barang){
            return response()->json([
                'message' => "Barang not found",
            ], 403);
        }

        /////////////// id Diskusi 
        $last = Diskusi::orderBy('idDiskusi', 'desc')->first();
        $lastNumber = 0;

        if ($last) {
            $lastNumber = (int) $last->idDiskusi;
        }

        $newId = strval($lastNumber + 1);
        
        
        while (Diskusi::where('idDiskusi', $newId)->exists()) {
            $lastNumber++;
            $newId = strval($lastNumber + 1);
        }
        
        
        ////////////////////////// cek siapa yang login
        $userId = null;
        $isPembeli = Auth::guard('pembeli')->check();
        $isPegawai = Auth::guard('pegawai')->check();
        
        if ($isPembeli) {
            $userId = Auth::guard('pembeli')->user()->idPembeli;
        } elseif ($isPegawai) {
            $userId = Auth::guard('pegawai')->user()->idPenitip;
        } else {
            return response()->json([
                'message' => "Unauthorized. Anda harus login sebagai pembeli atau pegawai.",
            ], 403);
        }
        

        if($isPembeli){
           $data = [
                'idDiskusi'=> $newId,
                'idBarang'=> $barang->idBarang,
                'pesandiskusi'=> $request->pesandiskusi,
                'tanggalDiskusi'=> now()->toDateString(),
                'waktuMengirimDiskusi'=> now()->toTimeString(),
                'idPembeli'=> $userId,
                'idPegawai'=> null,
            ];
        }else if($isPenitip){
            $data = [
                'idDiskusi'=> $newId,
                'idBarang'=> $barang->idBarang,
                'pesandiskusi'=> $request->pesandiskusi,
                'tanggalDiskusi'=> now()->toDateString(),
                'waktuMengirimDiskusi'=> now()->toTimeString(),
                'idPembeli'=> null,
                'idPegawai'=> $userId,
            ];
        }else{
             return response()->json([
                'message' => "Unauthorized. Anda harus login sebagai pembeli atau pegawai.",
            ], 403);
        }

            
        try{
            $diskusi = Diskusi::create($data);

            return response()->json([
                "status" => true,
                "message" => "Create successful",
                "data" => $diskusi
            ], 200);

        }catch(Exception $e){
            return response()->json([
                "status" => false,
                "message" => "Something went wrong",
                "data" => $e->getMessage()
            ], 200);
        }
    }

    // public function index()
    // {
    //     $pembeli = Auth::user();

    //     // Ambil semua alamat milik pembeli yang login
    //     $alamatList = $pembeli->alamat()->get();

    //     if($alamatList->isEmpty()){
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Tidak ada alamat ditemukan untuk pembeli ini',
    //             'data' => null,
    //         ], 404);
    //     }else{
    //         return response()->json([
    //             'status' => true,
    //             'data' => $alamatList,
    //         ]);
    //     }
    // }


    // public function showDiskusiBarang($id)
    // {
    //     $barang = Barang::find($id);

    //     if (!$barang) {
    //         return response()->json([
    //             'message' => 'Barang not found'
    //         ], 404);
    //     }

    //     $DiskusiList = $barang->diskusi()->with(['pembeli', 'pegawai'])->get();;

    //     if($DiskusiList->isEmpty()){
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Tidak ada diskusi ditemukan untuk pembeli ini',
    //             'data' => null,
    //         ], 404);
    //     }else{
    //         return response()->json([
    //             'status' => true,
    //             'data' => $DiskusiList,
    //         ]);
    //     }
    // }


    public function getByBarang($idBarang)
    {
        $barang = Barang::find($idBarang);

        if (!$barang) {
            return response()->json([
                'message' => 'Barang not found'
            ], 404);
        }

        $diskusi = Diskusi::with(['pembeli', 'pegawai'])
            ->where('idBarang', $idBarang)
            ->orderBy('tanggalDiskusi', 'asc')
            ->orderBy('waktuMengirimDiskusi', 'asc')
            ->get();
        
        return response()->json([
            'status' => true,
            'data' => $diskusi
        ], 200);
    }


}
