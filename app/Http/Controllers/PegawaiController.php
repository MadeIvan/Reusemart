<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class PegawaiController extends Controller
{
    public function index() //show
    {
        try{
            $data = Pegawai::all();
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
            $data = Pegawai::find($id);
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
            'idJabatan'=> 'required|string',
            'namaPegawai'=> 'required|string|max:255',
            'tanggalLahir'=> 'required|date',
            'username'=> 'required|string|max:255',
            'password'=> 'required|string|max:255',
        ]);

        /////////////////GENERATE ID PEGAWAI////////////////////////
        $last = Pegawai::orderBy('idPegawai', 'desc')->first();
        $lastNumber = 0;

        if ($last) {
            $lastNumber = (int) str_replace('P', '', $last->idPegawai);
        }
        $newId = 'P' . ($lastNumber + 1);

        while (Pegawai::where('idPegawai', $newId)->withTrashed()->exists()) {
            $lastNumber++;  // Increment angka ID
            $newId = 'P' . $lastNumber;  // Update ID baru
        }

        /////////////////GENERATE ID DOMPET////////////////////////
        $dompet = (new DompetController)->createDompetPenitip(null);
        $idDompet = (string) $dompet->idDompet;

        \Log::info("Created new dompet with ID: {$idDompet}");

        /////////////////CREATE PEGAWAI////////////////////////
        $pegawai = Pegawai::create([
            'idPegawai' => $newId,
            'idJabatan'=> $request->idJabatan,
            'idDompet'=> $idDompet,
            'namaPegawai'=> $request->namaPegawai,
            'tanggalLahir'=> $request->tanggalLahir,
            'username'=> $request->username,
            'password'=> Hash::make($request->password),
        ]);

        return response()->json([
            "status" => true,
            "message" => "Create successful",
            "data" => $pegawai
        ], 200);
    }
    /////////////////////////[ LOGIN PEGAWAI ]////////////////////////
public function login(Request $request){
    $request->validate([
        'username' => 'required|string|max:255',
        'password' => 'required|string|max:255',
    ]);

    // Include soft-deleted users in the query
    $pegawai = Pegawai::withTrashed()
        ->whereRaw('BINARY username = ?', [$request->username])
        ->first();

    if ($pegawai) {
        if ($pegawai->deleted_at !== null) {
            return response()->json([
                "status" => false,
                "message" => "Your account has been deactivated.",
            ], 403);
        }
        if (Hash::check($request->password, $pegawai->password)) {
            $token = $pegawai->createToken('Personal Access Token')->plainTextToken;

            return response()->json([
                "status" => true,
                "message" => "Login successful",
                "data" => [
                    "pegawai" => $pegawai,
                    "token" => $token,
                ],
                
            ], 200);
        }
    }

    // If user not found or password incorrect
    return response()->json([
        "status" => false,
        "message" => "Invalid credentials",
    ], 401);
}

    public function update(Request $request, $id){ //update
        $pegawai = Pegawai::find($id);
        
        if(!$pegawai){
            return response()->json([
                "status" => false,
                "message" => "Pegawai not found",
                "data" => null
            ], 404);
        }

        $validatedData = $request->validate([
            'namaPegawai'=> 'required',
            'username'=> 'required',
            // 'password'=> 'required',
        ]);

        // $pegawai->namaPegawai = $validatedData['namaPegawai'];
        // $pegawai->username = $validatedData['username'];
        // $pegawai->password = $validatedData['password'];

        $pegawai->update($validatedData);

        try{
            $pegawai->update($validatedData);
            return response()->json([
                "status" => true,
                "message" => "Update successfull",
                "data" => $pegawai,
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
        $data = Pegawai::find($id);

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

    // public function checkEmailUsername(Request $request)
    // {
    //     $emailExists = Organisasi::where('email', $request->email)->exists();
    //     $usernameExists = Organisasi::where('username', $request->username)->exists();

    //     return response()->json([
    //         'emailExists' => $emailExists,
    //         'usernameExists' => $usernameExists
    //     ]);
    // }
}