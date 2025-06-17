{{-- filepath: c:\xampp\htdocs\ReUseMart\Reusemart\resources\views\nota\pdf\laporanPenitipanHabis.blade.php --}}
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
        <div class="text-bold" style="font-size:16px;">LAPORAN Barang yang Masa Penitipannya Sudah Habis</div>
        <div>Tanggal cetak: {{ $tanggalCetak }}</div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Id Penitip</th>
                    <th>Nama Penitip</th>
                    <th>Tanggal Masuk</th>
                    <th>Tanggal Akhir</th>
                    <th>Batas Ambil</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr>
                    <td>{{ $row['kode_produk'] }}</td>
                    <td>{{ $row['nama_produk'] }}</td>
                    <td>{{ $row['id_penitip'] }}</td>
                    <td>{{ $row['nama_penitip'] }}</td>
                    <td class="text-center">{{ $row['tanggal_masuk'] }}</td>
                    <td class="text-center">{{ $row['tanggal_akhir'] }}</td>
                    <td class="text-center">{{ $row['batas_ambil'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
                <tr>
                    <td>....</td>
                    <td>....</td>
                    <td>....</td>
                    <td>....</td>
                    <td>....</td>
                    <td>....</td>
                    <td>....</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>