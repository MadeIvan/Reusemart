<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Penitip;
use Illuminate\Support\Facades\Hash;



class PenitipController extends Controller
{
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'usernama' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     $penitip = Penitip::where('usernama', $request->usernama)->first();

    //     if (!$penitip) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     if ($penitip->password !== $request->password) {
    //         return response()->json(['message' => 'Invalid credentials'], 401);
    //     }

    //     // Success
    //     return response()->json([
    //         'message' => 'Login successful',
    //         'penitip' => [
    //             'idPenitip' => $penitip->idPenitip,
    //             'usernama' => $penitip->usernama,
    //             'namaPenitip' => $penitip->namaPenitip,
    //             'idTopeseller' => $penitip->idTopeseller,
    //             'idDompet' => $penitip->idDompet
    //         ]
    //     ]);
    // }
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password'=> 'required|string|min:8',
        ]);

        $penitip = Penitip::where('username', $request->username)->first();

        if (!$penitip) {
            return response()->json([
                "status" => false,
                "message" => "User not found",
            ], 404);
        }

        if ($penitip->deleted_at) {
            return response()->json([
                "status" => false,
                "message" => "Your account has been deactivated.",
            ], 403);
        }

        if (!Hash::check($request->password, $penitip->password)) {
            return response()->json([
                "status" => false,
                "message" => "Invalid credentials",
            ], 401);
        }

        // Login success
        $token = $penitip->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            "status" => true,
            "message" => "Login successful",
            "data" => [
                "pembeli" => $penitip,
                "token" => $token
            ]
        ], 200);
    }


    public function register(Request $request){ //register
        $request->validate([
            'username'=> 'required|string|max:255',
            'password'=> 'required|string|min:8',
            'namaPenitip'=> 'required|string|max:255',
            'nik'=> 'required|string|max:16',
            'alamat'=> 'required|string|max:255',
        ]);
        $last = Penitip::orderBy('idPenitip', 'desc')->first();
        $lastNumber = 0;

        if ($last) {
            $lastNumber = (int) str_replace('T', '', $last->idPenitip);
        }

        $newId = 'T' . ($lastNumber + 1);

        while (Penitip::where('idPenitip', $newId)->withTrashed()->exists()) {
            $lastNumber++;
            $newId = 'T' . $lastNumber;
        }

        $dompet = (new DompetController)->createDompetPenitip(null);
        $idDompet = (string) $dompet->idDompet;


        $penitip = Penitip::create([
            'idPenitip' => $newId,
            'idTopeseller'=> null,
            'idDompet'=> $idDompet,
            'username'=>  $request->username,
            'password'=>  Hash::make($request->password),
            'namaPenitip'=> $request->namaPenitip,
            'nik'=> $request->nik,
            'alamat'=> $request->alamat
        ]);

        return response()->json([
            "status" => true,
            "message" => "Get successful",
            "data" => $penitip
        ], 200);
    }

    public function show($id){ //search
        try{
            $data = Penitip::find($id);
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

    public function checkNIK(Request $request)
    {
        $nikExists = Penitip::where('nik', $request->nik)->exists();

        return response()->json([
            'nikExists' => $nikExists,
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
}
