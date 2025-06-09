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
                @foreach ($result as $bar)

                    <tr>
                        <td>{{ $bar['idBarang'] }}</td>
                        <td>{{ $bar['namaBarang'] }}</td>
                        <td>{{ $bar['hargaBarang'] }}</td>
                        <td>{{ $bar['tanggalMasuk'] }}</td>
                        <td>{{ $bar['tanggalLaku'] }}</td>
                        <td>{{ $bar['komisiHunter'] ?? '0' }}</td>
                        <td>{{ $bar['komisiMart'] }}</td>
                        <td>{{ $bar['bonusPenitip'] ?? '0' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
