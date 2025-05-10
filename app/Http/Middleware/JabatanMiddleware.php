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

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized. You do not have permission.',
            ], 403);

        }
        
        if(!in_array($user->idJabatan, $idjabatan)){
            return response()->json([
                'message' => 'Forbidden. You do not have permission.',
            ], 403);
        }
        
        return $next($request);
    }
}