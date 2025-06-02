<?php

namespace App\Http\Controllers;

use App\Models\PointRedemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PointRedemptionController extends Controller
{
    // List all point redemptions
    public function index()
    {
        $redemptions = PointRedemption::with('pembeli')->get();

        return response()->json([
            'status' => true,
            'data' => $redemptions,
        ]);
    }   

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idPembeli' => 'required|string|exists:pembeli,idPembeli',
            'points_used' => 'required|integer|min:1',
            'transaction_id' => 'required|string|unique:point_redemptions,transaction_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $redemption = PointRedemption::create([
            'idPembeli' => $request->idPembeli,
            'points_used' => $request->points_used,
            'transaction_id' => $request->transaction_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Point redemption recorded successfully.',
            'data' => $redemption,
        ]);
    }

    // Delete a point redemption by ID
    public function destroy($id)
    {
        $redemption = PointRedemption::find($id);

        if (!$redemption) {
            return response()->json([
                'status' => false,
                'message' => 'Point redemption not found.',
            ], 404);
        }

        $redemption->delete();

        return response()->json([
            'status' => true,
            'message' => 'Point redemption deleted successfully.',
        ]);
    }
}
