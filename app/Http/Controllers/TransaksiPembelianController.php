<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\TransaksiPembelian;
use App\Models\DetailTransaksiPembelian;
use App\Models\Barang;
use Exception;

class TransaksiPembelianController extends Controller
{
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

    public function store(Request $request){
        try {
            \Log::info('Data masuk:', $request->all());

            $validated = $request->validate([
                'idAlamat' => 'nullable|string',
                'tanggalWaktuPembelian' => 'required|date_format:Y-m-d H:i:s',
                'totalHarga' => 'required|numeric',
                'id_barang' => 'required|array',
                'id_barang.*' => 'string',
                'sisaPoin' => 'required|numeric',
            ]);

            // Cek apakah pembeli sudah login
            $pembeli = auth('pembeli')->user();
            if (!$pembeli) {
                return response()->json([
                    "status" => false,
                    "message" => "Pembeli Belum Login",
                    "data" => null,
                ], 400);
            }
            
            \Log::info('sisaPoin value:', ['value' => $request->input('sisaPoin'), 'type' => gettype($request->input('sisaPoin'))]);

            DB::beginTransaction();
            $yearMonth = date('Y.m');

            $last = TransaksiPembelian::where('noNota', 'like', "$yearMonth.%")
                ->orderBy('noNota', 'desc')
                ->first();

            $lastNumber = 0;
            if ($last) {
                $parts = explode('.', $last->noNota);
                $lastNumber = intval(end($parts));
            }
            $newId = $yearMonth . '.' . ($lastNumber + 1);

            // Simpan transaksi utama
            $transaksiPembelian = TransaksiPembelian::create([
                'noNota' => $newId,
                'idPembeli' => $pembeli->idPembeli,
                'idAlamat' => $validated['idAlamat'],
                'tanggalWaktuPembelian' => $validated['tanggalWaktuPembelian'],
                'status' => "Menunggu Pembayaran",
                'totalHarga' => $validated['totalHarga']
            ]);

            $idBarangs = $validated['id_barang'];
            // Simpan detail barang
            foreach ($validated['id_barang'] as $idBarang) {
                \Log::info("ID Barang:", [$idBarang]);
                

                DB::table('detailtransaksipembelian')->insert([
                    'noNota' => $newId,
                    'idBarang' => $idBarang
                ]);

                DB::table('barang')->where('idBarang', $idBarang)->update([
                    'statusBarang' => 'Terjual'
                ]);
            }

            DB::table('pembeli')->where('idPembeli', $pembeli->idPembeli)->update([
                'poin' => $validated['sisaPoin']
            ]);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Transaksi berhasil dibuat",
                "data" => $transaksiPembelian
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error saat simpan transaksi: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            return response()->json([
                "status" => false,
                "message" => "Terjadi kesalahan: " . $e->getMessage(),
                "data" => null
            ], 500);
        }
    }

    public function getDataTerbaru(Request $request){
        $pembeli = auth('pembeli')->user();
            if (!$pembeli) {
                return response()->json([
                    "status" => false,
                    "message" => "Pembeli Belum Login",
                    "data" => null,
                ], 400);
            }
        $yearMonth = date('Y.m');
        $last = TransaksiPembelian::where('noNota', 'like', "$yearMonth.%")
                ->orderBy('noNota', 'desc')
                ->first();

        return response()->json([
            "status" => true,
            "message" => "Berhasil mendapatkan No Nota terbaru",
            "data" => $last
        ]);
    }

    public function buktiBayar(Request $request, $id){
        try {
            $validated = $request->validate([
                'tanggalWaktuPelunasan' => 'required|date_format:Y-m-d H:i:s',
                'buktiPembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $pembeli = auth('pembeli')->user();
            if (!$pembeli) {
                return response()->json([
                    "status" => false,
                    "message" => "Pembeli Belum Login",
                    "data" => null,
                ], 400);
            }

            // Upload file bukti pembayaran
            $file = $request->file('buktiPembayaran');
            $path = $file->store('buktiPembayaran', 'public');

            $transaksi = TransaksiPembelian::where('noNota', $id)->first();

            if ($transaksi) {
                $transaksi->buktiPembayaran = $path;
                $transaksi->status = 'Menunggu Verifikasi';
                $transaksi->tanggalWaktuPelunasan = $validated['tanggalWaktuPelunasan'];
                $transaksi->save();

                return response()->json([
                    "status" => true,
                    "message" => "Berhasil mengunggah bukti pembayaran",
                    "data" => $transaksi
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Transaksi tidak ditemukan",
                    "data" => null
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Terjadi kesalahan: " . $e->getMessage(),
                "data" => null
            ], 500);
        }
    }

    public function canceled(Request $request, $id){
        try{
            ///////////cek pembeli///////////
            $pembeli = auth('pembeli')->user();
            if (!$pembeli) {
                return response()->json([
                    "status" => false,
                    "message" => "Pembeli Belum Login",
                    "data" => null,
                ], 400);
            }

            $validated = $request->validate([
                'poinAwal' => 'required|numeric',
                'id_barang' => 'required|array',
                'id_barang.*' => 'string',
            ]);

            //////////////////////ubah ke sebelum transaksi/////////////////////
            DB::beginTransaction();
            $idBarangs = $validated['id_barang'];
            // Simpan detail barang
            foreach ($validated['id_barang'] as $idBarang) {
                \Log::info("ID Barang:", [$idBarang]);

                DB::table('barang')->where('idBarang', $idBarang)->update([
                    'statusBarang' => 'Tersedia'
                ]);
            }

            DB::table('pembeli')->where('idPembeli', $pembeli->idPembeli)->update([
                'poin' => $validated['poinAwal']
            ]);

            DB::commit();

            $transaksi = TransaksiPembelian::where('noNota', $id)->first();
            return response()->json([
                "status" => true,
                "message" => "Pesanan dibatalkan",
                "data" => $transaksi
            ], 200);
        }catch (Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Terjadi kesalahan: " . $e->getMessage(),
                "data" => null
            ], 500);
        }
    }
}
