<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\PersonalAccessToken;

class LoginController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return response()->json([
                'message' => 'Already authenticated',
                'user' => Auth::user()
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }

    public function actionlogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password'=> 'required|string|min:8',
        ]);

        if (Auth::attempt($data)) {
            if(Auth::user()->name == "Admin"){
                return redirect('/admin/dashboard');
            }
            else {
                return redirect('/');
            }
        }else{
            Session::flash('error', 'Wrong Email or Password');
            
            return redirect('/login');
        }
    }

    // public function login(Request $request){ //login
    //     $request->validate([
    //         'username' => 'required|string|max:255',
    //         'password'=> 'required|string|min:8',
    //     ]);

    //     $organisasi = Organisasi::where('username', $request->username)->first();

    //     if (!$organisasi ) {
    //         return response()->json([
    //             "status" => false,
    //             "message" => "Invalid credentials",
    //         ], 401);
    //     }

    //     if ($organisasi && $organisasi->deleteAt) {
    //         return response()->json([
    //             "status" => false,
    //             "message" => "Your account has been deactivated.",
    //         ], 403);
    //     }

    //     if (!Hash::check($request->password, $organisasi->password)) {
    //         return response()->json([
    //             "status" => false,
    //             "message" => "Invalid credentials",
    //         ], 401);
    //     }

    //     $token = $organisasi->createToken('Personal Access Token')->plainTextToken;

    //     return response()->json([
    //         "status" => true,
    //         "message" => "Get successful",
    //         "data" => [
    //             "organisasi" => $organisasi,
    //             "token" => $token
    //         ]
    //     ], 200);
    // }


    public function logout (Request $request){
        if (Auth::guard('penitip')->check()) {
            $user = Auth::guard('penitip')->user();
        } elseif (Auth::guard('pembeli')->check()) {
            $user = Auth::guard('pembeli')->user();
        } elseif (Auth::guard('pegawai')->check()) {
            $user = Auth::guard('pegawai')->user();
        } else {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        // Hapus token aktif
        if (method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }

        // Atau hapus token pakai header manual
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $tokenValue = explode(' ', $authHeader)[1];
            $token = PersonalAccessToken::findToken($tokenValue);
            if ($token) {
                $token->delete();
                return response()->json(['message' => 'Logged out successfully']);
            }
        }

        return response()->json(['message' => 'No token found'], 401);
    }
}