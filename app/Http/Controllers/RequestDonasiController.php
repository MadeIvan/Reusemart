<?php

namespace App\Http\Controllers;

use App\Models\ReqDonasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
class RequestDonasiController extends Controller
{
    // Middleware to ensure that the user is authenticated
    public function __construct()
    {
        $this->middleware('auth:organisasi')->except('request', 'notaReqPdf'); // Apply authentication to all actions
    }

    // Fetch all ReqDonasi data for authenticated user
    public function index(Request $request)
    {
        // \Log::info('Bearer Token:', ['token' => $request->bearerToken()]);

        $user = Auth::user();
        // \Log::info('Authenticated User:', ['user' => $user]);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Fetch the ReqDonasi data that belongs to the authenticated user’s idOrganisasi
        $reqDonasi = ReqDonasi::where('idOrganisasi', $user->idOrganisasi)->get();

        return response()->json([
            'status' => 'success',
            'data' => $reqDonasi
        ]);
    }

    // Store a new ReqDonasi entry
   public function store(Request $request)
{
    // Ensure the user is authenticated
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthenticated'
        ], 401);
    }

    // Validate the incoming request data
    $validated = $request->validate([
        'barangRequest' => 'required|string', // Only 'barangRequest' is required from the user
    ]);

    // Generate idRequest based on the largest idRequest in the table
    $lastId = ReqDonasi::orderByDesc('idRequest')->first(); // Get the record with the largest idRequest
    $lastIdNumber = $lastId ? (int) $lastId->idRequest : 0; // Extract the numeric part and convert to integer
    $newId = $lastIdNumber + 1; // Increment the numeric part by 1

    // Check if the new ID is already taken
    while (ReqDonasi::where('idRequest', $newId)->exists()) {
        $newId++; // Increment until we find a unique ID
    }

    // Instantiate a new ReqDonasi model
    $reqDonasi = new ReqDonasi();

    // Set the attributes with default values
    $reqDonasi->idRequest = $newId;  // Set the newly generated idRequest (as a number)
    $reqDonasi->idTransaksiDonasi = null;  // idTransaksiDonasi is set to null
    $reqDonasi->idOrganisasi = $user->idOrganisasi;  // Set idOrganisasi from the authenticated user
    $reqDonasi->tanggalRequest = now();  // Set the current timestamp as tanggalRequest
    $reqDonasi->status = 'Pending';  // Set status to 'Pending'

    // Set the 'barangRequest' field from the validated input
    $reqDonasi->barangRequest = $validated['barangRequest'];

    // Save the new ReqDonasi
    $reqDonasi->save();

    // Return a success response
    return response()->json([
        'status' => 'success',
        'message' => 'Request for donation successfully created!',
        'data' => $reqDonasi
    ], 201);
}

    // Update the 'barangRequest' field only
    public function update(Request $request, $id)
    {
        try {
            // Ensure the user is authenticated
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Validate input
            $validated = $request->validate([
                'barangRequest' => 'required|string',
            ]);

            // Find the ReqDonasi record by its ID
            $reqDonasi = ReqDonasi::find($id);

            if (!$reqDonasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ReqDonasi not found'
                ], 404);
            }

            // Ensure that the authenticated user is allowed to update this ReqDonasi
            if ($reqDonasi->idOrganisasi !== $user->idOrganisasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to update this request'
                ], 403);
            }

            // Update only the barangRequest field
            $reqDonasi->barangRequest = $validated['barangRequest'];
            $reqDonasi->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Request for donation updated successfully!',
                'data' => $reqDonasi
            ]);
        } catch (\Exception $e) {
            // Log::error('Error updating request donasi: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update request: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete the ReqDonasi entry
    public function destroy($id)
    {
        try {
            // Ensure the user is authenticated
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Find the ReqDonasi record by its ID
            $reqDonasi = ReqDonasi::find($id);

            if (!$reqDonasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ReqDonasi not found'
                ], 404);
            }

            // Ensure that the authenticated user is allowed to delete this ReqDonasi
            if ($reqDonasi->idOrganisasi !== $user->idOrganisasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to delete this request'
                ], 403);
            }

            // Delete the record
            $reqDonasi->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Request for donation deleted successfully!'
            ]);
        } catch (\Exception $e) {
            // Log::error('Error deleting request donasi: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete request: ' . $e->getMessage()
            ], 500);
        }
    }
    public function request(){
        $requestDonasi = ReqDonasi::with([
            'transaksiDonasi.barang',
            'organisasi',
        ])
        ->where('status', 'Pending')
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $requestDonasi
        ]);
    }


    public function notaReqPdf()
    {
        $reqDonasi = ReqDonasi::with([
            'transaksiDonasi.barang',
            'organisasi',
        ])->where('status', 'Pending')->get();

        return Pdf::loadView('nota.pdf.laporanRequestDonasi', compact('reqDonasi'))
            ->setPaper('a4', 'landscape')
            ->stream("Laporan Request Donasi.pdf");
    }
    
}