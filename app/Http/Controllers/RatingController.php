<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    // Store new rating
    public function store(Request $request)
{
    $idTarget = $request->input('idTarget');
    $idBarang = $request->input('idBarang');
    $idRater = $request->input('idRater');
    $value = $request->input('value');

    // Check if rating with same idBarang and idRater already exists
    $existing = Rating::where('idBarang', $idBarang)
                      ->where('idRater', $idRater)
                      ->first();

    if ($existing) {
        return response()->json([
            'status' => false,
            'message' => 'Rating for this item by this user already exists.'
        ], 409); // 409 Conflict
    }

    $rating = new Rating();
    $rating->idTarget = $idTarget;  // Assuming this is the correct column name
    $rating->idBarang = $idBarang;
    $rating->idRater = $idRater;
    $rating->value = $value;
    $rating->save();

    return response()->json([
        'status' => true,
        'message' => 'Rating saved successfully.'
    ]);
}

public function getAverageRating($idTarget)
{
    // Calculate the average value for the given idTarget
    $average = Rating::where('idTarget', $idTarget)->avg('value');

    // Return the average as JSON (round to 2 decimals, or 0 if no ratings)
    return response()->json([
        'status' => true,
        'idTarget' => $idTarget,
        'averageRating' => $average ? round($average, 2) : 0,
    ]);
}

    // Optional: get ratings for a barang
    public function ratingsForBarang($idBarang)
    {
        $ratings = Rating::where('idBarang', $idBarang)->get();
        return response()->json($ratings);
    }
}
