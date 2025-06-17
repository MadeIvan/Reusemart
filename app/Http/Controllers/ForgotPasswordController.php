<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:penitip,pembeli,organisasi',
        ]);

        $email = $request->email;
        $role = $request->role;

        $model = match($role) {
            'pembeli' => \App\Models\Pembeli::class,
            'penitip' => \App\Models\Penitip::class,
            'organisasi' => \App\Models\Organisasi::class,
        };

        $user = $model::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan'], 404);
        }

        $token = Str::random(40);
        $url = url('/newPassword') . '?' . http_build_query([
            'email' => $email,
            'token' => $token,
            'role' => $role,
            'expires' => Carbon::now()->addMinutes(30)->timestamp,
        ]);

        cache()->put("reset_token_{$email}", [
            'token' => $token,
        ], 1800);

        Mail::raw("Klik link berikut untuk reset password:\n\n$url", function ($message) use ($email) {
            $message->to($email)->subject('Reset Password');
        });

        return response()->json(['message' => 'Link reset dikirim ke email.']);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'role' => 'required|in:pembeli,penitip,organisasi',
            'expires' => 'required|integer',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (now()->timestamp > $request->expires) {
            return response()->json(['message' => 'Link sudah kadaluarsa'], 400);
        }

        switch ($request->role) {
            case 'pembeli':
                $userModel = \App\Models\Pembeli::class;
                break;
            case 'penitip':
                $userModel = \App\Models\Penitip::class;
                break;
            case 'organisasi':
                $userModel = \App\Models\Organisasi::class;
                break;
            default:
                return response()->json(['message' => 'Role tidak dikenali'], 400);
        }

        $user = $userModel::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan'], 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['message' => 'Password berhasil direset']);
    }
}
