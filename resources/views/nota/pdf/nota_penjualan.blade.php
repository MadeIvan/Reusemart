<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Penjualan</title>
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
    <div style="font-size: 20px; font-weight: bold; margin-bottom: 9px;">
        Nota Penjualan ({{ $transaksi->idAlamat ? 'dibawa oleh kurir' : 'diambil oleh pembeli' }})
    </div>

    <div class="nota-box">
        <div class="header">ReUse Mart</div>
        <div style="margin-bottom: 12px;">Jl. Green Eco Park No. 456 Yogyakarta</div>
        <table>
            <tr>
                <td>No Nota</td><td>:</td>
                <td>{{ $transaksi->noNota }}</td>
            </tr>
            <tr>
                <td>Tanggal pesan</td><td>:</td>
                <td>{{ \Carbon\Carbon::parse($transaksi->tanggalWaktuPembelian)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Lunas pada</td><td>:</td>
                <td>{{ \Carbon\Carbon::parse($transaksi->tanggalWaktuPelunasan)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>
                  @if($transaksi->idAlamat)
                    Tanggal kirim
                  @else
                    Tanggal ambil
                  @endif
                </td>
                <td>:</td>
                <td>{{ $transaksi->tanggalPengirimanPengambilan ? \Carbon\Carbon::parse($transaksi->tanggalPengirimanPengambilan)->format('d/m/Y') : '-' }}</td>
            </tr>
        </table>

        <div class="bold" style="margin-top: 10px;">
            Pembeli : {{ $transaksi->pembeli->email ?? '-' }} / {{ $transaksi->pembeli->namaPembeli ?? '-' }}
        </div>
        <div>
            {{ $transaksi->pembeli->alamat->first()->alamat ?? '-' }}
        </div>
        <div>
            Delivery: 
            @if($transaksi->idAlamat)
                Kurir ReUseMart ({{ $transaksi->kurir_nama ?? '-' }})
            @else
                - (diambil sendiri)
            @endif
        </div>

        <!-- ... lanjut tabel produk, total, dll, seperti sebelumnya ... -->

        <table width="100%" style="margin-top:10px;">
            @foreach($transaksi->detailTransaksiPembelian as $item)
                <tr>
                    <td>{{ $item->barang->namaBarang ?? '-' }}</td>
                    <td class="right">{{ number_format($item->barang->hargaBarang ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td class="bold">Total</td>
                <td class="right bold">{{ number_format($transaksi->totalHarga, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Ongkos Kirim</td>
                <td class="right">{{ number_format($transaksi->ongkir ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="bold">Total</td>
                <td class="right bold">{{ number_format(($transaksi->totalHarga ?? 0) + ($transaksi->ongkir ?? 0), 0, ',', '.') }}</td>
            </tr>
            @if(isset($transaksi->potongan_poin) && $transaksi->potongan_poin > 0)
            <tr>
                <td>Potongan {{ $transaksi->potongan_poin }} poin</td>
                <td class="right">- {{ number_format($transaksi->potongan_rupiah ?? 0, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td class="bold">Total</td>
                <td class="right bold">{{ number_format($transaksi->final_total ?? 0, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div style="margin-top: 8px;">Poin dari pesanan ini: {{ $transaksi->poin_dari_pesanan ?? 0 }}</div>
        <div>Total poin customer: {{ $transaksi->pembeli->poin ?? 0 }}</div>

        <div style="margin-top: 8px;">QC oleh: {{ $transaksi->pegawaiQc->namaPegawai ?? '-' }} ({{ $transaksi->pegawaiQc->idPegawai ?? '' }})</div>

        <div class="sign-box">
            @if($transaksi->idAlamat)
                Diterima oleh:
            @else
                Diambil oleh:
            @endif
            <br><br>
            (..........................................)<br>
            Tanggal: ...................................
        </div>
    </div>
</body>
</html>
