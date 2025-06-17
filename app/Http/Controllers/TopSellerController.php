<?php

namespace App\Http\Controllers;

use App\Models\TopSeller;
use Illuminate\Http\Request;

class TopSellerController extends Controller
{
    public function index()
    {
         $lastTopSeller = TopSeller::with('penitip') // include penitip relation
        ->orderByDesc('idTopSeller')
        ->first();

        if (!$lastTopSeller) {
            return response()->json([
                'message' => 'Belum ada data Top Seller.'
            ], 404);
        }

        return response()->json([
            'idTopSeller' => $lastTopSeller->idTopSeller,
            'idPenitip' => $lastTopSeller->idPenitip,
            'namaPenitip' => optional($lastTopSeller->penitip)->namaPenitip,
            'nominal' => $lastTopSeller->nominal,
        ]);
    }

public function store(Request $request)
{
    $request->validate([
        'idPenitip' => 'required|string|max:10',
        'nominal' => 'required|numeric',
        'month' => 'required|string|min:1|max:12',
        'year' => 'required|integer|min:2000|max:2100',
    ]);

    $month = $request->month;
    $year = $request->year;

    // Generate idTopSeller in the format "year.month" (e.g., "2025.06")
    $idTopSeller = "{$year}.{$month}";

    // Check if there's already a top seller for the same year and month to avoid duplication
    $existingTopSeller = TopSeller::where('idTopSeller', $idTopSeller)->first();

    if ($existingTopSeller) {
        return response()->json([
            'message' => 'A Top Seller for this month already exists.'
        ], 400);
    }

    // Create a new Top Seller record
    $topSeller = TopSeller::create([
        'idTopSeller' => $idTopSeller,
        'idPenitip' => $request->idPenitip,
        'nominal' => $request->nominal,
    ]);

    return response()->json($topSeller, 201);
}


    // Show a specific TopSeller by id
    public function show($id)
    {
        $topSeller = TopSeller::find($id);

        if (!$topSeller) {
            return response()->json(['message' => 'TopSeller not found'], 404);
        }

        return response()->json($topSeller);
    }

    // Update a TopSeller record
    public function update(Request $request, $id)
    {
        $topSeller = TopSeller::find($id);

        if (!$topSeller) {
            return response()->json(['message' => 'TopSeller not found'], 404);
        }

        $request->validate([
            'idTopSeller' => 'nullable|string|max:10',
            'idPenitip' => 'nullable|string|max:10',
            'nominal' => 'nullable|numeric',
        ]);

        $topSeller->update($request->only(['idTopSeller', 'idPenitip', 'nominal']));

        return response()->json($topSeller);
    }

    // Delete a TopSeller record
    public function destroy($id)
    {
        $topSeller = TopSeller::find($id);

        if (!$topSeller) {
            return response()->json(['message' => 'TopSeller not found'], 404);
        }

        $topSeller->delete();

        return response()->json(['message' => 'TopSeller deleted successfully']);
    }
    public function getTopSellerById($idTopSeller)
{
    // Validate the request to ensure idTopSeller is provided
 
    // Fetch the TopSeller with related Penitip data
    $topSeller = TopSeller::with('penitip') // Assuming TopSeller has a relation to Penitip
        ->where('idTopSeller', $idTopSeller)
        ->first();

    if (!$topSeller) {
        return response()->json([
            'message' => 'Top Seller not found.'
        ], 404);
    }

    // Return the relevant data in the response
    return response()->json([
        'idTopSeller' => $topSeller->idTopSeller,
        'idPenitip' => $topSeller->idPenitip,
        'namaPenitip' => optional($topSeller->penitip)->namaPenitip,
        'nominal' => $topSeller->nominal,
    ]);
}
}
