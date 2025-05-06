<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;  
use Illuminate\Support\Facades\Validator;


class BarangController extends Controller
{
    // Show all Barang
    public function index()
{
    try {
    
        $barang = Barang::all();

        return response()->json($barang);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'An error occurred while fetching the products.',
            'message' => $e->getMessage(),  // Include the exception message for debugging
        ], 500);  // 500 Internal Server Error
    }
}

    // Show Barang by id
    public function show($id)
    {
        $barang = Barang::find($id);
        
        if ($barang) {
            return response()->json($barang);
        } else {
            return response()->json(['message' => 'Barang not found'], 404);
        }
    }

    // Create a new Barang
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idBarang'=> 'required|string|max:10',
            'idTransaksiDonasi' => 'required|string|max:255',
            'namaBarang' => 'required|string|max:255',
            'beratBarang' => 'required|numeric',
            'garansiBarang' => 'required|boolean',
            'periodeGaransi' => 'required|date',
            'hargaBarang' => 'required|numeric',
            'haveHunter' => 'required|boolean',
            'statusBarang' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
            'kategori' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $barang = Barang::create([
            'idBarang' => $request->idBarang,
            'idTransaksiDonasi' => $request->idTransaksiDonasi,
            'namaBarang' => $request->namaBarang,
            'beratBarang' => $request->beratBarang,
            'garansiBarang' => $request->garansiBarang,
            'periodeGaransi' => $request->periodeGaransi,
            'hargaBarang' => $request->hargaBarang,
            'haveHunter' => $request->haveHunter,
            'statusBarang' => $request->statusBarang,
            'image' => $request->image,
            'kategori' => $request->kategori,
        ]);

        return response()->json(['message' => 'Barang created successfully', 'data' => $barang], 201);
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
}