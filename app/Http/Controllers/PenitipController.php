<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penitip;
use App\Models\dompets;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; 
use App\Http\Controllers\DompetController;
use Illuminate\Support\Facades\DB;

class PenitipController extends Controller
{
    // Login function for Penitip
    public function login(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find penitip by username
        $penitip = Penitip::whereRaw('BINARY username = ?', [$request->username])->first();

        // Check if user exists
        if (!$penitip) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if password is correct
        if (!Hash::check($request->password, $penitip->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate access token for the authenticated user
        $token = $penitip->createToken('Personal Access Token')->plainTextToken;
        // Return response with user details and token
        return response()->json([
            'message' => 'Login successful',
            'penitip' => [
                'idPenitip' => $penitip->idPenitip,
                'username' => $penitip->username,
                'namaPenitip' => $penitip->namaPenitip,
                'idTopeSeller' => $penitip->idTopeSeller,
                'idDompet' => $penitip->idDompet,
                'Token' => $token
            ]
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

    // Register function for Penitip
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:penitip,username|max:255',
            'password' => 'required|string|min:6',
            'namaPenitip' => 'required|string|max:255',
            'nik' => 'required|string|size:16',
            'idTopeSeller' => 'nullable|integer',
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

            $dompet = (new DompetController)->createDompetPenitip(null);
            $idDompet = (string) $dompet->idDompet;

            \Log::info("Created new dompet with ID: {$idDompet}");
            $penitip = Penitip::create([
                'idPenitip' => $newId,
                'username' => $request->username,
                'password' => Hash::make($request->password), 
                'namaPenitip' => $request->namaPenitip,
                'nik' => $request->nik,
                'idTopeSeller' => $request->idTopeSeller,
                'idDompet' => $idDompet,
            ]);
            \Log::info("{$penitip->idPenitip},{$dompet->saldo}, {$idDompet}");
            $dompet=(new DompetController)->updateDompet($penitip->idPenitip, $dompet->saldo, $idDompet);
            return response()->json([
                'message' => 'Penitip registered successfully!',
                'penitip' => $penitip
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
