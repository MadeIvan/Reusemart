<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;  
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiDonasi;
use Illuminate\Support\Facades\DB;
use App\Models\ImagesBarang;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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
    $barang = Barang::with('detailTransaksiPenitipan.transaksiPenitipan.penitip', 
                            'imagesBarang', 
                            'detailTransaksiPenitipan.transaksiPenitipan.pegawai', 
                            'detailTransaksiPenitipan.transaksiPenitipan.pegawai2') // Load pegawai2 if available
                    ->get();

    $result = $barang->map(function($item) {
        // Get transaksiPenitipan instance safely
        $transaksi = optional($item->detailTransaksiPenitipan)->transaksiPenitipan;

        // Access the main pegawai (idPegawai1) from transaksiPenitipan
        $namaPegawai = optional(optional($transaksi)->pegawai)->namaPegawai;

        // Access the second pegawai (idPegawai2) if it exists
        $namaHunter = null;
        if (optional($transaksi)->idPegawai2) {
            // Check if idPegawai2 exists and load pegawai2 data
            $namaHunter = optional(optional($transaksi)->pegawai2)->namaPegawai;
        }
        $status = 'Tidak'; // Default status
        $tanggalPenitipan = optional($transaksi)->tanggalPenitipan;
        $tanggalPenitipanSelesai = optional($transaksi)->tanggalPenitipanSelesai;

        if ($tanggalPenitipan && $tanggalPenitipanSelesai) {
            $diffInDays = (new \DateTime($tanggalPenitipanSelesai))->diff(new \DateTime($tanggalPenitipan))->days;
            if ($diffInDays > 30) {
                $status = 'Ya'; // Set status to 'Ya' if the difference is greater than 30 days
            }
        }

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
            'tanggalMasuk' => optional($transaksi)->tanggalPenitipan,
            'tanggalPenitipanSelesai' => optional($transaksi)->tanggalPenitipanSelesai,
            'namaPenitip' => optional(optional($transaksi)->penitip)->namaPenitip,
            'idPenitip' => optional(optional($transaksi)->penitip)->idPenitip,
            'namaPegawai' => $namaPegawai, // Set namaHunter from pegawai's namaPegawai
            'namaHunter' => $namaHunter,
            'status'=> $status, // Set namaHunter2 from pegawai2's namaPegawai if idPegawai2 exists
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



public function indexall2()
{
    try {
    $barang = Barang::with('detailTransaksiPenitipan.transaksiPenitipan.penitip', 
                            'detailTransaksiPenitipan.transaksiPenitipan.pegawai', 
                            'detailTransaksiPenitipan.transaksiPenitipan.pegawai2',
                            'detailTransaksiPembelian.transaksiPembelian',
                            'detailTransaksiPembelian.transaksiPembelian.komisi') // Load pegawai2 if available
                            ->where('statusBarang', 'Terjual')
                            ->get();

    $result = $barang->map(function($item) {
        // Get transaksiPenitipan instance safely
        $transaksi = optional($item->detailTransaksiPenitipan)->transaksiPenitipan;
        $pembelian = $item->detailTransaksiPembelian->first();
        // Access the main pegawai (idPegawai1) from transaksiPenitipan
        $namaPegawai = optional(optional($transaksi)->pegawai)->namaPegawai;
        $tanggalLaku = optional(optional($pembelian)->transaksiPembelian)->tanggalWaktuPelunasan;
        // Access the second pegawai (idPegawai2) if it exists
        $namaHunter = null;
        if (optional($transaksi)->idPegawai2) {
            // Check if idPegawai2 exists and load pegawai2 data
            $namaHunter = optional(optional($transaksi)->pegawai2)->namaPegawai;
        }
        $status = 'Tidak'; // Default status
        $tanggalPenitipan = optional($transaksi)->tanggalPenitipan;
        $tanggalPenitipanSelesai = optional($transaksi)->tanggalPenitipanSelesai;
        $komisiHunter = optional(optional($pembelian)->transaksiPembelian)->komisi->komisiHunter;
        $komisiMart = 0.20 * $item->hargaBarang;
        $bonusPenitip = 0;

        // Calculate the difference between tanggalMasuk and tanggalLaku
        $tanggalMasuk = optional($item)->tanggalMasuk;
        if ($tanggalMasuk && $tanggalLaku) {
            $diffInDays = Carbon::parse($tanggalLaku)->diffInDays(Carbon::parse($tanggalMasuk));

            if ($diffInDays < 7) {
                // If the difference is less than 7 days, give 25% of komisiMart as bonusPenitip
                $bonusPenitip = 0.25 * $komisiMart;
                // Reduce komisiMart to 15% of hargaBarang
                $komisiMart = 0.15 * $item->hargaBarang;
            }
        }
        if ($tanggalPenitipan && $tanggalPenitipanSelesai) {
            $diffInDays = (new \DateTime($tanggalPenitipanSelesai))->diff(new \DateTime($tanggalPenitipan))->days;
            if ($diffInDays > 30) {
                $status = 'Ya'; // Set status to 'Ya' if the difference is greater than 30 days
            }
        }

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
            'komisiHunter' => $komisiHunter,
            'komisi' => optional(optional($pembelian)->transaksiPembelian)->komisi,
            'tanggalMasuk' => optional($transaksi)->tanggalPenitipan,
            'tanggalPenitipanSelesai' => optional($transaksi)->tanggalPenitipanSelesai,
            'namaPenitip' => optional(optional($transaksi)->penitip)->namaPenitip,
            'idPenitip' => optional(optional($transaksi)->penitip)->idPenitip,
            'namaPegawai' => $namaPegawai, // Set namaHunter from pegawai's namaPegawai
            'namaHunter' => $namaHunter,
            'tanggalLaku' => $tanggalLaku,
            'komisiMart' => $komisiMart,
            'bonusPenitip' => $bonusPenitip,
            'status'=> $status, // Set namaHunter2 from pegawai2's namaPegawai if idPegawai2 exists
            // Include all transaksiPenitipan attributes as a nested array
            
            
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
public function indexall3()
{
    try {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        
        $barang = Barang::with('detailTransaksiPenitipan.transaksiPenitipan.penitip', 
                                'detailTransaksiPenitipan.transaksiPenitipan.pegawai', 
                                'detailTransaksiPenitipan.transaksiPenitipan.pegawai2',
                                'detailTransaksiPembelian.transaksiPembelian',
                                'detailTransaksiPembelian.transaksiPembelian.komisi')
                        ->where('statusBarang', 'Terjual')
                        ->whereHas('detailTransaksiPenitipan.transaksiPenitipan', function($query) {
                            $query->whereNotNull('idPegawai2');
                        })
                        // ->groupBy(DB::raw('YEAR(komisi.transaksiPembelian.tanggalWaktuPelunasan), MONTH(komisi.transaksiPembelian.tanggalWaktuPelunasan)'))

                        
                        ->get();

    $result = $barang->map(function($item) {
        // Get transaksiPenitipan instance safely
        $transaksi = optional($item->detailTransaksiPenitipan)->transaksiPenitipan;
        $pembelian = $item->detailTransaksiPembelian->first();
        // Access the main pegawai (idPegawai1) from transaksiPenitipan
        $namaPegawai = optional(optional($transaksi)->pegawai)->namaPegawai;
        $tanggalLaku = optional(optional($pembelian)->transaksiPembelian)->tanggalWaktuPelunasan;
        // Access the second pegawai (idPegawai2) if it exists
        $namaHunter = optional(optional($transaksi)->pegawai2)->namaPegawai;

    // If namaHunter is null, return null to filter out this item later
        if (is_null($namaHunter)) {
            return null; // Filter out this item if namaHunter is null
        }
        if (!$namaHunter) {
        return null; // Filter out this item if namaHunter is null
        }
        //why no
        $status = 'Tidak'; // Default status
        $tanggalPenitipan = optional($transaksi)->tanggalPenitipan;
        $tanggalPenitipanSelesai = optional($transaksi)->tanggalPenitipanSelesai;
        $komisiHunter = optional($transaksi)->tanggalPenitipan;
        if(!$komisiHunter){
            return null;
        }
        $idHunter = optional($transaksi)->idPegawai2;
        $komisiMart = 0.20 * $item->hargaBarang;
        $bonusPenitip = 0;

        // Calculate the difference between tanggalMasuk and tanggalLaku
        $tanggalMasuk = optional($item)->tanggalMasuk;
        if ($tanggalMasuk && $tanggalLaku) {
            $diffInDays = Carbon::parse($tanggalLaku)->diffInDays(Carbon::parse($tanggalMasuk));

            if ($diffInDays < 7) {
                // If the difference is less than 7 days, give 25% of komisiMart as bonusPenitip
                $bonusPenitip = 0.25 * $komisiMart;
                // Reduce komisiMart to 15% of hargaBarang
                $komisiMart = 0.15 * $item->hargaBarang;
            }
        }
        if ($tanggalPenitipan && $tanggalPenitipanSelesai) {
            $diffInDays = (new \DateTime($tanggalPenitipanSelesai))->diff(new \DateTime($tanggalPenitipan))->days;
            if ($diffInDays > 30) {
                $status = 'Ya'; // Set status to 'Ya' if the difference is greater than 30 days
            }
        }

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
            'idHunter'=>$idHunter,
            'komisiHunter' => $komisiHunter,
            'komisi' => optional(optional($pembelian)->transaksiPembelian)->komisi,
            'tanggalMasuk' => optional($transaksi)->tanggalPenitipan,
            'tanggalPenitipanSelesai' => optional($transaksi)->tanggalPenitipanSelesai,
            'namaPenitip' => optional(optional($transaksi)->penitip)->namaPenitip,
            'idPenitip' => optional(optional($transaksi)->penitip)->idPenitip,
            'namaPegawai' => $namaPegawai, // Set namaHunter from pegawai's namaPegawai
            'namaHunter' => $namaHunter,
            'tanggalLaku' => $tanggalLaku,
            'komisiMart' => $komisiMart,
            'bonusPenitip' => $bonusPenitip,
            'status'=> $status, // Set namaHunter2 from pegawai2's namaPegawai if idPegawai2 exists
            // Include all transaksiPenitipan attributes as a nested array
            
            
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

public function indexByIdBarang($idBarang)
{
    try {
        // Fetch barang with necessary relationships, filtered by idBarang
        // Additionally, filter by idBarang and idPegawai2 (namaHunter)
        $barang = Barang::with('detailTransaksiPenitipan.transaksiPenitipan.penitip', 
                                'detailTransaksiPenitipan.transaksiPenitipan.pegawai', 
                                'detailTransaksiPenitipan.transaksiPenitipan.pegawai2',
                                'detailTransaksiPembelian.transaksiPembelian',
                                'detailTransaksiPembelian.transaksiPembelian.komisi')
                        ->where('statusBarang', 'Terjual')
                        ->where('idBarang', $idBarang) // Filter by the specified idBarang
                        ->whereHas('detailTransaksiPenitipan.transaksiPenitipan', function($query) {
                            // Only include items where idPegawai2 is not null in transaksiPenitipan
                            $query->whereNotNull('idPegawai2');
                        })
                        ->get();

        $result = $barang->map(function($item) {
            // Get transaksiPenitipan instance safely
            $transaksi = optional($item->detailTransaksiPenitipan)->transaksiPenitipan;
            $pembelian = $item->detailTransaksiPembelian->first();
            // Access the main pegawai (idPegawai1) from transaksiPenitipan
            $namaPegawai = optional(optional($transaksi)->pegawai)->namaPegawai;
            $tanggalLaku = optional(optional($pembelian)->transaksiPembelian)->tanggalWaktuPelunasan;
            // Access the second pegawai (idPegawai2) if it exists
            $namaHunter = optional(optional($transaksi)->pegawai2)->namaPegawai;

            // If namaHunter is null, return null to filter out this item later
            if (is_null($namaHunter)) {
                return null; // Filter out this item if namaHunter is null
            }

            $status = 'Tidak';
            $tanggalPenitipan = optional($transaksi)->tanggalPenitipan;
            $tanggalPenitipanSelesai = optional($transaksi)->tanggalPenitipanSelesai;
            $komisiHunter = optional(optional($pembelian)->transaksiPembelian)->komisi->komisiHunter;
            if (!$komisiHunter) {
                return null;
            }
            $komisiMart = 0.20 * $item->hargaBarang;
            $bonusPenitip = 0;

            // Calculate the difference between tanggalMasuk and tanggalLaku
            $tanggalMasuk = optional($item)->tanggalMasuk;
            if ($tanggalMasuk && $tanggalLaku) {
                $diffInDays = Carbon::parse($tanggalLaku)->diffInDays(Carbon::parse($tanggalMasuk));

                if ($diffInDays < 7) {
                    // If the difference is less than 7 days, give 25% of komisiMart as bonusPenitip
                    $bonusPenitip = 0.25 * $komisiMart;
                    // Reduce komisiMart to 15% of hargaBarang
                    $komisiMart = 0.15 * $item->hargaBarang;
                }
            }

            if ($tanggalPenitipan && $tanggalPenitipanSelesai) {
                $diffInDays = (new \DateTime($tanggalPenitipanSelesai))->diff(new \DateTime($tanggalPenitipan))->days;
                if ($diffInDays > 30) {
                    $status = 'Ya'; // Set status to 'Ya' if the difference is greater than 30 days
                }
            }

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
                'komisiHunter' => $komisiHunter,
                'komisi' => optional(optional($pembelian)->transaksiPembelian)->komisi,
                'tanggalMasuk' => optional($transaksi)->tanggalPenitipan,
                'tanggalPenitipanSelesai' => optional($transaksi)->tanggalPenitipanSelesai,
                'namaPenitip' => optional(optional($transaksi)->penitip)->namaPenitip,
                'idPenitip' => optional(optional($transaksi)->penitip)->idPenitip,
                'namaPegawai' => $namaPegawai,
                'namaHunter' => $namaHunter,
                'tanggalLaku' => $tanggalLaku,
                'komisiMart' => $komisiMart,
                'bonusPenitip' => $bonusPenitip,
                'status' => $status,
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

public function createpdfbyid($idBarang)
{
    try {
        // Fetch barang with necessary relationships, filtered by idBarang
        // Additionally, filter by idBarang and idPegawai2 (namaHunter)
        $barang = Barang::with('detailTransaksiPenitipan.transaksiPenitipan.penitip', 
                                'detailTransaksiPenitipan.transaksiPenitipan.pegawai', 
                                'detailTransaksiPenitipan.transaksiPenitipan.pegawai2',
                                'detailTransaksiPembelian.transaksiPembelian',
                                'detailTransaksiPembelian.transaksiPembelian.komisi')
                        ->where('statusBarang', 'Terjual')
                        ->where('idBarang', $idBarang) // Filter by the specified idBarang
                        ->whereHas('detailTransaksiPenitipan.transaksiPenitipan', function($query) {
                            // Only include items where idPegawai2 is not null in transaksiPenitipan
                            $query->whereNotNull('idPegawai2');
                        })
                        ->get();

        $result = $barang->map(function($item) {
            // Get transaksiPenitipan instance safely
            $transaksi = optional($item->detailTransaksiPenitipan)->transaksiPenitipan;
            $pembelian = $item->detailTransaksiPembelian->first();
            // Access the main pegawai (idPegawai1) from transaksiPenitipan
            $namaPegawai = optional(optional($transaksi)->pegawai)->namaPegawai;
            $tanggalLaku = optional(optional($pembelian)->transaksiPembelian)->tanggalWaktuPelunasan;
            // Access the second pegawai (idPegawai2) if it exists
            $namaHunter = optional(optional($transaksi)->pegawai2)->namaPegawai;

            // If namaHunter is null, return null to filter out this item later
            if (is_null($namaHunter)) {
                return null; // Filter out this item if namaHunter is null
            }

            $status = 'Tidak'; // Default status
            $tanggalPenitipan = optional($transaksi)->tanggalPenitipan;
            $tanggalPenitipanSelesai = optional($transaksi)->tanggalPenitipanSelesai;
            $komisiHunter = optional(optional($pembelian)->transaksiPembelian)->komisi->komisiHunter;
            if (!$komisiHunter) {
                return null;
            }
            $komisiMart = 0.20 * $item->hargaBarang;
            $bonusPenitip = 0;

            // Calculate the difference between tanggalMasuk and tanggalLaku
            $tanggalMasuk = optional($item)->tanggalMasuk;
            if ($tanggalMasuk && $tanggalLaku) {
                $diffInDays = Carbon::parse($tanggalLaku)->diffInDays(Carbon::parse($tanggalMasuk));

                if ($diffInDays < 7) {
                    // If the difference is less than 7 days, give 25% of komisiMart as bonusPenitip
                    $bonusPenitip = 0.25 * $komisiMart;
                    // Reduce komisiMart to 15% of hargaBarang
                    $komisiMart = 0.15 * $item->hargaBarang;
                }
            }

            if ($tanggalPenitipan && $tanggalPenitipanSelesai) {
                $diffInDays = (new \DateTime($tanggalPenitipanSelesai))->diff(new \DateTime($tanggalPenitipan))->days;
                if ($diffInDays > 30) {
                    $status = 'Ya'; // Set status to 'Ya' if the difference is greater than 30 days
                }
            }

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
                'komisiHunter' => $komisiHunter,
                'komisi' => optional(optional($pembelian)->transaksiPembelian)->komisi,
                'tanggalMasuk' => optional($transaksi)->tanggalPenitipan,
                'tanggalPenitipanSelesai' => optional($transaksi)->tanggalPenitipanSelesai,
                'namaPenitip' => optional(optional($transaksi)->penitip)->namaPenitip,
                'idPenitip' => optional(optional($transaksi)->penitip)->idPenitip,
                'namaPegawai' => $namaPegawai,
                'namaHunter' => $namaHunter,
                'tanggalLaku' => $tanggalLaku,
                'komisiMart' => $komisiMart,
                'bonusPenitip' => $bonusPenitip,
                'status' => $status,
            ];
        });

        return Pdf::loadView('nota.pdf.laporanHunter', compact('result'))
            ->setPaper('a4', 'landscape')
            ->stream("Laporan Stok Barang.pdf");
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'An error occurred while fetching the products.',
            'message' => $e->getMessage(),
            'status' => true
        ], 500);
    }
}

public function notaReqPdf()
{
    $barang = Barang::with('detailTransaksiPenitipan.transaksiPenitipan.penitip', 
                            'imagesBarang', 
                            'detailTransaksiPenitipan.transaksiPenitipan.pegawai', 
                            'detailTransaksiPenitipan.transaksiPenitipan.pegawai2') // Load pegawai2 if available
                    ->where('statusBarang', 'Tersedia') // Filter by statusBarang
                    ->get();

    $result = $barang->map(function($item) {
        // Get transaksiPenitipan instance safely
        $transaksi = optional($item->detailTransaksiPenitipan)->transaksiPenitipan;

        // Access the main pegawai (idPegawai1) from transaksiPenitipan
        $namaPegawai = optional(optional($transaksi)->pegawai)->namaPegawai;

        // Access the second pegawai (idPegawai2) if it exists
        $namaHunter = null;
        if (optional($transaksi)->idPegawai2) {
            $namaHunter = optional(optional($transaksi)->pegawai2)->namaPegawai;
        }
        $idHunter = null;
        if (optional($transaksi)->idPegawai2) {
            $idHunter = optional(optional($transaksi)->pegawai2)->idPegawai;
        }

        // Calculate the difference between tanggalPenitipan and tanggalPenitipanSelesai
        $status = 'Tidak'; // Default status
        $tanggalPenitipan = optional($transaksi)->tanggalPenitipan;
        $tanggalPenitipanSelesai = optional($transaksi)->tanggalPenitipanSelesai;

        if ($tanggalPenitipan && $tanggalPenitipanSelesai) {
            $diffInDays = (new \DateTime($tanggalPenitipanSelesai))->diff(new \DateTime($tanggalPenitipan))->days;
            if ($diffInDays > 30) {
                $status = 'Ya'; // Set status to 'Ya' if the difference is greater than 30 days
            }
        }

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
            'tanggalMasuk' => optional($transaksi)->tanggalPenitipan,
            'tanggalPenitipanSelesai' => optional($transaksi)->tanggalPenitipanSelesai,
            'namaPenitip' => optional(optional($transaksi)->penitip)->namaPenitip,
            'idPenitip' => optional(optional($transaksi)->penitip)->idPenitip,
            'namaPegawai' => $namaPegawai, // Set namaHunter from pegawai's namaPegawai
            'namaHunter' => $namaHunter,
            'idHunter' => $idHunter, // Set namaHunter2 from pegawai2's namaPegawai if idPegawai2 exists
            'status' => $status, // Include the status based on the date difference
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

    return Pdf::loadView('nota.pdf.laporanGudang', compact('result'))
            ->setPaper('a4', 'landscape')
            ->stream("Laporan Stok Barang.pdf");
}
public function notaReqPdf2()
{
    $barang = Barang::with('detailTransaksiPenitipan.transaksiPenitipan.penitip', 
                            'detailTransaksiPenitipan.transaksiPenitipan.pegawai', 
                            'detailTransaksiPenitipan.transaksiPenitipan.pegawai2',
                            'detailTransaksiPembelian.transaksiPembelian',
                            'detailTransaksiPembelian.transaksiPembelian.komisi') // Load pegawai2 if available
                            ->where('statusBarang', 'Terjual')
                            
                            ->get();

    $result = $barang->map(function($item) {
        // Get transaksiPenitipan instance safely
        $transaksi = optional($item->detailTransaksiPenitipan)->transaksiPenitipan;
        $pembelian = $item->detailTransaksiPembelian->first();
        // Access the main pegawai (idPegawai1) from transaksiPenitipan
        $namaPegawai = optional(optional($transaksi)->pegawai)->namaPegawai;
        $tanggalLaku = optional(optional($pembelian)->transaksiPembelian)->tanggalWaktuPelunasan;
        // Access the second pegawai (idPegawai2) if it exists
        $namaHunter = null;
        if (optional($transaksi)->idPegawai2) {
            // Check if idPegawai2 exists and load pegawai2 data
            $namaHunter = optional(optional($transaksi)->pegawai2)->namaPegawai;
        }
        $status = 'Tidak'; // Default status
        $tanggalPenitipan = optional($transaksi)->tanggalPenitipan;
        $tanggalPenitipanSelesai = optional($transaksi)->tanggalPenitipanSelesai;
        $komisiHunter = optional(optional($pembelian)->transaksiPembelian)->komisi->komisiHunter;
        $komisiMart = 0.20 * $item->hargaBarang;
        $bonusPenitip = 0;

        // Calculate the difference between tanggalMasuk and tanggalLaku
        $tanggalMasuk = optional($item)->tanggalMasuk;
        if ($tanggalMasuk && $tanggalLaku) {
            $diffInDays = Carbon::parse($tanggalLaku)->diffInDays(Carbon::parse($tanggalMasuk));

            if ($diffInDays < 7) {
                // If the difference is less than 7 days, give 25% of komisiMart as bonusPenitip
                $bonusPenitip = 0.25 * $komisiMart;
                // Reduce komisiMart to 15% of hargaBarang
                $komisiMart = 0.15 * $item->hargaBarang;
            }
        }
        if ($tanggalPenitipan && $tanggalPenitipanSelesai) {
            $diffInDays = (new \DateTime($tanggalPenitipanSelesai))->diff(new \DateTime($tanggalPenitipan))->days;
            if ($diffInDays > 30) {
                $status = 'Ya'; // Set status to 'Ya' if the difference is greater than 30 days
            }
        }

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
            'komisiHunter' => $komisiHunter,
            'komisi' => optional(optional($pembelian)->transaksiPembelian)->komisi,
            'tanggalMasuk' => optional($transaksi)->tanggalPenitipan,
            'tanggalPenitipanSelesai' => optional($transaksi)->tanggalPenitipanSelesai,
            'namaPenitip' => optional(optional($transaksi)->penitip)->namaPenitip,
            'idPenitip' => optional(optional($transaksi)->penitip)->idPenitip,
            'namaPegawai' => $namaPegawai, // Set namaHunter from pegawai's namaPegawai
            'namaHunter' => $namaHunter,
            'tanggalLaku' => $tanggalLaku,
            'komisiMart' => $komisiMart,
            'bonusPenitip' => $bonusPenitip,
            'status'=> $status, // Set namaHunter2 from pegawai2's namaPegawai if idPegawai2 exists
            // Include all transaksiPenitipan attributes as a nested array
            
            
        ];
    });
    $filteredResult = $result->filter(function($item) {
            return Carbon::parse($item['tanggalLaku'])->isCurrentMonth(); // Filter where 'komisiMart' is greater than 0
        });
    $result = $filteredResult;
    
    return Pdf::loadView('nota.pdf.laporanKomisi', compact('result'))
            ->setPaper('a4', 'landscape')
            ->stream("Laporan Komisi.pdf");
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
public function showIdPenitipAndBarang($idBarang)
{
    try {
        $item = Barang::with('detailTransaksiPenitipan.transaksiPenitipan.penitip')
            ->where('idBarang', $idBarang)
            ->first();

        if (!$item) {
            return response()->json([
                'error' => 'Barang not found',
                'status' => false
            ], 404);
        }

        $transaksi = optional($item->detailTransaksiPenitipan)->transaksiPenitipan;
        $idPenitip = optional($transaksi)->idPenitip;

        $result = [
            'idBarang' => $item->idBarang,
            'idPenitip' => $idPenitip,
        ];

        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'An error occurred while fetching data.',
            'message' => $e->getMessage(),
            'status' => false
        ], 500);
    }
}



}