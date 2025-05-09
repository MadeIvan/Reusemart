<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pembeli;
use App\Models\Alamat;

class PembeliController extends Controller
{
    public function register(Request $request){ //register
        $request->validate([
            'username' => 'required|string|max:255',
            'password'=> 'required|string|min:8',
            'namaPembeli'=> 'required|string|max:255',
            'email'=> 'required|string|max:255',
        ]);
        $last = Pembeli::orderBy('idPembeli', 'desc')->first();
        $lastNumber = 0;

        if ($last) {
            $lastNumber = (int) str_replace('B', '', $last->idPembeli);
        }

        $newId = 'B' . ($lastNumber + 1);

        while (Pembeli::where('idPembeli', $newId)->withTrashed()->exists()) {
            $lastNumber++;
            $newId = 'B' . $lastNumber;
        }

        $pembeli = Pembeli::create([
            'idPembeli' => $newId,
            'username' => $request->username,
            'password'=> Hash::make($request->password),
            'poin' => 0,
            'namaPembeli'=> $request->namaPembeli,
            'email'=> $request->email
        ]);

        return response()->json([
            "status" => true,
            "message" => "Get successful",
            "data" => $pembeli
        ], 200);
    }

    // public function login(Request $request){ //login
    //     $request->validate([
    //         'username' => 'required|string|max:255',
    //         'password'=> 'required|string|min:8',
    //     ]);

    //     $pembeli = Pembeli::where('username', $request->username)->first();
        
    //     if($pembeli){
    //         if($pembeli->deleteAt){
    //             return response()->json([
    //                 "status" => false,
    //                 "message" => "Your account has been deactivated.",
    //             ], 403);

    //         }else if(Hash::check($request->password, $pembeli->password)){
    //             $token = $pembeli->createToken('Personal Access Token')->plainTextToken;

    //             return response()->json([
    //                 "status" => true,
    //                 "message" => "Login successful",
    //                 "data" => [
    //                     "pembeli" => $pembeli,
    //                     "token" => $token
    //                 ]
    //             ], 200);
    //         }
    //         return response()->json([
    //             "status" => false,
    //             "message" => "Invalid credentials",
    //         ], 401);
    //     }
    // }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password'=> 'required|string|min:8',
        ]);

        $pembeli = Pembeli::where('username', $request->username)->first();

        if (!$pembeli) {
            return response()->json([
                "status" => false,
                "message" => "User not found",
            ], 404);
        }

        if ($pembeli->deleted_at) {
            return response()->json([
                "status" => false,
                "message" => "Your account has been deactivated.",
            ], 403);
        }

        if (!Hash::check($request->password, $pembeli->password)) {
            return response()->json([
                "status" => false,
                "message" => "Invalid credentials",
            ], 401);
        }

        // Login success
        $token = $pembeli->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            "status" => true,
            "message" => "Login successful",
            "data" => [
                "pembeli" => $pembeli,
                "token" => $token
            ]
        ], 200);
    }

    public function checkEmailUsername(Request $request)
    {
        $emailExists = Pembeli::where('email', $request->email)->exists();
        $usernameExists = Pembeli::where('username', $request->username)->exists();

        return response()->json([
            'emailExists' => $emailExists,
            'usernameExists' => $usernameExists
        ]);
    }

}
