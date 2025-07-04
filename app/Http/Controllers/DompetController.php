<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dompet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class DompetController extends Controller
{
    public function getAllDompet()
    {

        $dompet = Dompet::all();
        return response()->json($dompet);
    }

    public function getDompetById($id)
    {

        $dompet = Dompet::find($id);

        if ($dompet) {
            return response()->json($dompet);
        } else {
            return response()->json(['message' => 'Dompet not found'], 404);
        }   
    }

    public function createDompetPenitip($idPenitip)
    {
        $lastDompet = DB::select("SELECT MAX(CAST(idDompet AS UNSIGNED)) AS last_id FROM dompet");
        $lastDompet = $lastDompet[0]->last_id;
        $newId = $lastDompet ? (intval($lastDompet) + 1) : 1; 

        $dompet = Dompet::create([

            'idDompet' => $newId, 
            'idPegawai' => null, 
            'idPenitip' => $idPenitip ?? null, 
            'saldo' => 0.0, 
        ]);

        return $dompet;
    }

    public function updateDompet($idPenitip, $saldo, $idDompet)
    {


        $dompet = Dompet::find($idDompet);


        // Check if dompet exists
        if (!$dompet) {
            $errorMessage = 'Dompet not found';
            
            // Log the error
            Log::error($errorMessage, [
                'idPenitip' => $idPenitip,
                'saldo' => $saldo,
                'idDompet' => $idDompet,
            ]);

            return response()->json(['message' => $errorMessage], 404);
        }

        // Validate the data
        if (!is_string($idPenitip) || !is_numeric($saldo)) {
            $errorMessage = 'Invalid data provided';
            
            // Log the error
            Log::error($errorMessage, [
                'idPenitip' => $idPenitip,
                'saldo' => $saldo,
                'idDompet' => $idDompet,
            ]);

            return response()->json(['message' => $errorMessage], 400);
        }

        try {
            // Update the dompet properties
            $dompet->idPenitip = $idPenitip;
            $dompet->saldo = $saldo;

            // Save the updated dompet
            $dompet->save();

            // Log successful update
            Log::info('Dompet updated successfully', [
                'idPenitip' => $idPenitip,
                'saldo' => $saldo,
                'idDompet' => $idDompet,
            ]);

            return response()->json($dompet); // Return the updated dompet

        } catch (\Exception $e) {
            // Log the exception and error message
            $errorMessage = 'Failed to update dompet';
            
            Log::error($errorMessage, [
                'exception' => $e->getMessage(),
                'idPenitip' => $idPenitip,
                'saldo' => $saldo,
                'idDompet' => $idDompet,
            ]);

            return response()->json([
                'message' => $errorMessage,
                'error' => $e->getMessage()
            ], 500);
        }
    }
public function getDompetByPegawai($idPegawai)
{
    if (empty($idPegawai)) {
        return response()->json([
            'status' => false,
            'message' => 'idPegawai is required'
        ], 400);
    }
    $dompet = \App\Models\Dompet::where('idPegawai', $idPegawai)->first();
    
    \Log::info('Raw idPegawai: ' . json_encode($idPegawai));
    if ($dompet) {
        return response()->json([
            'status' => true,
            'data' => $dompet
        ]);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'Dompet not found for employee: ' . $idPegawai
        ], 404);
    }
}
}
