<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NavBarController extends Controller{

public function CSRouting($page)
{
    // Step 1: Define valid pages with display names
    $pages = [
        'home' => 'Home',
        'penitip' => 'Data Penitip',
        'reward' => 'Reward'
    ];

    // Step 2: Validate the requested page
    if (!array_key_exists($page, $pages)) {
        abort(404); // Return 404 if page is not valid
    }

    $current = $pages[$page]; // e.g., "Data Penitip" for "penitip"

    // Step 3: Build navigation links
    $Halaman = [
        'Home' => route('cs.page', 'home'),
        'Data Penitip' => route('cs.page', 'penitip'),
        'Reward' => route('cs.page', 'reward')
    ];

    // Step 4: Map the page name to the correct view file
    $viewMap = [
        'home' => 'Pegawai.ViewCS.Home',
        'penitip' => 'Pegawai.ViewCS.CrudsPenitip',
        'reward' => 'Pegawai.ViewCS.RewardPage'
    ];

    // Step 5: Return the view with necessary data
    return view($viewMap[$page], compact('Halaman', 'current'));
}


} 
