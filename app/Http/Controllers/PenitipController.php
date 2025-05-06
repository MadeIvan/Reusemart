<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penitip;


class PenitipController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'usernama' => 'required|string',
            'password' => 'required|string',
        ]);

        $penitip = Penitip::where('usernama', $request->usernama)->first();

        if (!$penitip) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($penitip->password !== $request->password) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Success
        return response()->json([
            'message' => 'Login successful',
            'penitip' => [
                'idPenitip' => $penitip->idPenitip,
                'usernama' => $penitip->usernama,
                'namaPenitip' => $penitip->namaPenitip,
                'idTopeseller' => $penitip->idTopeseller,
                'idDompet' => $penitip->idDompet
            ]
        ]);
    }
}
