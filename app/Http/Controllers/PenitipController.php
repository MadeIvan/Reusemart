<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Penitip;
use App\Models\dompets;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; 
use App\Http\Controllers\DompetController;
use App\Http\Controllers\TransaksiPenitipanController;
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
            \Log::info("Created new penitip ID: {$newId}");
            $dompet = (new DompetController)->createDompetPenitip(null);
            $idDompet = (string) $dompet->idDompet;

            \Log::info("Created new dompet with ID: {$idDompet}");
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


}


