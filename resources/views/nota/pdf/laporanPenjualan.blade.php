<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reusemart - Laporan Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; }
        .nota-box { border: 1px solid #222; padding: 18px 22px 12px 22px; margin-top: 14px; }
        .header { font-size: 16px; font-weight: bold; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .sign-box { margin-top: 25px; }

        .chart {
            display: flex;
            align-items: flex-end;
            height: 300px; /* Adjust height to accommodate larger values */
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .bar {
            width: 30px;
            margin-right: 10px;
            background-color: #4CAF50;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .bar span {
            color: white;
            font-size: 10px;
        }

        .month-label {
            text-align: center;
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="nota-box">
        <div class="header">ReUse Mart</div>
        <div style="margin-bottom: 12px;">Jl. Green Eco Park No. 456 Yogyakarta</div>

        <div class="header" style="text-decoration: underline;">Laporan Penjualan Bulanan </div>
        <div>Tahun :  {{ now()->locale('id')->translatedFormat('Y') }}</div>
        <div style="margin-bottom: 12px;">Tanggal cetak: {{ now()->locale('id')->translatedFormat('d F Y') }}</div>

        <!-- Table displaying sales data -->
        <table style="border-collapse: collapse; width: 100%;" border="1" cellspacing="0" cellpadding="6">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Produk</th>
                    <th>Jumlah Penjualan Kotor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($result as $bar)
                    <tr>
                        <td>{{ \Carbon\Carbon::createFromFormat('m', $bar['month'])->format('F') }}</td>
                        <td>{{ $bar['jumlah'] ?? '-' }}</td>
                        <td>{{ number_format($bar['total_sum'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Bar chart displaying total_sum per month -->
        <div class="chart">
            @foreach ($result as $bar)
                <div class="bar" style="height: {{ $bar['total_sum'] / 200000 }}px;">
                    <span>{{ $bar['total_sum'] }}</span>
                </div>
            @endforeach
        </div>

        
    </div>
</body>
</html>
