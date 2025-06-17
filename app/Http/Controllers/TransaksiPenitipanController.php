<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\TransaksiPenitipan;
use App\Models\DetailTransaksiPenitipan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Pegawai;
use App\Models\TransaksiPenitipan;
use App\Models\DetailTransaksiPenitipan;
use App\Models\Penitip;
use App\Models\Barang;
use App\Models\DetailTransaksiPembelian;
use App\Models\Komisi;
use Barryvdh\DomPDF\Facade\Pdf;
class TransaksiPenitipanController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'idPegawai1' => 'required|exists:pegawai,idPegawai',
        'idPegawai2' => 'nullable|exists:pegawai,idPegawai',
        'idPenitip' => 'required|exists:penitip,idPenitip',
        'totalHarga' => 'required|numeric|min:0',
        'idBarang' => 'required|exists:barang,idBarang'
    ]);

    if (DetailTransaksiPenitipan::where('idBarang', $request->idBarang)->exists()) {
        return response()->json([
            'status' => false,
            'message' => 'Barang already associated with a Transaksi Penitipan'
        ], 400);
    }

    DB::beginTransaction();
    try {
        $last = TransaksiPenitipan::orderBy('idTransaksiPenitipan', 'desc')->first();
        $lastNumber = $last ? (int) str_replace('TP', '', $last->idTransaksiPenitipan) : 0;
        $newIdTP = 'TP' . ($lastNumber + 1);
        while (TransaksiPenitipan::where('idTransaksiPenitipan', $newIdTP)->exists()) {
            $lastNumber++;
            $newIdTP = 'TP' . $lastNumber;
        }

        $tanggalMulai = new \DateTime($request->tanggalPenitipan);
        $tanggalSelesai = (clone $tanggalMulai)->modify('+30 days');

        $transaksi = TransaksiPenitipan::create([
            'idTransaksiPenitipan' => $newIdTP,
            'idPegawai1' => $request->idPegawai1,
            'idPegawai2' => $request->idPegawai2,
            'idPenitip' => $request->idPenitip,
            'tanggalPenitipan' => now(),
            'tanggalPenitipanSelesai' => now()->addDays(30),
            'totalHarga' => $request->totalHarga,
        ]);

        DetailTransaksiPenitipan::create([
            'idTransaksiPenitipan' => $newIdTP,
            'idBarang' => $request->idBarang
        ]);

        DB::commit();
        return response()->json([
            'status' => true,
            'message' => 'Transaksi Penitipan created successfully',
            'data' => $transaksi
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Transaction failed: ' . $e->getMessage()
        ], 500);
    }
}


public function add30($id){
    $tanggalMulai = new \DateTime($id);

     $tanggalSelesai = (clone $tanggalMulai)->modify('+30 days');
    return $tanggalSelesai;

}

public function getallbyid($id){
    try {
        // Try to find the barang by its ID
       
        $barang = TransaksiPenitipan::where('tanggalPenitipanSelesai', $id)->get();

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
public function notaPenitipanPdf($id)
{
    $idTransaksi=$id;
    $transaksiPenitipan = TransaksiPenitipan::with(['penitip', 'detailTransaksiPenitipan.barang'])->findOrFail($idTransaksi);
    $detailTransaksiPenitipan = $transaksiPenitipan->detailTransaksiPenitipan;
    $pegawai = Pegawai::find($transaksiPenitipan->idPegawai1); // misal idPegawaiQC untuk yang QC
    

    $pdf = Pdf::loadView('PegawaiGudang.notaPenitipan', compact('transaksiPenitipan', 'detailTransaksiPenitipan', 'pegawai'));

    return $pdf->download('nota-penitipan-' . $id . '.pdf');

    // $transaksi = \App\Models\TransaksiPenitipan::with([
    //     'detailTransaksiPenitipan.barang',
    //     'pegawai',
    //     'pegawai2',
    //     'penitip',

        
    // ])->where('idTransaksiPenitipan', $id)->firstOrFail();
    // // return response()->json([
    // //     'status' => true,
    // //     'message' => 'Data transaksi berhasil diambil',
    // //     'data' => $transaksi
    // // ]);

    // return Pdf::loadView('Pegawai.notaPenitipan', compact('transaksi'))
    //     ->download("nota-penitipan-{$id}.pdf");
}


    public function transaksiPenitip($id){

        $penitipan = TransaksiPenitipan::with([
            'penitip',
            'detailTransaksiPenitipan.barang.detailTransaksiPembelian' => function ($query) {
                $query->whereHas('transaksiPembelian', function ($q) {
                    $q->where(function ($subQuery) {
                        $subQuery->where('status', 'like', 'Lunas%')
                                ->orWhere('status', 'like', '%Barang diterima%');
                    });
                });
            },
            'detailTransaksiPenitipan.barang.detailTransaksiPembelian.transaksiPembelian.komisi'
        ])
        ->where('idPenitip', $id)
        ->whereHas('detailTransaksiPenitipan.barang', function ($query) {
            $query->where('statusBarang', 'Terjual')
                ->whereHas('detailTransaksiPembelian.transaksiPembelian', function ($q) {
                  $q->where(function ($subQuery) {
                        $subQuery->where('status', 'like', 'Lunas%')
                                ->orWhere('status', 'like', '%Barang Diterima%');
                    });
              });

        })
        ->get();

        $laporan = [];

        foreach ($penitipan as $transaksi) {
            $penitip = $transaksi->penitip;

            foreach ($transaksi->detailTransaksiPenitipan as $detail) {
                $barang = $detail->barang;

                if ($barang && $barang->detailTransaksiPembelian->isNotEmpty()) {
                    foreach ($barang->detailTransaksiPembelian as $dtp) {
                        $transaksiPembelian = $dtp->transaksiPembelian ?? null;
                        $komisi = Komisi::where('noNota', $transaksiPembelian->noNota)
                                    ->first();

                        $laporan[] = [
                            'penitip' => $penitip,
                            'barang'=> $barang,
                            'komisi'=> $komisi,
                            'tanggalMasuk' => $transaksi->tanggalPenitipan,
                        ];
                    }
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $laporan
        ]);
    }


    public function laporanTransaksiPenitipPdf(Request $request)
    {
        $id = $request->query('idPenitip');
        $thn = $request->query('tahun');
        $bln = $request->query('bulan');

        $query = TransaksiPenitipan::with([
            'penitip',
            'detailTransaksiPenitipan.barang.detailTransaksiPembelian' => function ($query) {
                $query->whereHas('transaksiPembelian', function ($q) {
                    $q->where(function ($subQuery) {
                        $subQuery->where('status', 'like', 'Lunas%')
                                ->orWhere('status', 'like', '%Barang diterima%');
                    });
                });
            },
            'detailTransaksiPenitipan.barang.detailTransaksiPembelian.transaksiPembelian.komisi'
        ])
        ->where('idPenitip', $id)
        ->whereHas('detailTransaksiPenitipan.barang', function ($query) {
            $query->where('statusBarang', 'Terjual')
                ->whereHas('detailTransaksiPembelian.transaksiPembelian', function ($q) {
                    $q->where(function ($subQuery) {
                        $subQuery->where('status', 'like', 'Lunas%')
                                ->orWhere('status', 'like', '%Barang diterima%');
                    });
                });
        });

        if ($thn) {
            $query->whereHas('detailTransaksiPenitipan.barang.detailTransaksiPembelian.transaksiPembelian', function ($query) use ($thn) {
                $query->whereYear('tanggalWaktuPembelian', $thn);
            });
        }

            if ($bln) {
            $query->whereHas('detailTransaksiPenitipan.barang.detailTransaksiPembelian.transaksiPembelian', function ($query) use ($bln) {
                $query->whereMonth('tanggalWaktuPembelian', $bln);
            });
        }


        $penitipan = $query->get();

        $laporan = [];

        foreach ($penitipan as $transaksi) {
            $penitip = $transaksi->penitip;

            foreach ($transaksi->detailTransaksiPenitipan as $detail) {
                $barang = $detail->barang;

                if ($barang && $barang->detailTransaksiPembelian->isNotEmpty()) {
                    foreach ($barang->detailTransaksiPembelian as $dtp) {
                        $transaksiPembelian = $dtp->transaksiPembelian ?? null;

                        if ($transaksiPembelian && (
                            str_starts_with($transaksiPembelian->status, 'Barang diterima') ||
                            str_starts_with($transaksiPembelian->status, 'Lunas Siap Diantarkan') || 
                            str_starts_with($transaksiPembelian->status, 'Lunas Siap Diambil') ||
                            str_starts_with($transaksiPembelian->status, 'Lunas Belum Dijadwalkan') ||
                            str_starts_with($transaksiPembelian->status, 'Lunas Belum Diambil') ||
                            str_starts_with($transaksiPembelian->status, 'Lunas Dikirim') ||
                            str_starts_with($transaksiPembelian->status, 'Lunas Tidak Diambil (Didonasikan)')
                        )) {
                            $komisi = Komisi::where('noNota', $transaksiPembelian->noNota)->first();

                            $laporan[] = [
                                'penitip' => $penitip,
                                'barang' => $barang,
                                'komisi' => $komisi,
                                'tanggalMasuk' => $transaksi->tanggalPenitipan,
                            ];
                        }
                    }
                }
            }
        }

        $penitipData = $penitipan->first()?->penitip ?? null;

        return Pdf::loadView('nota.pdf.laporanUntukPenitip', compact('laporan', 'penitipData', 'thn', 'bln'))
            ->setPaper('a4', 'landscape')
            ->stream("Laporan Penitip.pdf");
    }

}
