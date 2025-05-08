<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
        if (Auth::check()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }
    
        return response()->json(['message' => 'Not logged in'], 401);
    }
}