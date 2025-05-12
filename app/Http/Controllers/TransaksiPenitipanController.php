<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransaksiPenitipanController extends Controller
{
    public function store(Request $request){ //register
        

        $request->validate([
            'idPegawai2'=> 'nullable|string', //hunter
            'idPenitip'=> 'required|string8',
            'tanggalPenitipan'=> 'required|date_format:Y-m-d H:i:s',
            'totalHarga'=> 'required|integer',
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
}
