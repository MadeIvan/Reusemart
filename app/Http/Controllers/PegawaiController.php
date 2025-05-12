<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Exception;
use Illuminate\Support\Facades\Log;
use Nette\Schema\ValidationException;
use Carbon\Carbon;
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
            // 'idDompet'=> 'string|max:255',
            'namaPegawai'=> 'required|string|max:255',
            // 'tanggalLahir'=> 'required|date',
            'username'=> 'required|string|max:255',
            'password'=> 'required|string|max:255',
        ]);

        /////////////////GENERATE ID PEGAWAI////////////////////////
        $last = Pegawai::orderBy('idPegawai', 'desc')->first();
        $lastNumber = 0;

        if ($last) {
            // Ambil angka dari ID terakhir, misalnya 'ORG12' -> 12
            $lastNumber = (int) str_replace('P', '', $last->idPegawai);
        }

        // Generate ID baru dengan menambahkan angka terakhir + 1
        $newId = 'P' . ($lastNumber + 1);

        // Cek apakah ID baru sudah ada, baik yang aktif maupun yang sudah dihapus (soft delete)
        while (Pegawai::where('idPegawai', $newId)->withTrashed()->exists()) {
            $lastNumber++;  // Increment angka ID
            $newId = 'P' . $lastNumber;  // Update ID baru
        }

        /////////////////GENERATE ID DOMPET////////////////////////
        $dompet = (new DompetController)->createDompetPenitip(null);
        $idDompet = (string) $dompet->idDompet;

        // \Log::info("Created new dompet with ID: {$idDompet}");

        /////////////////CREATE PEGAWAI////////////////////////
        $pegawai = Pegawai::create([
            'idPegawai' => $newId,
            'idJabatan'=> $request->idJabatan,
            'idDompet'=> $idDompet,
            'namaPegawai'=> $request->namaPegawai,
            // 'tanggalLahir'=> $request->tanggalLahir,
            'username'=> $request->username,
            'password'=> Hash::make($request->password),
        ]);

        return response()->json([
            "status" => true,
            "message" => "Create successful",
            "data" => $pegawai
        ], 200);
    }

    public function login(Request $request){ //login
        $request->validate([
            'username' => 'required|string|max:255',
            'password'=> 'required|string|min:8',

        ]);

        
        $pegawai = Pegawai::where('username', $request->username)->first();
        if($pegawai){
            if($pegawai->delete_at){
                return response()->json([
                    "status" => false,
                    "message" => "Your account has been deactivated.",
                ], 403);
            }else if(Hash::check($request->password, $pegawai->password)){
                $token = $pegawai->createToken('Personal Access Token')->plainTextToken;

                return response()->json([
                    "status" => true,
                    "message" => "Get successful",
                    "data" => [
                        "pegawai" => $pegawai,
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



public function update(Request $request, $id)
{
    Log::info("Attempting to update Pegawai with ID: $id");

    $pegawai = Pegawai::find($id);

    if (!$pegawai) {
        Log::warning("Pegawai not found for ID: $id");
        return response()->json([
            "status" => false,
            "message" => "Pegawai not found",
            "data" => null
        ], 404);
    }

    Log::info("Request data received:", $request->all());

    try {
        $validatedData = $request->validate([
            'namaPegawai' => 'required|string',
            'username' => 'required|string',
        ]);

        Log::info("ValidatedData:", $validatedData);

        $pegawai->update($validatedData);

        Log::info("Pegawai updated successfully: ID $id");

        return response()->json([
            "status" => true,
            "message" => "Update successful",
            "data" => $pegawai,
        ], 200);

    } catch (ValidationException $e) {
        // Log::error("Validation failed:", $e->errors());

        return response()->json([
            "status" => false,
            "message" => "Validation failed",
            // "errors" => $e->errors(),
        ], 422);

    } catch (\Exception $e) {
        Log::error("Unexpected error on updating Pegawai ID $id: " . $e->getMessage());

        return response()->json([
            "status" => false,
            "message" => "Something went wrong",
            "error" => $e->getMessage(),
        ], 400);
    }
}

public function softDelete($id)
{
    $pegawai = Pegawai::find($id);

    if (!$pegawai) {
        return response()->json([
            'status' => false,
            'message' => 'Pegawai not found.'
        ], 404);
    }

    // Assign current date to deleted_at
    $pegawai->deleted_at = Carbon::now();
    $pegawai->save();

    return response()->json([
        'status' => true,
        'message' => 'Pegawai soft deleted successfully.'
    ], 200);
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

    function resetPassword($id){
        $pegawai = Pegawai::find($id);

        $pegawai->password =  Hash::make($pegawai->tanggalLahir);
        $pegawai->update();

        return response()->json([
            'message' => 'Berhasil',
        ]);
    }
}