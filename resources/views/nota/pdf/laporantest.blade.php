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
                    <th>Nama Hunter</th>
                    <th>Komisi</th>
                    <th>id Barang</th>

                </tr>
            </thead>
            <tbody>


                @foreach ($result as $bar)
                    <tr>
                        <td>{{ $bar['namaHunter'] }}</td>
                        <td>{{ $bar['komisiHunter'] }}</td>
                        <td>{{ $bar['idBarang'] }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</body>
</html>
