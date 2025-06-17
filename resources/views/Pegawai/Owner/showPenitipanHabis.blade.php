<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Penitipan Habis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    @include('layouts.navbar')
    <div class="container my-5 p-4 bg-white rounded shadow-sm" style="max-width: 1100px;">
        <h2 class="text-center text-success mb-4">Daftar Penitipan Habis</h2>
        <div class="d-flex justify-content-center mb-4">
    <a href="{{ route('pegawai.laporanPenitipanHabis') }}" target="_blank" class="btn btn-outline-success">
        Tampilkan PDF
    </a>
</div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="penitipan-table">
                <thead>
                    <tr style="background-color: #d4ead3;">
                        <th class="fw-bold">Kode Produk</th>
                        <th class="fw-bold">Nama Produk</th>
                        <th class="fw-bold">Id Penitip</th>
                        <th class="fw-bold">Nama Penitip</th>
                        <th class="fw-bold">Tanggal Masuk</th>
                        <th class="fw-bold">Tanggal Akhir</th>
                        <th class="fw-bold">Batas Ambil</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by JS -->
                </tbody>
            </table>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch("{{ url('/api/penitipan-habis') }}")
            .then(response => response.json())
            .then(result => {
                const tbody = document.querySelector('#penitipan-table tbody');
                tbody.innerHTML = '';
                if(result.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>`;
                } else {
                    result.data.forEach(row => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${row.kode_produk ?? '....'}</td>
                                <td>${row.nama_produk ?? '....'}</td>
                                <td>${row.id_penitip ?? '....'}</td>
                                <td>${row.nama_penitip ?? '....'}</td>
                                <td>${row.tanggal_masuk ?? '....'}</td>
                                <td>${row.tanggal_akhir ?? '....'}</td>
                                <td>${row.batas_ambil ?? '....'}</td>
                            </tr>
                        `;
                    });
                }
            });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>