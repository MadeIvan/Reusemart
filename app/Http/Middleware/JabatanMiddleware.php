<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JabatanMiddleware
{
    public function handle(Request $request, Closure $next, $idjabatan)
    {
        $user = Auth::guard('pegawai')->user();

        if ($user || in_array($user->idJabatan, $idjabatan)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthorized. You do not have permission.',
        ], 403);
    }
}