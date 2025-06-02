<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;
use Nette\Schema\ValidationException;
use Carbon\Carbon;
use App\Models\Pegawai;
use App\Models\Pembeli;
use App\Models\Barang;
use App\Models\Alamat;

class PegawaiController extends Controller
{
    // === [PegawaiController Methods] ===

    public function index() //show
    {
        // $pegawai = Pegawai::with('jabatan')->get();
        try{
            $data = Pegawai::with('jabatan')->get();
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

    public function show($id){ //search
        try{
            $data = Pegawai::find($id);
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

    public function register(Request $request){ //register pegawai
        $request->validate([
            'idJabatan'=> 'required|string',
            // 'idDompet'=> 'string|max:255',
            'namaPegawai'=> 'required|string|max:255',
            'tanggalLahir'=> 'required|date',
            'username'=> 'required|string|max:255',
            'password'=> 'required|string|max:255',
        ]);

        /////////////////GENERATE ID PEGAWAI////////////////////////
        $last = Pegawai::orderBy('idPegawai', 'desc')->first();
        $lastNumber = 0;

        if ($last) {
            $lastNumber = (int) str_replace('P', '', $last->idPegawai);
        }
        $newId = 'P' . ($lastNumber + 1);

        while (Pegawai::where('idPegawai', $newId)->withTrashed()->exists()) {
            $lastNumber++;  // Increment angka ID
            $newId = 'P' . $lastNumber;  // Update ID baru
        }

        /////////////////GENERATE ID DOMPET////////////////////////
        $dompet = (new DompetController)->createDompetPenitip(null);
        $idDompet = (string) $dompet->idDompet;

        // \Log::info("Created new dompet with ID: {$idDompet}");

        /////////////////CREATE PEGAWAI////////////////////////
        $pegawai = Pegawai::create([
            'idPegawai' => $newId,
            'idJabatan'=> $request->idJabatan,
            'idDompet'=> $idDompet,
            'namaPegawai'=> $request->namaPegawai,
            'tanggalLahir'=> $request->tanggalLahir,
            'username'=> $request->username,
            'password'=> Hash::make($request->password),
        ]);

        return response()->json([
            "status" => true,
            "message" => "Create successful",
            "data" => $pegawai
        ], 200);
    }

    public function login(Request $request){
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);
        // Include soft-deleted users in the query
        $pegawai = Pegawai::withTrashed()
            ->whereRaw('BINARY username = ?', [$request->username])
            ->first();

        if ($pegawai) {
            if ($pegawai->deleted_at !== null) {
                return response()->json([
                    "status" => false,
                    "message" => "Your account has been deactivated.",
                ], 403);
            }
            if (Hash::check($request->password, $pegawai->password)) {
                $token = $pegawai->createToken('Personal Access Token')->plainTextToken;

                return response()->json([
                    "status" => true,
                    "message" => "Login successful",
                    "data" => [
                        "pegawai" => $pegawai,
                        "token" => $token,
                    ],
                    
                ], 200);
            }
        }
        // If user not found or password incorrect
        return response()->json([
            "status" => false,
            "message" => "Invalid credentials",
        ], 401);
    }

    public function update(Request $request, $id)
    {
        Log::info("Attempting to update Pegawai with ID: $id");

        $pegawai = Pegawai::find($id);

        if (!$pegawai) {
            Log::warning("Pegawai not found for ID: $id");
            return response()->json([
                "status" => false,
                "message" => "Pegawai not found",
                "data" => null
            ], 404);
        }

        Log::info("Request data received:", $request->all());

        try {
            $validatedData = $request->validate([

                'namaPegawai' => 'required|string',
                'username' => 'required|string',
            ]);

            Log::info("ValidatedData:", $validatedData);

            $pegawai->update($validatedData);

            Log::info("Pegawai updated successfully: ID $id");

            return response()->json([
                "status" => true,
                "message" => "Update successful",
                "data" => $pegawai,
            ], 200);

        } catch (ValidationException $e) {
            // Log::error("Validation failed:", $e->errors());

            return response()->json([
                "status" => false,
                "message" => "Validation failed",
                // "errors" => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error("Unexpected error on updating Pegawai ID $id: " . $e->getMessage());

            return response()->json([
                "status" => false,
                "message" => "Something went wrong",
                "error" => $e->getMessage(),
            ], 400);
        }
    }

    public function softDelete($id)
    {
        $pegawai = Pegawai::find($id);

        if (!$pegawai) {
            return response()->json([
                'status' => false,
                'message' => 'Pegawai not found.'
            ], 404);
        }

        // Assign current date to deleted_at
        $pegawai->deleted_at = Carbon::now();
        $pegawai->save();

        return response()->json([
            'status' => true,
            'message' => 'Pegawai soft deleted successfully.'
        ], 200);
    }

    public function destroy($id){ //delete
        // $data = Auth::user();
        $data = Pegawai::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'You are not logged in',
            ], 403);
        }
        
        $data->delete();

        return response()->json([
            'message' => 'Customer deleted successfully',
        ]);
    }

    function resetPassword($id){
        $pegawai = Pegawai::find($id);

        $pegawai->password =  Hash::make($pegawai->tanggalLahir);
        $pegawai->update();

        return response()->json([
            'message' => 'Berhasil',
        ]);
    }

    // === [PembeliController Methods] ===

    public function registerPembeli(Request $request){ //register pembeli
        $request->validate([
            'username' => 'required|string|max:255',
            'password'=> 'required|string|min:8',
            'namaPembeli'=> 'required|string|max:255',
            'email'=> 'required|string|max:255',
        ]);
        $last = Pembeli::orderBy('idPembeli', 'desc')->first();
        $lastNumber = 0;

        if ($last) {
            $lastNumber = (int) str_replace('B', '', $last->idPembeli);
        }

        $newId = 'B' . ($lastNumber + 1);

        while (Pembeli::where('idPembeli', $newId)->withTrashed()->exists()) {
            $lastNumber++;
            $newId = 'B' . $lastNumber;
        }

        $pembeli = Pembeli::create([
            'idPembeli' => $newId,
            'username' => $request->username,
            'password'=> Hash::make($request->password),
            'poin' => 0,
            'namaPembeli'=> $request->namaPembeli,
            'email'=> $request->email
        ]);

        return response()->json([
            "status" => true,
            "message" => "Get successful",
            "data" => $pembeli
        ], 200);
    }

    public function loginPembeli(Request $request){
        $request->validate([
            'username' => 'required|string|max:255',
            'password'=> 'required|string|min:8',
        ]);

        $pembeli = Pembeli::where('username', $request->username)->first();

        if (!$pembeli) {
            return response()->json([
                "status" => false,
                "message" => "User not found",
            ], 404);
        }

        if ($pembeli->deleted_at) {
            return response()->json([
                "status" => false,
                "message" => "Your account has been deactivated.",
            ], 403);
        }

        if (!Hash::check($request->password, $pembeli->password)) {
            return response()->json([
                "status" => false,
                "message" => "Invalid credentials",
            ], 401);
        }

        // Login success
        $token = $pembeli->createToken('Personal Access Token',['pembeli'])->plainTextToken;

        return response()->json([
            "status" => true,
            "message" => "Login successful",
            "data" => [
                "pembeli" => $pembeli,
                "token" => $token
            ]
        ], 200);
    }

    public function logout(Request $request){
        if (Auth::check()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }
        return response()->json(['message' => 'Not logged in'], 401);
    }

    public function checkEmailUsername(Request $request)
    {
        $email = $request->query('email');
        $username = $request->query('username');

        $emailExists = Pembeli::where('email', $email)->exists();
        $usernameExists = Pembeli::where('username', $username)->exists();

        return response()->json([
            'emailExists' => $emailExists,
            'usernameExists' => $usernameExists
        ]);
    }

    public function addToCart(Request $request, $id) {
        $pembeli = Auth::guard('pembeli')->user();
        if(!$pembeli){
            return response()->json([
                "status" => false,
                "message" => "Pembeli Belum Login",
                "data" => null,
            ], 400);
        }

        $cartKey = 'cart_user_' . $pembeli->idPembeli;
        $cart = Cache::get($cartKey, []); 

        // Cek apakah barang sudah ada di cart
        if(!isset($cart[$id])) {
            $cart[$id] = [
                'idBarang' => $id,
                'jumlah' => 1
            ];
        }else {
            return response()->json([
                "status" => false,
                "message" => "Barang sudah ada di keranjang",
                "data" => $cart
            ], 409); // conflict
        }

        Cache::forever($cartKey, $cart);

        return response()->json([
            "status" => true,
            "message" => "Get successful",
            "data" => $cart
        ], 200);
    }

    public function getCart(Request $request)
    {
        $user = Auth::guard('pembeli')->user();
        $cart = Cache::get('cart_user_' . $user->idPembeli, []);

        $detailedCart = [];

        foreach ($cart as $item) {
            $barang = Barang::select(
                'idBarang',
                'namaBarang',
                'beratBarang',
                'garansiBarang',
                'periodeGaransi',
                'hargaBarang',
                'haveHunter',
                'statusBarang',
                'image',
                'kategori'
            )->find($item['idBarang']);

            if ($barang) {
                $detailedCart[] = [
                    ...$barang->toArray(),
                    'jumlah' => $item['jumlah'],
                ];
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Get cart with detail successful',
            'data' => $detailedCart,
            'count' => count($detailedCart)
        ]);
    }

    public function removeFromCart(Request $request, $id)
    {
        $pembeli = Auth::guard('pembeli')->user();
        if (!$pembeli) {
            return response()->json([
                "status" => false,
                "message" => "Pembeli belum login",
            ], 401);
        }

        $cartKey = 'cart_user_' . $pembeli->idPembeli;
        $cart = Cache::get($cartKey, []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            Cache::forever($cartKey, $cart);

            return response()->json([
                "status" => true,
                "message" => "Barang berhasil dihapus dari keranjang",
            ], 200);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Barang tidak ditemukan di keranjang",
            ], 404);
        }
    }
    public function showKurir(){{
        $kurir = Pegawai::whereHas('jabatan', function($query) {
            $query->where('idJabatan', '4');
        })->get();
        return response()->json([
            "status" => true,
            "message" => "Get successful",
            "data" => $kurir
        ], 200);
    }}
}
