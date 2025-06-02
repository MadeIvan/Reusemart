<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FCMTokenController extends Controller
{
    public function registerFcmToken(Request $request)
    {
        // Coba ambil user dari masing-masing guard
        $user = auth('pegawai')->user() ?? auth('penitip')->user() ?? auth('pembeli')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(['message' => 'FCM token registered successfully']);
    }

}
