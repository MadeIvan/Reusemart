<!DOCTYPE html>
<html lang="en">
<head>
     <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (!localStorage.getItem("auth_token")) {
                window.location.href = "{{ url('/PegawaiLogin') }}";
            }
        });
    </script>

    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reusemart</title>
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
    @include('layouts.navbar')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Jumlah Penjualan Bulanan</h3>
            <div>
                <button class="btn btn-outline-secondary btn-sm" onclick="refreshData()" title="Refresh Data">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>
        <div class="mb-3" style="text-align: right; margin-bottom: 1rem;">
            <button class="btn btn-success" onclick="downloadLaporan()" id="donasiButton"> <i class="bi bi-file-earmark-arrow-down"></i> Download</button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>Bulan</th>
                        <th>Jumlah Barang Terjual</th>
                        <th>Jumlah Penjualan Kotor</th>


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
         document.addEventListener("DOMContentLoaded", function () {
            if (!localStorage.getItem("auth_token")) {
                window.location.href = "/PegawaiLogin";
                return;
            }

            showLoading(true);
            fetchRequest();
        });

       async function fetchRequest() {
        try {
            const response = await fetch("http://127.0.0.1:8000/api/penjualanBulanan", {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log("Data dari API:", data);

            // Debugging: Check the structure of the data
            console.log("Data structure:", JSON.stringify(data));

            // Check if data.data exists and is an array
            if (Array.isArray(data)) {
                const dataRequest = data;
                console.log(dataRequest);
                if (dataRequest.length > 0) {
                    renderTable(dataRequest);
                } else {
                    alert("Tidak ada barang dengan status Tersedia.");
                    console.error("No available items with status Tersedia.");
                    showError("Tidak ada barang dengan status Tersedia.");
                }
            } else {
                alert("Data tidak valid atau tidak ditemukan.");
                console.error("Invalid data:", data);
                showError("Data dari API tidak valid.");
            }
        } catch (error) {
            console.error("Error fetching request data:", error);
            alert("Terjadi kesalahan saat mengambil data.");
            showError(error.message);
        } finally {
            showLoading(false);
        }
    }



        function renderTable(data) {
        const bodyRequest = document.getElementById('dataTable');
        bodyRequest.innerHTML = ''; // Clear the table before rendering new data

        data.forEach(item => {
                    const row = document.createElement("tr");
                    const tanggalPenitipan = new Date(item.transaksiPenitipan?.tanggalPenitipan);
                    const tanggalPenitipanSelesai = new Date(item.transaksiPenitipan?.tanggalPenitipanSelesai);
                    const differenceInMilliseconds = tanggalPenitipanSelesai - tanggalPenitipan;
                    const differenceInDays = differenceInMilliseconds / (1000 * 3600 * 24);
                    const status = differenceInDays > 30 ? "Ya" : "Tidak";
                    row.innerHTML = `
                        <td>${item.month|| '-'}</td>
                        <td>${item.Jumlah|| '-'}</td>
                        <td>${item.total_sum|| '0'}</td>

                    `;
                    
                    bodyRequest.appendChild(row);
                });
    }

        function showLoading(show) {
            const bodyRequest = document.getElementById('dataTable');
            if (show) {
                bodyRequest.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-4">
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
            const bodyRequest = document.getElementById('dataTable');
            bodyRequest.innerHTML = `
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

        // Add refresh button functionality
        function refreshData() {
            showLoading(true);
            fetchRequest();
        }

        function downloadLaporan() {
            window.open("{{ route('nota.pdf.laporanPenjualan') }}", "_blank");
        }

    </script>
</body>
</html>