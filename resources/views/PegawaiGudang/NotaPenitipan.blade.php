{{-- resources/views/pdf/nota_penitipan.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Nota Penitipan Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
        }
        .box {
            border: 1px solid black;
            padding: 12px 20px;
            margin-top: 10px;
            width: 380px;
        }
        .header-bold {
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 0;
        }
        .subtitle {
            margin-top: 0;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .no-border td, .no-border th {
            border: none !important;
            padding: 2px 0;
        }
        .item-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .right-align {
            text-align: right;
        }
        .qc-text {
            margin-top: 40px;
            margin-bottom: 50px;
        }
    </style>
</head>
<body>
    <div>
        <strong>Nota Penitipan Barang</strong>
        <div class="box">
            <table class="no-border">
                <tr>
                    <td class="header-bold">ReUse Mart</td>
                </tr>
                <tr>
                    <td class="subtitle">Jl. Green Eco Park No. 456 Yogyakarta</td>
                </tr>
            </table>

            <table class="no-border" style="margin-top:10px;">
                <tr>
                    <td style="width: 120px;">No Nota</td>
                    <td>: {{ date('Y', strtotime($transaksiPenitipan->tanggalPenitipan)) }}.{{ date('m', strtotime($transaksiPenitipan->tanggalPenitipan)) }}.{{ $transaksiPenitipan->idTransaksiPenitipan }}</td>
                </tr>
                <tr>
                    <td>Tanggal penitipan</td>
                    <td>: {{ date('d/m/Y H:i:s', strtotime($transaksiPenitipan->tanggalPenitipan)) }}</td>
                </tr>
                <tr>
                    <td>Masa penitipan sampai</td>
                    <td>: {{ date('d/m/Y', strtotime($transaksiPenitipan->tanggalPenitipanSelesai)) }}</td>
                </tr>
            </table>

            <table class="no-border" style="margin-top:10px;">
                <tr>
                    <td><strong>Penitip :</strong> {{ $transaksiPenitipan->penitip->idPenitip }} / {{ $transaksiPenitipan->penitip->namaPenitip }}</td>
                </tr>
                <tr>
                    <td>{{ $transaksiPenitipan->penitip->alamat }}</td>
                </tr>
            </table>

            <table class="item-table" style="margin-top:15px;">
                @foreach ($detailTransaksiPenitipan as $detail)
                <tr>
                    <td style="width: 65%; vertical-align: top; display: flex; justify-content: space-between;">
                        <span>{{ $detail->barang->namaBarang }}</span>
                            <td></td>
    <td></td>
    <td></td>
                        <span style="min-width: 100px; text-align: left;">{{ number_format($detail->barang->hargaBarang, 0, ',', '.') }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding-left: 0;">
                        @if ($detail->barang->garansiBarang)
                            Garansi ON Juli 2025<br>
                        @endif
                        Berat barang: {{ $detail->barang->beratBarang }} kg
                    </td>
                </tr>
                @endforeach
            </table>

            <div class="qc-text">
                Diterima dan QC oleh:<br><br><br><br>
                {{ $pegawai->idPegawai }} - {{ $pegawai->namaPegawai }}
            </div>
        </div>
    </div>
</body>
</html>
