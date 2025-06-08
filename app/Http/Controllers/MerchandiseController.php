<?php

namespace App\Http\Controllers;

use App\Models\Merchandise;
use Illuminate\Http\Request;

class MerchandiseController extends Controller
{
    /**
     * Display a listing of the merchandise.
     */
    public function index()
    {
        $merchandise = Merchandise::all();
        return response()->json($merchandise);
    }

    /**
     * Store a newly created merchandise.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idMerchandise' => 'required|string|max:10|unique:merchandise,idMerchandise',
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'jumlahSatuan' => 'required|integer|min:0',
        ]);

        $merchandise = Merchandise::create($validated);

        return response()->json([
            'message' => 'Merchandise created successfully.',
            'data' => $merchandise
        ], 201);
    }

    /**
     * Display the specified merchandise.
     */
    public function show($id)
    {
        $merchandise = Merchandise::find($id);

        if (!$merchandise) {
            return response()->json(['message' => 'Merchandise not found.'], 404);
        }

        return response()->json($merchandise);
    }

    /**
     * Update the specified merchandise.
     */
    public function update(Request $request, $id)
    {
        $merchandise = Merchandise::find($id);

        if (!$merchandise) {
            return response()->json(['message' => 'Merchandise not found.'], 404);
        }

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'harga' => 'sometimes|required|integer|min:0',
            'jumlahSatuan' => 'sometimes|required|integer|min:0',
        ]);

        $merchandise->update($validated);

        return response()->json([
            'message' => 'Merchandise updated successfully.',
            'data' => $merchandise
        ]);
    }

    /**
     * Remove the specified merchandise.
     */
    public function destroy($id)
    {
        $merchandise = Merchandise::find($id);

        if (!$merchandise) {
            return response()->json(['message' => 'Merchandise not found.'], 404);
        }

        $merchandise->delete();

        return response()->json(['message' => 'Merchandise deleted successfully.']);
    }
}
