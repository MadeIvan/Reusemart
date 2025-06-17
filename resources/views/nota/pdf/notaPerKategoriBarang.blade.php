{{-- filepath: c:\xampp\htdocs\ReUseMart\Reusemart\resources\views\nota\pdf\notaPerKategoriBarang.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #222; padding: 6px 8px; }
        th { background: #eee; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div>
        <div style="font-weight:bold;">ReUse Mart</div>
        <div>Jl. Green Eco Park No. 456 Yogyakarta</div>
        <br>
        <div class="text-bold" style="font-size:16px;">LAPORAN PENJUALAN PER KATEGORI BARANG</div>
        <div>Tahun : {{ $tahun }}</div>
        <div>Tanggal cetak: {{ $tanggalCetak }}</div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Jumlah item terjual</th>
                    <th>Jumlah item gagal terjual</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataKategori as $row)
                <tr>
                    <td>{{ $row['nama'] }}</td>
                    <td class="text-center">{{ $row['terjual'] }}</td>
                    <td class="text-center">{{ $row['gagal'] }}</td>
                </tr>
                @endforeach
                <tr>
                    <td class="text-bold">Total</td>
                    <td class="text-center text-bold">{{ $totalTerjual }}</td>
                    <td class="text-center text-bold">{{ $totalGagal }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>