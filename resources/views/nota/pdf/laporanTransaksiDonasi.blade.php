<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reusemart - Laporan Transaksi Donasi</title>
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

        <div class="header"  style="text-decoration: underline;">Laporan Transaksi Donasi</div>
            @if(!is_numeric($year))
                 @php
                    $minYear = $donasi->min(fn($d) => \Carbon\Carbon::parse($d->transaksiDonasi->tanggalDonasi)->year);
                    $maxYear = $donasi->max(fn($d) => \Carbon\Carbon::parse($d->transaksiDonasi->tanggalDonasi)->year);
                @endphp
                <div>Tahun: {{ $minYear }} - {{ $maxYear }}</div>
            @else
                <div>Tahun: {{ $year }}</div>
            @endif

<!-- 
        <div>Tahun: {{ $tahun ?? now()->translatedFormat('Y') }}</div> -->
        <div style="margin-bottom: 12px;">Tanggal cetak: {{ now()->locale('id')->translatedFormat('d F Y') }}</div>

        
        <table style="border-collapse: collapse; width: 100%;" border="1" cellspacing="0" cellpadding="6">
             <thead>
                <tr>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Id Penitip</th>
                    <th>Nama Penitip</th>
                    <th>Tanggal Donasi</th>
                    <th>Organisasi</th>
                    <th>Nama Penerima</th>
                </tr>
            </thead>    
            <tbody>
                
                    @foreach ($donasi as $req)
                        <tr>
                            <td>{{ $req->transaksiDonasi->idBarang }}</td>
                            <td>{{ $req->transaksiDonasi->barang->namaBarang }}</td>
                            <td>{{ $req->transaksiDonasi->barang->detailTransaksiPenitipan->transaksiPenitipan->idPenitip}}</td>
                            <td>{{ $req->transaksiDonasi->barang->detailTransaksiPenitipan->transaksiPenitipan->penitip->namaPenitip }}</td>
                            <td style="text-align: center;">{{ \Carbon\Carbon::parse($req->transaksiDonasi->tanggalDonasi)->format('d/m/Y') }}</td>
                            <td>{{ $req->organisasi->namaOrganisasi }}</td>
                            <td>{{ $req->transaksiDonasi->namaPenerima }}</td>
                        </tr>
                    @endforeach
                
            </tbody>
        </table>
        
    </div>
</body>
</html>
