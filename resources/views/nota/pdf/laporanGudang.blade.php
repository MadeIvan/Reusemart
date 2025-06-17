<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reusemart - Laporan Stok Gudang</title>
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

        <div class="header"  style="text-decoration: underline;">Laporan Stok Gudang {{ now()->locale('id')->translatedFormat('d F Y')}}</div>
        <div style="margin-bottom: 12px;">Tanggal cetak: {{ now()->locale('id')->translatedFormat('d F Y')}}</div>

        <table style="border-collapse: collapse; width: 100%; " border="1" cellspacing="0" cellpadding="6">
             <thead>
                <tr>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>ID Penitip</th>
                    <th>Nama Penitip</th>
                    <th>Tanggal Masuk</th>
                    <th>Perpanjangan</th>
                    <th>ID Hunter</th>
                    <th>Nama Hunter</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($result as $bar)

                    <tr>
                        <td>{{ $bar['idBarang'] }}</td>
                        <td>{{ $bar['namaBarang'] }}</td>
                        <td>{{ $bar['idPenitip'] }}</td>
                        <td>{{ $bar['namaPenitip'] }}</td>
                        <td>{{ $bar['tanggalMasuk'] }}</td>
                        <td>{{ $bar['status'] }}</td>
                        <td>{{ $bar['idHunter'] }}</td>
                        <td>{{ $bar['namaHunter'] ?? '-' }}</td>
                        <td>{{ $bar['hargaBarang'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
