<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;  
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiDonasi;
use Illuminate\Support\Facades\DB;
use App\Models\ImagesBarang;


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



    public function indexall()
{
    try {
        $barang = Barang::with('detailTransaksiPenitipan.transaksiPenitipan.penitip','imagesBarang')->get();

        $result = $barang->map(function($item) {
            // Get transaksiPenitipan instance safely
            $transaksi = optional($item->detailTransaksiPenitipan)->transaksiPenitipan;

            return [
                'idBarang' => $item->idBarang,
                'namaBarang' => $item->namaBarang,
                'beratBarang' => $item->beratBarang,
                'garansiBarang' => $item->garansiBarang,
                'periodeGaransi' => $item->periodeGaransi,
                'hargaBarang' => $item->hargaBarang,
                'haveHunter' => $item->haveHunter,
                'statusBarang' => $item->statusBarang,
                'image' => $item->image,
                'kategori' => $item->kategori,
                'tanggalPenitipanSelesai' => optional($transaksi)->tanggalPenitipanSelesai,
                'namaPenitip' => optional(optional($transaksi)->penitip)->namaPenitip,

                // Include all transaksiPenitipan attributes as a nested array
                'transaksiPenitipan' => $transaksi ? $transaksi->toArray() : null,
                    'imagesBarang' => $item->imagesBarang ? [
                    'image1' => $item->imagesBarang->image1,
                    'image2' => $item->imagesBarang->image2,
                    'image3' => $item->imagesBarang->image3,
                    'image4' => $item->imagesBarang->image4,
                    'image5' => $item->imagesBarang->image5,
                ] : null,
                
            ];
        });

        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'An error occurred while fetching the products.',
            'message' => $e->getMessage(),
            'status' => true
        ], 500);
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
    ]);

    $barang = Barang::create([
        'idBarang' => $validated['idBarang'],
        'namaBarang' => $validated['namaBarang'],
        'beratBarang' => $validated['beratBarang'],
        'garansiBarang' => $validated['garansiBarang'],
        'periodeGaransi' => $validated['periodeGaransi'],
        'hargaBarang' => $validated['hargaBarang'],
        'haveHunter' => $validated['haveHunter'],
        'statusBarang' => $validated['statusBarang'],
        'kategori' => $validated['kategori'],
        'image' => null, // no related imagesBarang yet
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Barang created successfully',
        'data' => $barang
    ], 201);
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

public function generateIdBarang(Request $request)
{
    $prefix = $request->query('prefix');

    if (!$prefix) {
        return response()->json(['error' => 'Prefix is required'], 400);
    }

    // Fetch all existing idBarang starting with the prefix
    $existingIds = Barang::where('idBarang', 'like', $prefix . '%')->pluck('idBarang');

    // Extract numeric suffixes from existing IDs
    $numbers = $existingIds->map(function ($id) use ($prefix) {
        return (int) substr($id, strlen($prefix));
    })
    ->filter(function ($num) {
        return $num > 0; // only positive integers
    })
    ->sort()
    ->values();

    // Find the smallest missing positive integer suffix
    $nextNumber = 1;
    foreach ($numbers as $num) {
        if ($num == $nextNumber) {
            $nextNumber++;
        } elseif ($num > $nextNumber) {
            // Found a gap, stop incrementing
            break;
        }
    }

    $nextId = $prefix . $nextNumber;

    return response()->json(['nextId' => $nextId]);
}


}