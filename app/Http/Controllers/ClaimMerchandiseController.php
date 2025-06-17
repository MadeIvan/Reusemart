<?php

namespace App\Http\Controllers;

use App\Models\ClaimMerchandise;
use Illuminate\Http\Request;
use App\Models\Merchandise;
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
            'idClaim' => 'required|string|max:10|unique:claim,idClaim',
            'idPegawai' => 'required|exists:pegawai,idPegawai',
            'idMerchandise' => 'required|exists:merchandise,idMerchandise',
            'idPembeli' => 'required|exists:pembeli,idPembeli',
            'tanggalAmbil' => 'nullable|date',
        ]);

        $claim = ClaimMerchandise::create($validated);
        return response()->json([
            'message' => 'Claim created successfully',
            'data' => $claim
        ], 201);
    }

    /**
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
