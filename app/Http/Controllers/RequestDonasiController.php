<?php

namespace App\Http\Controllers;

use App\Models\RequestDonasi;
use App\Models\TransaksiDonasi;
use Exception;
use Illuminate\Http\Request;

class RequestDonasiController extends Controller
{
    // Show all request donasi
    public function index(){
        try{
            $data=RequestDonasi::with(['Organisasi','TransaksiDonasi'])->get();
            return response()->json([
                "status" => true,
                "message" => "Get successful",
                "data" => $data
            ], 200);
        }catch(Exception $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 400);
        }
    }
//     public function index()
// {
//     // Eager load the 'organisasi' relationship
//     $requestDonasis = RequestDonasi::with('organisasi')->get();

//     // Add 'namaOrganisasi' to each item
//     $result = $requestDonasis->map(function($item) {
//         // Safely handle missing 'organisasi' data
//         $item->namaOrganisasi = $item->organisasi ? $item->organisasi->namaOrganisasi : 'Unknown';
//         return $item;
//     });

//     return response()->json([
//         'status' => true,
//         'data' => $result
//     ]);
// }

    // Show a specific request donasi
    public function show($id)
    {
        $request = RequestDonasi::find($id);

        if (!$request) {
            return response()->json([
                'status' => false,
                'message' => 'Request Donasi not found'
            ], 404);
        }

        return response()->json($request);
    }

    // Store a new request donasi
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idTransaksiDonasi' => 'required|string|max:10',
            'idOrganisasi' => 'required|string|max:10',
            'barangRequest' => 'required|string|max:255',
            'tanggalRequest' => 'required|date',
            'status' => 'required|string|max:255',
        ]);

        $requestDonasi = RequestDonasi::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Request Donasi created successfully',
            'data' => $requestDonasi
        ], 201);
    }

    // Update an existing request donasi
    public function update(Request $request, $id)
    {
        $requestDonasi = RequestDonasi::find($id);

        if (!$requestDonasi) {
            return response()->json([
                'status' => false,
                'message' => 'Request Donasi not found'
            ], 404);
        }

        // Validate and update data
        $validated = $request->validate([
            'idTransaksiDonasi' => 'required|string|max:10',
            'idOrganisasi' => 'required|string|max:10',
            'barangRequest' => 'required|string|max:255',
            'tanggalRequest' => 'required|date',
            'status' => 'required|string|max:255',
        ]);

        $requestDonasi->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Request Donasi updated successfully',
            'data' => $requestDonasi
        ], 200);
    }

    // Delete a request donasi
    public function destroy($id)
    {
        $requestDonasi = RequestDonasi::find($id);

        if (!$requestDonasi) {
            return response()->json([
                'status' => false,
                'message' => 'Request Donasi not found'
            ], 404);
        }

        $requestDonasi->delete();

        return response()->json([
            'status' => true,
            'message' => 'Request Donasi deleted successfully'
        ], 200);
    }
}
