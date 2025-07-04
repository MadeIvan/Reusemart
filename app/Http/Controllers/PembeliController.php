<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\Pembeli;
use App\Models\Barang;
use App\Models\Alamat;
use App\Models\TransaksiPembelian;
use App\Models\DetailTransaksiPenitipan;

class PembeliController extends Controller
{
    public function register(Request $request){ //register
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

    public function login(Request $request){
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

    public function checkEmailUsername(Request $request)
    {
        $email = $request->query('email');
        $username = $request->query('username');

        $emailExists = Pembeli::where('email', $email)->exists();
        $usernameExists = Pembeli::where('username', $username)->exists();

        // $emailExists = Pembeli::where('email', $request->email)->exists();
        // $emailExists = Pembeli::where('email', $email)->exists();
        // $usernameExists = Pembeli::where('username', $request->username)->exists();
        // $usernameExists = Pembeli::where('username', $username)->exists();

        return response()->json([
            'emailExists' => $emailExists,
            'usernameExists' => $usernameExists
        ]);
    }


    public function logout (Request $request){
        if (Auth::check()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }
    
        return response()->json(['message' => 'Not logged in'], 401);
    }

    public function getData(Request $request)    
    {
        // $penitip = $request->user();
        $pembeli = auth('pembeli')->user();
        
        return response()->json([
            "status" => true,
            "message" => "User retrieved successfully",
            "data" => $pembeli
        ]);
    }

    public function updatePoin(Request $request)    
    {
        $pembeli = auth('pembeli')->user();
        $pembeli->poin = $request->poin;
        $pembeli->save();
        
        return response()->json([
            "status" => true,
            "message" => "User retrieved successfully",
            "data" => $pembeli
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
                $detailedCart[] = array_merge(
                    $barang->toArray(),
                    ['jumlah' => $item['jumlah']]
                );


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
    // Remove all items from the cart
    public function removeAllCart(Request $request)
    {
        $pembeli = Auth::guard('pembeli')->user();
        if (!$pembeli) {
            return response()->json([
                "status" => false,
                "message" => "Pembeli belum login",
            ], 401);
        }
        $cartKey = 'cart_user_' . $pembeli->idPembeli;
        Cache::forget($cartKey);
        return response()->json([
            "status" => true,
            "message" => "Keranjang berhasil dikosongkan",
        ], 200);
    }

    public function getPoin(Request $request)
    {
        try {
            $user = $request->user() ?? auth('pembeli')->user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated',
                    'data' => null
                ], 401);
            }
            $pembeli = \App\Models\Pembeli::with('alamat')->where('idPembeli', $user->idPembeli)->first();
            return response()->json([
                'status' => true,
                'message' => 'Get successful',
                'data' => $pembeli
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function pembatalan (){
        $pembeli = auth('pembeli')->user();
        if (!$pembeli) {
            return response()->json([
                "status" => false,
                "message" => "Pembeli belum login",
            ], 401);
        }

        $idPembeli = $pembeli->idPembeli;

        $pembatalan = \App\Models\TransaksiPembelian::where('status', 'Lunas Belum Dijadwalkan')
                    ->where('idPembeli', $idPembeli)->get();
        
        return response()->json([
            "status" => true,
            "message" => "Get successful",
            "data" => $pembatalan
        ], 200);

    }

    public function batalkanPembelian(Request $request, $noNota)
    {
        try {
            $pembeli = auth('pembeli')->user();
            if (!$pembeli) {
                return response()->json([
                    "status" => false,
                    "message" => "pembeli belum login",
                    "data" => null,
                ], 400);
            }

            $transaksi = \App\Models\TransaksiPembelian::where('noNota', $noNota)->first();
            if (!$transaksi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Transaksi tidak ditemukan.'
                ]);
            }

            $pembeli = Pembeli::where('idPembeli', $transaksi->idPembeli)->first();
            $alamat = $transaksi->idAlamat;

            $detailBarang = \App\Models\DetailTransaksiPembelian::where('noNota', $noNota)->get();
            $totalHarga = 0;

            foreach ($detailBarang as $detail) {
                // \Log::info("Detail ID Barang: " . $detail->idBarang);
                $barang = Barang::where('idBarang', $detail->idBarang)->first();

                if ($barang) {
                    // \Log::info("Barang ditemukan: " . $barang->idBarang . " | Harga: " . $barang->hargaBarang);
                    $totalHarga += $barang->hargaBarang;
                    $barang->update([
                        'statusBarang' => 'Tersedia'
                    ]);
                }
            }

            $pembeli->poin = $pembeli->poin + $transaksi->totalHarga / 10000;
            $pembeli->save();

            $transaksi->status = 'Dibatalkan Pembeli';
            $transaksi->save();

            return response()->json([
                'status' => true,
                'message' => 'berhasil.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => null
            ], 500);
        }
    }

    

}
