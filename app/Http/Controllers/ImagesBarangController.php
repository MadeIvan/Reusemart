<?php

namespace App\Http\Controllers;

use App\Models\ImagesBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Barang;

class ImagesBarangController extends Controller
{
    // List all imagesbarang
    public function index()
    {
        $images = ImagesBarang::all();
        return response()->json($images);
    }

    // Show single imagesbarang
    public function show($id)
    {
        $image = ImagesBarang::find($id);
        if (!$image) {
            return response()->json(['message' => 'ImagesBarang not found'], 404);
        }
        return response()->json($image);
    }

    // Store new imagesbarang
   public function store(Request $request)
{
    $data = $request->validate([
        'id' => 'required|string|max:10',
        'image1' => 'nullable|string|max:255',
        'image2' => 'nullable|string|max:255',
        'image3' => 'nullable|string|max:255',
        'image4' => 'nullable|string|max:255',
        'image5' => 'nullable|string|max:255',
    ]);

    // Create ImagesBarang record
    $imagesBarang = ImagesBarang::create($data);

    // Find the Barang using the id (assuming 'id' matches 'idBarang')
    $barang = Barang::find($data['id']); 
    if ($barang) {
        $barang->image = $imagesBarang->id; // Set FK to imagesBarang's PK
        $barang->save();
    } else {
        // Optional: handle barang not found, e.g. return error
        return response()->json([
            'status' => false,
            'message' => 'Barang not found with id ' . $data['id']
        ], 404);
    }

    return response()->json([
        'status' => true,
        'message' => 'ImagesBarang created and linked successfully',
        'data' => $imagesBarang
    ], 201);
}


    // Update imagesbarang
    public function update(Request $request, $id)
    {
        $image = ImagesBarang::find($id);
        if (!$image) {
            return response()->json(['message' => 'ImagesBarang not found'], 404);
        }

        $validated = $request->validate([
            'images1' => 'nullable|string',
            'images2' => 'nullable|string',
            'images3' => 'nullable|string',
            'images4' => 'nullable|string',
            'images5' => 'nullable|string',
        ]);

        $image->update($validated);
        return response()->json($image);
    }

    // Delete imagesbarang
    public function destroy($id)
    {
        $image = ImagesBarang::find($id);
        if (!$image) {
            return response()->json(['message' => 'ImagesBarang not found'], 404);
        }

        $image->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}