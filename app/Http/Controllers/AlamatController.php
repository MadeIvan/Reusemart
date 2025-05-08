<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alamat;
use App\Models\Pembeli;
use Illuminate\Support\Facades\Auth;

class AlamatController extends Controller
{
    ////////////////////////////STORE///////////////////////////////
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

    //////////////////////SHOW ALL ///////////////////////
    public function index()
    {
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


    ////////////////////SHOW BY ID////////////////////////
    public function show(Request $request)
    {
        $pembeli = Auth::id();
        if(!$pembeli){
            return response()->json([
                "status" => false,
                "message" => "Pembeli Belum Login",
                "data" => null,
            ], 400);
        }

        try{
            $query = $request->input('q');
    
            $results = Alamat::where('alamat', 'like', '%' . $query . '%')
                ->get();
    
            return response()->json([
                'status' => 200,
                'data' => $results,
            ]);
        }catch(Exception $e){
            return response()->json([
                "status" => false,
                "message" => "Something went wrong",
                "data" => $e->getMessage()
            ], 400);
        }
    }


    ////////////////////////////////DELETE///////////////////////////////
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

    ////////////////////////UPDATE/////////////////////////
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
