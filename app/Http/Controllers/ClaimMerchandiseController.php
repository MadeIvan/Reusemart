<?php

namespace App\Http\Controllers;

use App\Models\ClaimMerchandise;
use Illuminate\Http\Request;
use App\Models\Merchandise;

use Illuminate\Support\Facades\DB;


class ClaimMerchandiseController extends Controller
{
    /**
     * Display a listing of the claims.
     */
    public function index()
    {
        // Return all claims with their related pegawai, merchandise, and pembeli
        $claims = ClaimMerchandise::with(['pegawai', 'merchandise', 'pembeli'])->get();
        return response()->json($claims);
    }

    /**
     * Store a newly created claim.
     */



public function store(Request $request)
{
    $validated = $request->validate([
        'idPegawai' => 'nullable|exists:pegawai,idPegawai',
        'idMerchandise' => 'required|exists:merchandise,idMerchandise',
        'idPembeli' => 'required|exists:pembeli,idPembeli',
        'tanggalAmbil' => 'nullable|date',
    ]);

    DB::beginTransaction();
    try {
        // Generate new idClaim: find max idClaim, increment, format as CMxxx
        $last = ClaimMerchandise::orderBy('idClaim', 'desc')->first();
        $lastNumber = 0;

        if ($last) {
            $lastNumber = (int) str_replace('CM', '', $last->idClaim);
        }
        $newId = 'CM' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        // Ensure uniqueness (in case of deleted/soft deleted)
        while (ClaimMerchandise::where('idClaim', $newId)->exists()) {
            $lastNumber++;
            $newId = 'CM' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
        }

        // Find the merchandise and decrease stock
        $merchandise = Merchandise::find($validated['idMerchandise']);
        if (!$merchandise) {
            DB::rollBack();
            return response()->json(['message' => 'Merchandise not found'], 404);
        }
        if ($merchandise->jumlahSatuan <= 0) {
            DB::rollBack();
            return response()->json(['message' => 'Stock merchandise habis'], 400);
        }
        $merchandise->jumlahSatuan -= 1;
        $merchandise->save();

        // Find the pembeli and decrease their poin
        $pembeli = \App\Models\Pembeli::find($validated['idPembeli']);
        if (!$pembeli) {
            DB::rollBack();
            return response()->json(['message' => 'Pembeli not found'], 404);
        }
        if ($pembeli->poin < $merchandise->harga) {
            DB::rollBack();
            return response()->json(['message' => 'Poin pembeli tidak cukup'], 400);
        }
        $pembeli->poin -= $merchandise->harga;
        $pembeli->save();

        // Create the claim with generated idClaim
        $claim = ClaimMerchandise::create(array_merge($validated, ['idClaim' => $newId]));

        DB::commit();
        return response()->json([
            'message' => 'Claim created successfully, stock and poin updated',
            'data' => $claim
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
    }
}

     * Display the specified claim.
     */
    public function show($id)
    {
        $claim = ClaimMerchandise::with(['pegawai', 'merchandise', 'pembeli'])->find($id);

        if (!$claim) {
            return response()->json(['message' => 'Claim not found'], 404);
        }

        return response()->json($claim);
    }

    /**
     * Update the specified claim.
     */
    public function update(Request $request, $id)
    {
        $claim = ClaimMerchandise::find($id);

        if (!$claim) {
            return response()->json(['message' => 'Claim Merchandise not found'], 404);
        }

        $validated = $request->validate([
            'idPegawai' => 'required|exists:pegawai,idPegawai',
            'tanggalAmbil' => 'required|date',
        ]);

        // Find related Merchandise
        $merchandise = Merchandise::find($claim->idMerchandise);
        if (!$merchandise) {
            return response()->json(['message' => 'Merchandise not found'], 404);
        }

        // Check jumlahSatuan
        if ($merchandise->jumlahSatuan <= 0) {
            return response()->json([
                'message' => 'Cannot update claim. Merchandise stock is already depleted.'
            ], 400);
        }

        // Decrease jumlahSatuan by 1
        $merchandise->jumlahSatuan -= 1;
        $merchandise->save();

        // Update the claim
        $claim->update($validated);

        return response()->json([
            'message' => 'Claim Merchandise updated successfully.',
            'data' => $claim
        ]);
    }

    /**
     * Remove the specified claim.
     */
    public function destroy($id)
    {
        $claim = ClaimMerchandise::find($id);

        if (!$claim) {
            return response()->json(['message' => 'Claim not found'], 404);
        }

        $claim->delete();
        return response()->json(['message' => 'Claim deleted successfully']);
    }
}
