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
    @include('layouts.navbar')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Daftar Transaksi Donasi</h3>
            <div>
                <button class="btn btn-outline-secondary btn-sm" onclick="refreshData()" title="Refresh Data">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Menampilkan Transaksi Donasi yang Telah Diselesaikan 
        </div>

        <div class="mb-3 d-flex  justify-content-between align-items-center gap-2">
            <select id="selectYear" class="form-select form-select-sm" style="width: 140px;">
                <option value="">Semua Tahun</option>
            </select>
            <button class="btn btn-success" onclick="downloadLaporan()" id="donasiButton"> <i class="bi bi-file-earmark-arrow-down"></i> Download</button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Id Penitip</th>
                        <th>Nama Penitip</th>
                        <th>Tanggal Donasi</th>
                        <th>Organisasi</th>
                        <th>Nama Penerima</th>
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
        let allData = [];  // Data asli dari API
        // let filteredData = [];  // Data setelah filter tahun

         document.addEventListener("DOMContentLoaded", function () {
            if (!localStorage.getItem("auth_token")) {
                window.location.href = "/PegawaiLogin";
                return;
            }

            showLoading(true);
            fetchRequest();
            // populateYearOptions();

            document.getElementById('selectYear').addEventListener('change', function () {
                const selectedYear = this.value;
                filterAndRender(selectedYear);
            });
        });

        async function fetchRequest() {
            try {
                const response = await fetch("http://127.0.0.1:8000/api/requestDonasi-accepted", {
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

                if (data.status === "success" && data.data.length > 0) {
                    // const dataRequest = data.data.filter(row => row.status === "Diterima");
                    allData = data.data.filter(row => row.status === "Diterima");
                    // populateYearDropdown(dataRequest);
                    populateYearDropdown(allData);
                    // renderTable(dataRequest);
                    filterAndRender();
                } else {
                    alert("Gagal memuat data request donasi.");
                    console.error("Error loading data:", data);
                    showError("Data request donasi kosong atau gagal dimuat.");
                }
            } catch (error) {
                console.error("Error fetching request data:", error);
                alert("Terjadi kesalahan saat mengambil data.");
                showError(error.message);
            } finally {
                showLoading(false);
            }
        }

        function populateYearDropdown(data) {
            const yearSet = new Set();

            data.forEach(row => {
                const tanggalDonasi = row.transaksi_donasi?.tanggalDonasi;
                if (tanggalDonasi) {
                    const year = new Date(tanggalDonasi).getFullYear();
                    yearSet.add(year);
                }
            });

            const selectYear = document.getElementById('selectYear');
            selectYear.innerHTML = '<option value="">Semua Tahun</option>'; // default pilihan semua tahun

            [...yearSet].sort((a,b) => b - a).forEach(year => {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                selectYear.appendChild(option);
            });
        }

        function filterAndRender(year = "") {
            if (year) {
                filteredData = allData.filter(row => {
                    const transaksiDonasi = row.transaksiDonasi || row.transaksi_donasi;
                    if (!transaksiDonasi || !transaksiDonasi.tanggalDonasi) return false;
                    const rowYear = new Date(transaksiDonasi.tanggalDonasi).getFullYear();
                    return rowYear.toString() === year.toString();
                });
            } else {
                filteredData = [...allData];
            }

            filteredData.sort((a, b) => {
                const dateA = new Date(a.transaksi_donasi?.tanggalDonasi || a.transaksiDonasi?.tanggalDonasi || '-');
                const dateB = new Date(b.transaksi_donasi?.tanggalDonasi || b.transaksiDonasi?.tanggalDonasi || '-');
                return dateB - dateA; // descending
            });
            renderTable(filteredData);
        }
        


        function renderTable(data) {
            const bodyRequest = document.getElementById("dataTable");
            bodyRequest.innerHTML = "";

            if (!data || data.length === 0) {
                bodyRequest.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada transaksi dengan status "Diterima..."
                        </td>
                    </tr>
                `;
                return;
            }

            data.forEach(row => {
                function formatTanggal(tanggalISO) {
                    if (!tanggalISO) return '-';
                    const date = new Date(tanggalISO);
                    // Format: hari/bulan/tahun tanpa leading zero
                    return date.toLocaleDateString('id-ID');
                }

                const tanggalDonasi = formatTanggal(row.transaksi_donasi?.tanggalDonasi);

                if(row.transaksi_donasi.barang.detail_transaksi_penitipan.transaksi_penitipan.pegawai2 !== null){
                    const htmlRow = `
                        <tr>
                            <td>${row.transaksi_donasi.idBarang || '-'}</td>
                            <td>${row.transaksi_donasi.barang.namaBarang || '-'}</td>
                            <td>${row.transaksi_donasi.barang.detail_transaksi_penitipan.transaksi_penitipan.idPenitip || '-'}</td>
                            <td>${row.transaksi_donasi.barang.detail_transaksi_penitipan.transaksi_penitipan.penitip.namaPenitip || '-'}</td>
                            <td>${tanggalDonasi}</td>
                            <td>${row.organisasi.namaOrganisasi || '-'}</td>
                            <td>${row.transaksi_donasi.namaPenerima || '-'}</td>
                        </tr>
                    `;
                    bodyRequest.innerHTML += htmlRow;
                }
                
            });
        }

        function showLoading(show) {
            const bodyRequest = document.getElementById('dataTable');
            if (show) {
                bodyRequest.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4">
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
                    <td colspan="7" class="text-center py-4">
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
            const selectedYear = document.getElementById("selectYear").value;
            let url = "{{ route('nota.pdf.laporanTransaksiDonasi') }}";

            if (selectedYear) {
                url += `?year=${selectedYear}`;
            }

            window.open(url, "_blank");

            // window.open("{{ route('nota.pdf.laporanTransaksiDonasi') }}", "_blank");
        }

    </script>
</body>
</html>