<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Penjadwalan Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        table.table-bordered > :not(caption) > * > * {
            border-width: 1px 1px;
        }
        td, th {
            vertical-align: middle !important;
        }
        .status-badge {
            font-size: 0.8em;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Sistem Penjadwalan</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Daftar Penjadwalan Barang</h3>
            <div>
                <button class="btn btn-outline-secondary btn-sm" onclick="refreshData()" title="Refresh Data">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Menampilkan transaksi dengan status "Lunas Siap..."
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>No Nota</th>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Status Barang</th>
                        <th>Nama Pembeli</th>
                        <th>Alamat</th>
                        <th>Total Harga</th>
                        <th>Status Transaksi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="dataTable">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let data = [];
        let isLoading = true;

        // Fetch data from API
        async function fetchData() {
            try {
                showLoading(true);
                const response = await fetch('http://127.0.0.1:8000/api/dibayar');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                // Filter only transactions with status starting with "Lunas Siap"
                data = result.filter(row => row.status && row.status.startsWith('Menunggu Verifikasi'));
                
                isLoading = false;
                showLoading(false);
                renderTable();
                
                // Update info banner
                updateInfoBanner();
                
            } catch (error) {
                console.error('Error fetching data:', error);
                isLoading = false;
                showLoading(false);
                showError(error.message);
            }
        }

        function showLoading(show) {
            const tableBody = document.getElementById('dataTable');
            if (show) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="mt-2">Memuat data...</div>
                        </td>
                    </tr>
                `;
            }
        }

        function showError(message) {
            const tableBody = document.getElementById('dataTable');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <div class="alert alert-danger d-inline-block">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Error:</strong> ${message}
                            <br><small>Pastikan server Laravel berjalan di http://127.0.0.1:8000</small>
                        </div>
                        <div class="mt-2">
                            <button class="btn btn-primary btn-sm" onclick="fetchData()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Coba Lagi
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        function updateInfoBanner() {
            const infoBanner = document.querySelector('.alert-info');
            infoBanner.innerHTML = `
                <i class="bi bi-info-circle me-2"></i>
                Menampilkan <strong>${data.length}</strong> transaksi dengan status "Menunggu Verifikasi..."
            `;
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        function getStatusBadge(status) {
            // Since we're only showing "Lunas Siap" statuses, show them as success
            return '<span class="badge bg-success status-badge">' + status + '</span>';
        }

        function renderTable(filteredData = data) {
            const tableBody = document.getElementById('dataTable');
            
            if (isLoading) {
                return; // Don't render while loading
            }
            
            if (filteredData.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada transaksi dengan status "Menunggu Verifikasi..."
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = filteredData.map(row => {
                // Handle missing or malformed data gracefully
                // const status = row.status || 'Unknown';
                const details = row.detail_transaksi_pembelian || [];
                const idBarangList = details.map(item => item.idBarang || '-');
                const namaBarangList = details.map(item => item.barang?.namaBarang || '-');
                const statusList = details.map(item => item.status || '-');
                
                let alamat = '-';
                if (row.pembeli?.alamat && Array.isArray(row.pembeli.alamat) && row.pembeli.alamat.length > 0) {
                    if (row.idAlamat) {
                        const foundAlamat = row.pembeli.alamat.find(a => a.idAlamat === row.idAlamat);
                        alamat = foundAlamat?.alamat || row.pembeli.alamat[0].alamat || '-';
                    } else {
                        alamat = row.pembeli.alamat[0].alamat || '-';
                    }
                }

                const namaPembeli = row.pembeli?.namaPembeli || '-';
                const totalHarga = row.totalHarga || 0;
                const status = row.status || 'Unknown';

                return `
                    <tr>
                        <td><strong>${row.noNota || '-'}</strong></td>
                        <td><code>${idBarangList.join(', ')}</code></td>
                        <td>${namaBarangList.join(', ')}</td>
                        <td>${status.join(', ')}</td>
                        <td>${namaPembeli}</td>
                        <td><small>${alamat}</small></td>
                        <td><strong>${formatCurrency(totalHarga)}</strong></td>
                        <td>${getStatusBadge(status)}</td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <button class="btn btn-warning btn-sm" title="Download Nota" onclick="downloadNota('${row.noNota}')">
                                    <i class="bi bi-receipt"></i>
                                </button>
                                <button class="btn btn-primary btn-sm" title="Jadwalkan" onclick="schedule('${row.noNota}')">
                                    <i class="bi bi-calendar-plus"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function filterStatus(type) {
            let filteredData;
            
            switch(type) {
                case 'Menunggu Verifikasi':
                    filteredData = data.filter(row => row.status.includes('Menunggu Verifikasi'));
                    break;
                default:
                    filteredData = data;
            }
            
            renderTable(filteredData);
        }

        function downloadNota(noNota) {
            alert(`Mengunduh nota: ${noNota}`);
        }

        function schedule(noNota) {
            if (confirm(`Jadwalkan pengiriman untuk nota ${noNota}?`)) {
                alert(`Penjadwalan untuk nota ${noNota} berhasil!`);
                // Update status in real implementation
            }
        }

        // Initialize: Fetch data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            fetchData();
        });

        // Add refresh button functionality
        function refreshData() {
            fetchData();
        }
    </script>
</body>
</html>