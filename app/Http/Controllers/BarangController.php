<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;  
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiDonasi;


class BarangController extends Controller
{
    // Show all Barang
    public function index()
    {
        try {
        
            $barang = Barang::where('statusBarang', 'tersedia')->get();

            return response()->json($barang);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching the products.',
                'message' => $e->getMessage(),  // Include the exception message for debugging
            ], 500);  // 500 Internal Server Error
        }
    }

    // Show Barang by id
    public function show($idBarang)
{
    try {
        // Try to find the barang by its ID
        $barang = Barang::find( $idBarang);

        // If the barang is found, return it as JSON
        if ($barang) {
            return response()->json($barang);
        } else {
            // If the barang is not found, return a 404 error with a custom message
            return response()->json(['message' => 'Barang not found'], 404);
        }
    } catch (\Exception $e) {
        // Catch any exception and return a 500 internal server error
        return response()->json([
            'error' => 'An error occurred while fetching the product.',
            'message' => $e->getMessage(), // Include the exception message for debugging
        ], 500);
    }
}

    // Create a new Barang
    public function store(Request $request)
{
    // Validate the incoming request
    $validated = $request->validate([
        'idBarang' => 'required|string|max:10',
        'idTransaksiDonasi' => 'nullable|string|max:10',
        'namaBarang' => 'required|string|max:255',
        'beratBarang' => 'required|numeric',
        'garansiBarang' => 'required|boolean',
        'periodeGaransi' => 'nullable|date',
        'hargaBarang' => 'required|numeric',
        'haveHunter' => 'required|boolean',
        'statusBarang' => 'required|string|max:255',
        'kategori' => 'required|string|max:50',
        'image' => 'nullable|image|max:2048', // Optional image upload
    ]);

    // Handle image upload if present
    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('barang_images', 'public');
    }

    // Insert into the Barang table
    $barang = Barang::create($validated);

    return response()->json([
        'status' => true,
        'message' => 'Barang created successfully!',
        'data' => $barang
    ]);
}

    // Update an existing Barang
    public function update(Request $request, $id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json(['message' => 'Barang not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'idTransaksiDonasi' => 'sometimes|required|string|max:255',
            'namaBarang' => 'sometimes|required|string|max:255',
            'beratBarang' => 'sometimes|required|numeric',
            'garansiBarang' => 'sometimes|required|boolean',
            'periodeGaransi' => 'sometimes|required|date',
            'hargaBarang' => 'sometimes|required|numeric',
            'haveHunter' => 'sometimes|required|boolean',
            'statusBarang' => 'sometimes|required|string|max:255',
            'image' => 'nullable|string|max:255',
            'kategori' => 'sometimes|required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $barang->update($request->all());

        return response()->json(['message' => 'Barang updated successfully', 'data' => $barang]);
    }

    // Delete a Barang
    public function destroy($id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json(['message' => 'Barang not found'], 404);
        }

        $barang->delete();

        return response()->json(['message' => 'Barang deleted successfully']);
    }


    public function updateStatus(Request $request, $id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json(['message' => 'Barang not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            
            'statusBarang' => 'sometimes|required|string|max:255',
            
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $barang->update($request->all());

        return response()->json(['message' => 'Barang updated successfully', 'data' => $barang]);
    }

    public function getAvailableBarang()
{
    // Fetch barang where status = 'tersedia' and idBarang not in transaksiDonasi
    $availableBarang = Barang::where('statusBarang', 'tersedia')
                             ->whereNotIn('idBarang', TransaksiDonasi::pluck('idBarang'))
                             ->get(['idBarang', 'namaBarang']);

    return response()->json([
        'status' => true,
        'data' => $availableBarang
    ]);
}
}