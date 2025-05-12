<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jabatan;

class JabatanController extends Controller
{
    //
    public function index()
    {
        $jabatan = Jabatan::where('namaJabatan', '!=', 'Owner')->get();

        return response()->json([
            'status' => true,
            'data' => $jabatan
        ]);
    }
}
