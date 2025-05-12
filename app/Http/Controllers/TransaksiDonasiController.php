<?php

namespace App\Http\Controllers;

use App\Models\TransaksiDonasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\RequestDonasi;

class TransaksiDonasiController extends Controller
{
    /**
     * Store a newly created TransaksiDonasi in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    // Validate the incoming request data
    $validated = $request->validate([
        'idBarang' => 'required|string|max:10',
        'idRequest' => 'required|string|max:10',
        'namaPenerima' => 'required|string|max:255',
        'statusDonasi' => 'required|string|max:255',  // Optional: If needed
        'tanggalDonasi' => 'required|date',
    ]);

    // Generate idTransaksiDonasi as 'TD' + last number + 1
    try {
        // Fetch the last record and extract the numeric part
        $lastTransaksi = TransaksiDonasi::latest('idTransaksiDonasi')->first();

        // Check if there are any records and get the number part from the last one
        $lastNumber = 0;
        if ($lastTransaksi) {
            $lastNumber = (int) str_replace('TD', '', $lastTransaksi->idTransaksiDonasi);  // Remove 'TD' and get the numeric part
        }

        // Increment the number part by 1
        $newNumber = $lastNumber + 1;

        // Generate the new idTransaksiDonasi (TD + incremented number)
        $newIdTransaksiDonasi = 'TD' . $newNumber;

        // Check if the new ID already exists, and increment if necessary
        while (TransaksiDonasi::where('idTransaksiDonasi', $newIdTransaksiDonasi)->exists()) {
            $lastNumber++;  // Increment the last number
            $newIdTransaksiDonasi = 'TD' . $lastNumber;  // Update ID with the new incremented value
        }

        // Create the new TransaksiDonasi record
        $transaksiDonasi = TransaksiDonasi::create([
            'idTransaksiDonasi' => $newIdTransaksiDonasi,
            'idBarang' => $validated['idBarang'],
            'idRequest' => $validated['idRequest'],
            'namaPenerima' => $validated['namaPenerima'],
            'status' => 'Diterima',  // Set statusDonasi to "Diterima" by default
            'tanggalDonasi' => $validated['tanggalDonasi'],
        ]);

        // Now update the related RequestDonasi status to "Diterima"
        $requestDonasi = RequestDonasi::find($validated['idRequest']);
        
        if ($requestDonasi) {
            $requestDonasi->status = 'Diterima';  // Set the status to "Diterima"
            $requestDonasi->save();  // Save the updated RequestDonasi
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Related RequestDonasi not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Transaksi Donasi created successfully and RequestDonasi updated',
            'data' => $transaksiDonasi
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error occurred: ' . $e->getMessage(),
        ], 500);
    }
}



    /**
     * Display a listing of TransaksiDonasi.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaksiDonasis = TransaksiDonasi::all();

        return response()->json([
            'status' => true,
            'data' => $transaksiDonasis
        ]);
    }

    /**
     * Display a specific TransaksiDonasi by its ID.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaksiDonasi = TransaksiDonasi::find($id);

        if (!$transaksiDonasi) {
            return response()->json([
                'status' => false,
                'message' => 'Transaksi Donasi not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $transaksiDonasi
        ]);
    }

    /**
     * Update an existing TransaksiDonasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Find the existing TransaksiDonasi record
        $transaksiDonasi = TransaksiDonasi::find($id);

        if (!$transaksiDonasi) {
            return response()->json([
                'status' => false,
                'message' => 'Transaksi Donasi not found'
            ], 404);
        }

        // Validate the incoming request data
        $validated = $request->validate([
            'idBarang' => 'required|string|max:10',
            'idRequest' => 'required|string|max:10',
            'namaPenerima' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'tanggalDonasi' => 'required|date',
        ]);

        // Update the TransaksiDonasi record
        $transaksiDonasi->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Transaksi Donasi updated successfully',
            'data' => $transaksiDonasi
        ]);
    }

    /**
     * Remove a TransaksiDonasi record.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transaksiDonasi = TransaksiDonasi::find($id);

        if (!$transaksiDonasi) {
            return response()->json([
                'status' => false,
                'message' => 'Transaksi Donasi not found'
            ], 404);
        }

        $transaksiDonasi->delete();

        return response()->json([
            'status' => true,
            'message' => 'Transaksi Donasi deleted successfully'
        ]);
    }
}
