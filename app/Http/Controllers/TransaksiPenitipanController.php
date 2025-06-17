<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
// use App\Models\TransaksiPenitipan;
// use App\Models\DetailTransaksiPenitipan;
// use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Pegawai;
use App\Models\TransaksiPenitipan;
use App\Models\DetailTransaksiPenitipan;

use Carbon\Carbon;

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




public function getAllByPenitip($idPenitip)
{
    // Eager load imagesbarang for each barang
    $transaksi = \App\Models\TransaksiPenitipan::with([
        'detailTransaksiPenitipan.barang.imagesbarang'
    ])
    ->where('idPenitip', $idPenitip)
    ->orderBy('tanggalPenitipan', 'desc')
    ->get();

    // Map the response to include all images for each barang (from ImagesBarang table)
    $result = $transaksi->map(function($tp) {
        $tpArr = $tp->toArray();
        $tpArr['detailTransaksiPenitipan'] = collect($tp->detailTransaksiPenitipan)->map(function($detail) {
            $barang = $detail->barang;
            // If images are in the related imagesbarang table
            $images = [
                'image1' => $barang->imagesbarang->image1 ?? null,
                'image2' => $barang->imagesbarang->image2 ?? null,
                'image3' => $barang->imagesbarang->image3 ?? null,
                'image4' => $barang->imagesbarang->image4 ?? null,
                'image5' => $barang->imagesbarang->image5 ?? null,
            ];
            $detailArr = $detail->toArray();
            $detailArr['imagesBarang'] = $images;
            return $detailArr;
        });
        unset($tpArr['detailTransaksiPenitipan']);
        return $tpArr;
    });

    return response()->json([
        'status' => true,
        'message' => 'Get successful',
        'data' => $result
    ], 200);
}


    public function perpanjangPenitipan($idTransaksiPenitipan)
    {
        $transaksi = TransaksiPenitipan::find($idTransaksiPenitipan);
        if (!$transaksi) {
            return response()->json([
                'status' => false,
                'message' => 'Transaksi penitipan tidak ditemukan.'
            ], 404);
        }

        // Tambah 30 hari dari tanggal hari ini
        $newDate = Carbon::now()->addDays(30)->toDateString();
        $transaksi->tanggalPenitipanSelesai = $newDate;
        $transaksi->save();

        return response()->json([
            'status' => true,
            'message' => 'Tanggal penitipan selesai berhasil diperpanjang.',
            'tanggalPenitipanSelesai' => $newDate
        ]);
    }


    
public function laporanPenitipanHabis(Request $request)
{
    $now = now()->toDateString();

    // Ambil semua transaksi penitipan yang sudah habis masa penitipannya
    $transaksis = \App\Models\TransaksiPenitipan::with([
        'detailTransaksiPenitipan.barang',
        'penitip'
    ])
    ->where('tanggalPenitipanSelesai', '<', $now)
    ->get();

    $rows = [];
    foreach ($transaksis as $tp) {
        foreach ($tp->detailTransaksiPenitipan as $detail) {
            $barang = $detail->barang;
            if (!$barang) continue;
            $rows[] = [
                'kode_produk' => $barang->idBarang,
                'nama_produk' => $barang->namaBarang,
                'id_penitip' => $tp->penitip->idPenitip ?? '',
                'nama_penitip' => $tp->penitip->namaPenitip ?? '',
                'tanggal_masuk' => \Carbon\Carbon::parse($tp->tanggalPenitipan)->format('d/m/Y'),
                'tanggal_akhir' => \Carbon\Carbon::parse($tp->tanggalPenitipanSelesai)->format('d/m/Y'),
                'batas_ambil' => \Carbon\Carbon::parse($tp->tanggalPenitipanSelesai)->addDays(7)->format('d/m/Y'),
            ];
        }
    }

    // Untuk API
    if ($request->wantsJson()) {
        return response()->json([
            'tanggalCetak' => now()->format('d F Y'),
            'data' => $rows,
        ]);
    }

    // Untuk PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('nota.pdf.laporanPenitipanHabis', [
        'rows' => $rows,
        'tanggalCetak' => now()->format('d F Y'),
    ]);
    return $pdf->stream('laporan-penitipan-habis.pdf');
}
public function apiPenitipanHabis(Request $request)
{
    $now = now()->toDateString();

    // Ambil semua transaksi penitipan yang sudah habis masa penitipannya
    $transaksis = \App\Models\TransaksiPenitipan::with([
        'detailTransaksiPenitipan.barang',
        'penitip'
    ])
    ->where('tanggalPenitipanSelesai', '<', $now)
    ->get();

    $rows = [];
    foreach ($transaksis as $tp) {
        foreach ($tp->detailTransaksiPenitipan as $detail) {
            $barang = $detail->barang;
            if (!$barang) continue;
            $rows[] = [
                'kode_produk' => $barang->idBarang,
                'nama_produk' => $barang->namaBarang,
                'id_penitip' => $tp->penitip->idPenitip ?? '',
                'nama_penitip' => $tp->penitip->namaPenitip ?? '',
                'tanggal_masuk' => \Carbon\Carbon::parse($tp->tanggalPenitipan)->format('d/m/Y'),
                'tanggal_akhir' => \Carbon\Carbon::parse($tp->tanggalPenitipanSelesai)->format('d/m/Y'),
                'batas_ambil' => \Carbon\Carbon::parse($tp->tanggalPenitipanSelesai)->addDays(7)->format('d/m/Y'),
            ];
        }
    }

    return response()->json([
        'tanggalCetak' => now()->format('d F Y'),
        'data' => $rows,
    ]);
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
