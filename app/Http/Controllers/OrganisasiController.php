<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organisasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class OrganisasiController extends Controller
{
    public function index() //show
    {
        try{
            $data = Organisasi::all();
            return response()->json([
                "status" => true,
                "message" => "Get successful",
                "data" => $data
            ], 200);
        }catch(Exception $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 400);
        }
    }

    public function show($id){ //search
        try{
            $data = Organisasi::find($id);
            return response()->json([
                "status" => true,
                "message" => "Get successful",
                "data" => $data
            ], 200);
        }catch(Exception $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 400);
        }
    }

    public function register(Request $request){ //register
        $request->validate([
            'username' => 'required|string|max:255',
            'password'=> 'required|string|min:8',
            'namaOrganisasi'=> 'required|string|max:255',
            'alamat'=> 'required|string|max:255',
        ]);
        $last = Organisasi::orderBy('idOrganisasi', 'desc')->first();
        $lastNumber = 0;

        if ($last) {
            // Ambil angka dari ID terakhir, misalnya 'ORG12' -> 12
            $lastNumber = (int) str_replace('ORG', '', $last->idOrganisasi);
        }

        // Generate ID baru dengan menambahkan angka terakhir + 1
        $newId = 'ORG' . ($lastNumber + 1);

        // Cek apakah ID baru sudah ada, baik yang aktif maupun yang sudah dihapus (soft delete)
        while (Organisasi::where('idOrganisasi', $newId)->withTrashed()->exists()) {
            $lastNumber++;  // Increment angka ID
            $newId = 'ORG' . $lastNumber;  // Update ID baru
        }

        $organisasi = Organisasi::create([
            'idOrganisasi' => $newId,
            'username' => $request->username,
            'password'=> Hash::make($request->password),
            'namaOrganisasi'=> $request->namaOrganisasi,
            'alamat'=> $request->alamat
        ]);


        // $organisasi = Organisasi::create([
        //     'username' => $request->username,
        //     'password'=> Hash::make($request->password),
        //     'namaOrganisasi'=> $request->namaOrganisasi,
        //     'alamat'=> $request->alamat
        // ]);

        return response()->json([
            "status" => true,
            "message" => "Get successful",
            "data" => $organisasi
        ], 200);

        // try{
        //     $data = Organisasi::create($request->all());
        //     return response()->json([
        //         "status" => true,
        //         "message" => "Get successful",
        //         "data" => $data
        //     ], 200);
        // }catch(Exception $e){
        //     return response()->json([
        //         "status" => false,
        //         "message" => $e->getMessage(),
        //         "data" => null
        //     ], 400);
        // }
    }

    public function login(Request $request){ //login
        $request->validate([
            'username' => 'required|string|max:255',
            'password'=> 'required|string|min:8',
        ]);

        
        $organisasi = Organisasi::where('username', $request->username)->first();
        if($organisasi){
            if($organisasi->delete_at){
                return response()->json([
                    "status" => false,
                    "message" => "Your account has been deactivated.",
                ], 403);
            }else if(Hash::check($request->password, $organisasi->password)){
                $token = $organisasi->createToken('Personal Access Token')->plainTextToken;

                return response()->json([
                    "status" => true,
                    "message" => "Get successful",
                    "data" => [
                        "organisasi" => $organisasi,
                        "token" => $token
                    ]
                ], 200);
            }
            return response()->json([
                "status" => false,
                "message" => "Invalid credentials",
            ], 401);
        }
    }

    public function update(Request $request, $id){ //update
        $organisasi = Organisasi::find($id);
        
        if(!$organisasi){
            return response()->json([
                "status" => false,
                "message" => "Organisasi not found",
                "data" => null
            ], 404);
        }

        $validatedData = $request->validate([
            'username' => 'required',
            'namaOrganisasi'=> 'required',
            'alamat'=> 'required',
        ]);

        $organisasi->username = $validatedData['username'];
        $organisasi->namaOrganisasi = $validatedData['namaOrganisasi'];
        $organisasi->alamat = $validatedData['alamat'];

        $organisasi->update($validatedData);

        try{
            $organisasi->update($validatedData);
            return response()->json([
                "status" => true,
                "message" => "Update successfull",
                "data" => $organisasi,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Something went wrong",
                "data" => $e->getMessage(),
            ], 400);
        }
    }

    // public function destroy($id){
    //     try{
    //         $data = Organisasi::find($id);
    //         $data->delete();
    //         return response()->json([
    //             "status" => true,
    //             "message" => "Get successful",
    //             "data" => $data
    //         ], 200);
    //     }catch(Exception $e){
    //         return response()->json([
    //             "status" => false,
    //             "message" => $e->getMessage(),
    //             "data" => null
    //         ], 400);
    //     }
    // }

    public function destroy($id){ //delete
        // $data = Auth::user();
        $data = Organisasi::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'You are not logged in',
            ], 403);
        }
        
        $data->delete();

        return response()->json([
            'message' => 'Customer deleted successfully',
        ]);
    }
}
