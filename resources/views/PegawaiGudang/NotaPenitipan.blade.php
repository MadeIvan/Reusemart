<!DOCTYPE html>
<html>
<head>
    <title>Nota Penitipan #{{ $transaksi->idTransaksiPenitipan }}</title>
    <style>
        /* Add your PDF styling here */
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .header { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Nota Penitipan</h2>
        <p>No Nota: {{ $transaksi->noNota }}</p>
        <p>Tanggal: {{ $transaksi->tanggalPenitipan ?? '-' }}</p>
        <p>Penitip: {{ $transaksi->penitip->nama_penitip ?? '-' }}</p>
        <p>Alamat: {{ $transaksi->penitip->alamat ?? '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->detailTransaksiPenitipan as $detail)
                <tr>
                    <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                    <td>{{ $detail->jumlah ?? 1 }}</td>
                    <td>{{ $detail->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Pegawai Penanggung Jawab: {{ $transaksi->pegawai->nama_pegawai ?? '-' }}</p>



    <p>Terima kasih atas kepercayaan Anda!</p>
</body>
</html>
