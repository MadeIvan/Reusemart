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
                Kurir ReUseMart ({{ $transaksi->pegawai3->namaPegawai ?? '-' }})

            @else
                - (diambil sendiri)
            @endif
        </div>

        @php
            // Calculate ongkir based on your business logic
            if (is_null($transaksi->idAlamat)) {
                // Ambil sendiri
                $ongkir = 0;
            } else {
                // Delivery - fixed logic
                if ($transaksi->totalHarga > 1500000) {
                    $ongkir = 0; // Free shipping for orders > 1.5M
                } else {
                    $ongkir = 100000; // Standard shipping fee
                }
            }

            // Calculate point discount - correct property name
            if (is_null($transaksi->pointRedemption) || is_null($transaksi->pointRedemption->points_used) || $transaksi->pointRedemption->points_used == 0) {
                $HargaPoin = 0;
            } else {
                $HargaPoin = 100 * $transaksi->pointRedemption->points_used;
            }

            // Progressive totals for display
            // Total1: Original item total (totalHarga already contains sum of all barang, but we need to subtract ongkir and add back HargaPoin to get pure item total)
            $total1 = ($transaksi->totalHarga ?? 0) - $ongkir + $HargaPoin;
            
            // Total2: After adding ongkir
            $total2 = $total1 + $ongkir;
            
            // Total3: Final total after subtracting points
            $total3 = $total2 - $HargaPoin;
        @endphp

        <table width="100%" style="margin-top:10px;">
            {{-- Display individual items --}}
            @foreach($transaksi->detailTransaksiPembelian as $item)
                <tr>
                    <td>{{ $item->barang->namaBarang ?? '-' }}</td>
                    <td class="right">{{ number_format($item->barang->hargaBarang ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach 

            {{-- Total1: Just adding the barang harga --}}
            <tr>
                <td class="bold">Total</td>
                <td class="right bold">{{ number_format($total1, 0, ',', '.') }}</td>
            </tr>
                    
            {{-- Add ongkir --}}
            <tr>
                <td>Ongkir</td>
                <td class="right">{{ number_format($ongkir, 0, ',', '.') }}</td>
            </tr>
            
            {{-- Total2: After adding ongkir --}}
            <tr>
                <td class="bold">Total</td>
                <td class="right bold">{{ number_format($total2, 0, ',', '.') }}</td>
            </tr>

            {{-- Point discount (if any) --}}

            <tr>
                <td>Potongan Poin ({{ number_format($HargaPoin / 100, 0, ',', '.') }} poin)</td>
                <td class="right">- {{ number_format($HargaPoin, 0, ',', '.') }}</td>
            </tr>


            {{-- Total3: Final total after subtracting points --}}
            <tr>
                <td class="bold">Total</td>
                <td class="right bold">{{ number_format($total3, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div style="margin-top: 8px;">
            Poin dari pesanan ini: 
            {{
            $transaksi->totalHarga < 500000
                ? floor($transaksi->totalHarga / 10000)
                : floor(($transaksi->totalHarga / 10000)+(($transaksi->totalHarga / 10000)*0.2))
            }}
        </div>
        <div>Total poin customer: {{ $transaksi->pembeli->poin ?? 0 }}</div>

        <div style="margin-top: 8px;">QC oleh: {{ $transaksi->pegawai->namaPegawai ?? '-' }} ({{ $transaksi->pegawai->idPegawai ?? '' }})</div>

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