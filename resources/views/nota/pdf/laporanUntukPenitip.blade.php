<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reusemart - Laporan Penitip</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; }
        .nota-box { border: 1px solid #222; padding: 18px 22px 12px 22px; margin-top: 14px; }
        .header { font-size: 16px; font-weight: bold; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .sign-box { margin-top: 25px; }

    </style>
</head>
<body>
    <div class="nota-box">
        <div class="header">ReUse Mart</div>
        <div style="margin-bottom: 12px;">Jl. Green Eco Park No. 456 Yogyakarta</div>

        <div class="header"  style="text-decoration: underline;">Laporan Transaksi Penitip</div>

        @if($penitipData)
            <div>ID Penitip: {{ $penitipData->idPenitip }}</div>
            <div>Nama Penitip: {{ $penitipData->namaPenitip }}</div>
        @else
            <div>ID Penitip: -</div>
            <div>Nama Penitip: -</div>
        @endif

        @php
            
            \Carbon\Carbon::setLocale('id');
            $namaBulan = is_numeric($bln) ? \Carbon\Carbon::create()->month($bln)->translatedFormat('F') : $bln;
        @endphp
        <div>Bulan: {{$namaBulan}}</div>

        <div>Tahun: {{ $thn }}</div>

        <div style="margin-bottom: 12px;">Tanggal cetak: {{ now()->locale('id')->translatedFormat('d F Y') }}</div>

        
        <table style="border-collapse: collapse; width: 100%;" border="1" cellspacing="0" cellpadding="6">
             <thead>
                <tr>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Tanggal Masuk</th>
                    <th>Tanggal Laku</th>
                    <th>Harga Jual Bersih (sudah dipotong Komisi)</th>
                    <th>Bonus terjual cepat</th>
                    <th>Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laporan as $lap)
                    @php
                        $barang = $lap['barang'];
                        $penitip = $lap['penitip'];
                        $tanggalMasuk = \Carbon\Carbon::parse($lap['tanggalMasuk']);
                        $tanggalLaku = optional($barang->detailTransaksiPembelian->first()->transaksiPembelian)->tanggalWaktuPembelian 
                                        ? \Carbon\Carbon::parse($barang->detailTransaksiPembelian->first()->transaksiPembelian->tanggalWaktuPembelian)
                                        : null;

                        $hargaBarang = $barang->hargaBarang ?? 0;
                        $hargaJualBersih = 0;
                        $bonusTerjualCepat = 0;
                        $pendapatan = 0;

                        if ($tanggalLaku) {
                            $selisihHari = $tanggalMasuk->diffInDays($tanggalLaku);

                            if ($selisihHari < 7) {
                                $hargaJualBersih = $hargaBarang * 0.8;
                                $bonusTerjualCepat = ($hargaBarang * 0.2) * 0.1;
                                $pendapatan = $hargaJualBersih + $bonusTerjualCepat;
                            } elseif ($selisihHari <= 30) {
                                $hargaJualBersih = $hargaBarang * 0.8;
                                $pendapatan = $hargaJualBersih;
                            } elseif ($selisihHari < 60 && $selisihHari >= 30) {
                                $hargaJualBersih = $hargaBarang * 0.7;
                                $pendapatan = $hargaJualBersih;
                            }
                        }
                    @endphp

                    <tr>
                        <td>{{ $barang->idBarang }}</td>
                        <td>{{ $barang->namaBarang }}</td>
                        <td>{{ $tanggalMasuk->format('d/m/Y') }}</td>
                        <td>{{ $tanggalLaku ? $tanggalLaku->format('d/m/Y') : '-' }}</td>
                        <td>Rp {{ number_format($hargaJualBersih, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($bonusTerjualCepat, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($pendapatan, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</body>
</html>
