<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiskusiController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'idBarang'=> 'required|string|max:16',
            'pesandiskusi'=> 'required|string|max:255',
            'tanggalDiskusi'=> 'required|date',
            'waktuMengirimDiskusi'=> 'required|date_format:H:i:s',
        ]);

        //cek id barang
        $barangId = $request->idBarang;
        $barang = Barang::find($barangId);
        if(!$barang || $barang->id != $barangId){
            return response()->json([
                'message' => "Barang not found",
            ], 403);
        }

        // id Diskusi 
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

        $data = [
            'idBarang'=> $barangId,
            'pesandiskusi'=> $request->pesandiskusi,
            'tanggalDiskusi'=> now()->toDateString(),
            'waktuMengirimDiskusi'=> now()->toTimeString(),
        ];

        // cek siapa yang login
        if(Auth::guard('pembeli')->check()){
            $data['idPembeli'] = Auth::guard('pembeli')->user()->id;
        }else if(Auth::guard('pegawai')->check()){
            $data['idPegawai'] = Auth::guard('pegawai')->user()->id;
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

    public function index()
    {
        $pembeli = Auth::user();

        // Ambil semua alamat milik pembeli yang login
        $alamatList = $pembeli->alamat()->get();

        if($alamatList->isEmpty()){
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada alamat ditemukan untuk pembeli ini',
                'data' => null,
            ], 404);
        }else{
            return response()->json([
                'status' => true,
                'data' => $alamatList,
            ]);
        }
    }

    public function getByBarang($idBarang)
    {
        $barang = Barang::find($idBarang);

        if (!$barang) {
            return response()->json([
                'message' => 'Barang not found'
            ], 404);
        }

        $diskusi = Diskusi::with(['pembeli', 'pegawai']) // relasi jika ingin ambil data nama pembeli/pegawai
                    ->where('idBarang', $idBarang)
                    ->orderBy('tanggalDiskusi', 'asc')
                    ->orderBy('waktuMengirimDiskusi', 'asc')
                    ->get();

        return response()->json([
            'message' => 'List diskusi for barang',
            'data' => $diskusi
        ], 200);
    }

}
