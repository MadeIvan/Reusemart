<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reusemart - Laporan Request Donasi</title>
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

        <div class="header"  style="text-decoration: underline;">Laporan Request Donasi</div>
        <div style="margin-bottom: 12px;">Tanggal cetak: {{ now()->locale('id')->translatedFormat('d F Y')}}</div>

        <table style="border-collapse: collapse; width: 100%; " border="1" cellspacing="0" cellpadding="6">
             <thead>
                <tr>
                    <th>ID Organisasi</th>
                    <th>Nama Organisasi</th>
                    <th>Alamat</th>
                    <th>Barang Request</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reqDonasi as $req)
                    <tr>
                        <td>{{ $req->organisasi->idOrganisasi }}</td>
                        <td>{{ $req->organisasi->namaOrganisasi }}</td>
                        <td>{{ $req->organisasi->alamat }}</td>
                        <td>{{ $req->barangRequest }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
