<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenitipController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('penitip/login', [PenitipController::class, 'login']);
Route::get('/penitip', [PenitipController::class, 'getAllPenitip']);
Route::post('/penitip/register', [PenitipController::class, 'register']);

Route::get('/dompet', [DompetController::class, 'getAllDompet']); 
Route::get('/dompet/{id}', [DompetController::class, 'getDompetById']);
Route::post('/dompet', [DompetController::class, 'createDompet']);
Route::put('/dompet/{id}', [DompetController::class, 'updateDompet']);
Route::delete('/dompet/{id}', [DompetController::class, 'deleteDompet']);


