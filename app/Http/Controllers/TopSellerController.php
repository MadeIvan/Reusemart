<?php

namespace App\Http\Controllers;

use App\Models\TopSeller;
use Illuminate\Http\Request;

class TopSellerController extends Controller
{
    // Fetch all TopSeller records
    public function index()
    {
        $topSellers = TopSeller::with('penitip')->get();
        return response()->json($topSellers);
    }

    // Create a new TopSeller record
    public function store(Request $request)
    {
        $request->validate([
            'idTopSeller' => 'required|string|max:10',
            'idPenitip' => 'required|string|max:10',
            'nominal' => 'required|numeric',
        ]);

        $topSeller = TopSeller::create([
            'idTopSeller' => $request->idTopSeller,
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
}
