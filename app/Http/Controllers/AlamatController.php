<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alamat;
use App\Models\Pembeli;
use Illuminate\Support\Facades\Auth;

class AlamatController extends Controller
{
    public function store(Request $request){
        try{
            $pembeliId = Auth::id(); 
    
            $request->validate([
                'alamat'=> 'required|string|max:255',
                'kategori'=> 'required|string',
                'isDefault'=> 'required|boolean',
            ]);
            $last = Alamat::orderBy('idAlamat', 'desc')->first();
            $lastNumber = 0;
    
            if ($last) {
                $lastNumber = (int) str_replace('A', '', $last->idAlamat);
            }
    
            $newId = 'A' . ($lastNumber + 1);
            
    
            while (Alamat::where('idAlamat', $newId)->exists()) {
                $lastNumber++;
                $newId = 'A' . $lastNumber;
            }
    
            $alamat = Alamat::create([
                'idAlamat' => $newId,
                'idPembeli' => $pembeliId,
                'alamat' => $request->alamat,
                'kategori'=> $request->kategori,
                'isDefault'=> $request->isDefault
            ]);
    
            return response()->json([
                "status" => true,
                "message" => "Get successful",
                "data" => $alamat
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
        // return response()->json([
        //     "status" => true,
        //     "message" => "MALAS",
        // ]);
//-------------------------------------
        // $data = Alamat::all();
        //     return response()->json([
        //         "status" => true,
        //         "message" => "Get successful",
        //         "data" => $data
        //     ], 200);
//-------------------------------------
        // try {
        //     if (!Auth::guard('pembeli')->check()) {
        //         return response()->json([
        //             "status" => false,
        //             "message" => "Pembeli Belum Login",
        //             "data" => null
        //         ], 400);
        //     }
    
        //     // Mendapatkan data pembeli yang sedang login
        //     // $pembeli = Auth::guard('pembeli')->user();
        //     // $pembeliId = $pembeli->idPembeli;

        //     $pembeliId = Auth::id();
    
        //     $alamat = Alamat::where('idPembeli', $pembeliId)->get();

        //     if ($alamat->isEmpty()) {
        //         return response()->json([
        //             "status" => false,
        //             "message" => "Tidak ada alamat ditemukan untuk pembeli ini",
        //             "data" => null
        //         ], 404);
        //     } 

        //     return response()->json([
        //         "status" => true,
        //         "message" => "Get Successfull",
        //         "data" => $alamat
        //     ], 200);
        // } catch (Exception $e) {
        //     return response()->json([
        //         "status" => false,
        //         "message" => "Something went wrong",
        //         "data" => $e->getMessage()
        //     ], 400);
        // }
//-------------------------------------
        // try{
        //     // $pembeliId = Auth::id();
        //     Auth::guard('pembeli')->check();
        //     $pembeliId = Auth::guard('pembeli')->user()->id;

        //     if(!$pembeliId){
        //         return response()->json([
        //             "status" => false,
        //             "message" => "Pembeli Belum Login",
        //             "data" => $pembeliId,
        //         ], 400);
        //     }

        //     $alamat = Alamat::where('idPembeli', $pembeliId)->get();
        //     return response()->json([
        //         "status" => true,
        //         "message" => "Get Successfull",
        //         "data" => $alamat
        //     ], 200);    
        // }catch(Exception $e){
        //     return response()->json([
        //         "status" => false,
        //         "message" => "Something went wrong",
        //         "data" => $e->getMessage()
        //     ], 400);
        // }
//-------------------------------------.
        // $pembeli = auth('pembeli')->user();
        // $pembeliId = $pembeli->idPembeli;
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

    public function show($id)
    {
        // $pembeli = auth('pembeli')->user();
        $pembeli = Auth::id();
        if(!$pembeli){
            return response()->json([
                "status" => false,
                "message" => "Pembeli Belum Login",
                "data" => null,
            ], 400);
        }
        
        try{
            $alamat = Alamat::where('idAlamat', $id)
                        ->where('idPembeli', $pembeli)
                        ->first();
            if($alamat){
                return response()->json([
                    "status" => true,
                    "message" => "Get Successfull",
                    "data" => $alamat
                ], 200);    
            }else{
                return response()->json([
                    "status" => false,
                    "message" => "Alamat Tidak Ditemukan",
                    "data" => null
                ], 404);
            }
        }catch(Exception $e){
            return response()->json([
                "status" => false,
                "message" => "Something went wrong",
                "data" => $e->getMessage()
            ], 400);
        }
    }

    public function delete($id){
        try{
            $pembeli = Auth::user();
            if(!$pembeli){
                return response()->json([
                    "status" => false,
                    "message" => "Pembeli Belum Login",
                    "data" => null,
                ], 400);
            }else{
                $alamat = Alamat::find($id);
                $alamat->delete();
                return response()->json([
                    "status" => true,
                    "message" => "Get successful",
                    "data" => $alamat
                ], 200);
            }
        }catch(Exception $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 400);
        }
    }

    public function update(Request $request, $id){
        $request->validate([
            'alamat'=> 'required|string|max:255',
            'kategori'=> 'required|string',
            'isDefault'=> 'required|boolean',
        ]);

        //cek id pembeli
        $pembeliId = Auth::id();

        // cek idAlamat
        $alamat = Alamat::where('idAlamat', $id)
                        ->where('idPembeli', $pembeliId)
                        ->first();

        if(!$alamat){
            return response()->json([
                "status" => false,
                "message" => "alamat not found",
                "data" => null
            ], 404);
        }else{
            $alamat->update([
                'alamat' => $request->alamat,
                'kategori'=> $request->kategori,
                'isDefault'=> $request->isDefault
            ]);

            return response()->json([
                "status" => true,
                "message" => "Update successfull",
                "data" => $alamat,
            ], 200);
        }
    }


}
