<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reusemart - Laporan Komisi</title>
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

        <div class="header"  style="text-decoration: underline;">Laporan Komisi</div>
        <div >Bulan: {{ now()->locale('id')->translatedFormat('F') }}</div>
        <div >Tahun: {{ now()->year }}</div>
        <div style="margin-bottom: 12px;">Tanggal cetak: {{ now()->locale('id')->translatedFormat('d F Y')}}</div>

        <table style="border-collapse: collapse; width: 100%; " border="1" cellspacing="0" cellpadding="6">
             <thead>
                <tr>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Harga Jual</th>
                    <th>Tanggal Masuk</th>
                    <th>Tanggal Laku</th>
                    <th>Komisi Hunter</th>
                    <th>Komisi Reusemart</th>
                    <th>Bonus Penitip</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalHarga = 0;
                    $totalKomisiHunter = 0;
                    $totalKomisiMart = 0;
                    $totalBonusPenitip = 0;
                @endphp

                @foreach ($result as $bar)
                    @php
                        $totalHarga += $bar['hargaBarang'];
                        $totalKomisiHunter += $bar['komisiHunter'] ?? 0;
                        $totalKomisiMart += $bar['komisiMart'];
                        $totalBonusPenitip += $bar['bonusPenitip'] ?? 0;
                    @endphp

                    <tr>
                        <td>{{ $bar['idBarang'] }}</td>
                        <td>{{ $bar['namaBarang'] }}</td>
                        <td>{{ number_format($bar['hargaBarang'], 0, ',', '.') }}</td>
                        <td>{{ $bar['tanggalMasuk'] }}</td>
                        <td>{{ $bar['tanggalLaku'] }}</td>
                        <td>{{ number_format($bar['komisiHunter'], 0, ',', '.') }} </td>
                        <td>{{ number_format($bar['komisiMart'], 0, ',', '.') }}</td>
                        <td>{{ number_format($bar['bonusPenitip'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td></td>
                    <td>Total</td>
                    <td>{{ number_format($totalHarga, 0, ',', '.') }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ number_format($totalKomisiHunter, 0, ',', '.') }}</td>
                    <td>{{ number_format($totalKomisiMart, 0, ',', '.') }}</td>
                    <td>{{ number_format($totalBonusPenitip, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
