<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Profil Pegawai - ReUseMart</title>
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    crossorigin="anonymous"
  />
  <!-- Toastify CSS -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css"
  />
</head>
<body>
      @include('layouts.navbar')

   <div class="container my-5 p-4 bg-white rounded shadow-sm" style="max-width: 1000px;">
        <h2 class="text-center text-success mb-4">Laporan Penjualan per Kategori</h2>
        <div class="d-flex justify-content-center mb-4 gap-2">
            <button id="btn-json" class="btn btn-success">Tampilkan Data Web</button>
            <a href="{{ route('pegawai.laporanPerKategoriBarang') }}" target="_blank" class="btn btn-outline-success">Tampilkan PDF</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="kategori-table">
                <thead>
                    <tr style="background-color: #d4ead3;">
                        <th class="fw-bold">Kategori</th>
                        <th class="fw-bold">Jumlah Item Terjual</th>
                        <th class="fw-bold">Jumlah Item Gagal Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by JS -->
                </tbody>
                <tfoot>
                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                        <td>Total</td>
                        <td id="total-terjual">....</td>
                        <td id="total-gagal">....</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Default: load JSON data
    loadJsonData();

    document.getElementById('btn-json').addEventListener('click', function() {
        loadJsonData();
    });

    function loadJsonData() {
        fetch('http://127.0.0.1:8000/api/laporan-kategori')
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#kategori-table tbody');
                tbody.innerHTML = '';
                data.data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.nama}</td>
                        <td>${row.terjual}</td>
                        <td>${row.gagal}</td>
                    `;
                    tbody.appendChild(tr);
                });
                document.getElementById('total-terjual').textContent = data.total_terjual > 0 ? data.total_terjual : '....';
                document.getElementById('total-gagal').textContent = data.total_gagal > 0 ? data.total_gagal : '....';
            });
    }
});
</script>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <!-- Toastify JS -->
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</body>
</html>