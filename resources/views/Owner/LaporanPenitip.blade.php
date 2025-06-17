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
            <h3>Laporan Transaksi Penitip </h3>
            <div>
                <button class="btn btn-outline-secondary btn-sm" onclick="refreshData()" title="Refresh Data">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Menampilkan Barang Penitip yang Telah Terjual 
        </div>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
                <select id="selectPenitip" class="form-select form-select-sm" style="width: 140px;">
                    <option value="">Pilih Penitip</option>
                </select>
                <select id="selectBulan" class="form-select form-select-sm" style="width: 140px;">
                    <option value="">Pilih Bulan</option>
                </select>
                <select id="selectTahun" class="form-select form-select-sm" style="width: 140px;">
                    <option value="">Pilih Tahun</option>
                </select>
            </div>

            <button class="btn btn-success" onclick="downloadLaporan()" id="downloadButton" disabled>
                <i class="bi bi-file-earmark-arrow-down"></i> Download
            </button>
        </div>


        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Laku</th>
                        <th>Harga Jual Bersih (Sudah dipotong Komisi)</th>
                        <th>BonusTerjual Cepat</th>
                        <th>Pendapatan</th>
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
        let allPenitip = [];
        // let selectedPenitip = null;
        // let filteredData = [];  // Data setelah filter tahun

         document.addEventListener("DOMContentLoaded", function () {
            if (!localStorage.getItem("auth_token")) {
                window.location.href = "/PegawaiLogin";
                return;
            }

            // showLoading(true);
            // fetchRequest();
            fetchPenitip();
            // populateYearOptions();

            document.getElementById("dataTable").innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Silakan pilih penitip untuk melihat data.
                    </td>
                </tr>
            `;

            document.getElementById('selectPenitip').addEventListener('change', function () {
                const selectedPenitip = this.value;
                if(selectedPenitip){
                    fetchRequest(selectedPenitip);
                    checkDownloadEnable();


                }else{
                    document.getElementById("dataTable").innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Silakan pilih penitip untuk melihat data.
                        </td>
                    </tr>
                `;
                }
            });
        });

        function checkDownloadEnable() {
            const tahun = document.getElementById("selectTahun").value;
            const bulan = document.getElementById("selectBulan").value;
            const downloadBtn = document.getElementById("downloadButton");

            downloadBtn.disabled = !(tahun && bulan);
        }
        

        document.getElementById('selectTahun').addEventListener('change', ()=> {filterAndRender(); checkDownloadEnable() });
        document.getElementById('selectBulan').addEventListener('change', ()=> {filterAndRender(); checkDownloadEnable() });

        async function fetchRequest(idPenitip) {
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/laporanPenitip/${idPenitip}`, {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                },
                });
                const data = await response.json();
                console.log("Data dari API:", data);
                if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
                }

                

                if (data.status === "success") {
                    allData = data.data;
                    filterAndRender();
                    populateYearDropdown(allData);
                    populateMonthDropdown(allData);
                } else {
                    alert("Gagal memuat data transaksi pembelian barang penitip.");
                    console.error("Error loading data:", data);
                    showError("data transaksi pembelian barang penitip. kosong atau gagal dimuat.");
                }
            } catch (error) {
                console.error("Error fetching data transaksi pembelian barang penitip.:", error);
                alert("Terjadi kesalahan saat mengambil data.");
                showError(error.message);
            } finally {
                showLoading(false);
            }
        }

        async function fetchPenitip(){
            try{
                const response = await fetch(`http://127.0.0.1:8000/api/penitip`, {
                    method: "GET",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json",
                    },
                });

                const result = await response.json();

                if (result.status === "success") {
                    allPenitip = result.data;
                    populatePenitipDropdown(allPenitip);
                    
                } else {
                    console.error('Gagal memuat data penitip');
                }

            }catch (error) {
                console.error('Gagal mengambil data penitip:', error);
            } finally {
                showLoading(false);
            }

        }

        function populatePenitipDropdown(data) {
            const selectPenitip = document.getElementById('selectPenitip');
            selectPenitip.innerHTML = '<option value="">Pilih Penitip</option>';

            data.forEach(penitip => {
                const option = document.createElement('option');
                option.value = penitip.idPenitip;
                option.textContent = penitip.namaPenitip;
                selectPenitip.appendChild(option);
            });
        }

        function populateYearDropdown(data) {
            const yearSet = new Set();

            data.forEach(row => {
                const tanggalPembelian = row.barang.detail_transaksi_pembelian[0];
                if (tanggalPembelian && tanggalPembelian.transaksi_pembelian?.tanggalWaktuPembelian) {
                    const year = new Date(tanggalPembelian.transaksi_pembelian.tanggalWaktuPembelian).getFullYear();
                    yearSet.add(year);
                }
            });

            const selectYear = document.getElementById('selectTahun');
            selectYear.innerHTML = '<option value="">Pilih Tahun</option>';

            Array.from(yearSet).sort().forEach(year => {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                selectYear.appendChild(option);
            });
        }

        function populateMonthDropdown(data) {
            const monthSet = new Set();

            data.forEach(row => {
                const detail = row.barang.detail_transaksi_pembelian[0];
                if (detail && detail.transaksi_pembelian?.tanggalWaktuPembelian) {
                    const month = new Date(detail.transaksi_pembelian.tanggalWaktuPembelian).getMonth() + 1;
                    monthSet.add(month);
                }
            });

            const selectMonth = document.getElementById('selectBulan');
            selectMonth.innerHTML = '<option value="">Pilih Bulan</option>';

            Array.from(monthSet).sort((a, b) => a - b).forEach(month => {
                const option = document.createElement('option');
                option.value = month;
                option.textContent = new Date(0, month - 1).toLocaleString('id-ID', { month: 'long' });
                selectMonth.appendChild(option);
            });
        }


        function filterAndRender() {
            const selectedYear = document.getElementById("selectTahun").value;
            const selectedMonth = document.getElementById("selectBulan").value;

            let filtered = allData;

            if (selectedYear || selectedMonth) {
                filtered = allData.filter(row => {
                    const tanggal = row.barang?.detail_transaksi_pembelian?.[0]?.transaksi_pembelian?.tanggalWaktuPembelian;
                    if (!tanggal) return false;

                    const date = new Date(tanggal);
                    const year = date.getFullYear().toString();
                    const month = (date.getMonth() + 1).toString(); // 1 - 12

                    const matchYear = selectedYear ? year === selectedYear : true;
                    const matchMonth = selectedMonth ? month === selectedMonth : true;

                    return matchYear && matchMonth;
                });
            }

            renderTable(filtered);
        }    

        function renderTable(data) {
            const bodyRequest = document.getElementById("dataTable");
            bodyRequest.innerHTML = "";

            if (!data || data.length === 0) {
                bodyRequest.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                             Tidak ada data transaksi penitipan yang terjual.
                        </td>
                    </tr>
                `;
                return;
            }

            data.forEach(row => {
                const barang = row.barang || {};
                const tanggalMasukDate = new Date(row.tanggalMasuk);
                const tanggalMasuk = tanggalMasukDate.toLocaleDateString('id-ID');

                const transaksiPembelian = barang.detail_transaksi_pembelian?.[0]?.transaksi_pembelian;
                const tanggalLakuDate = transaksiPembelian?.tanggalWaktuPembelian
                    ? new Date(transaksiPembelian.tanggalWaktuPembelian)
                    : null;
                const tanggalLaku = tanggalLakuDate ? tanggalLakuDate.toLocaleDateString('id-ID') : '-';

                let hargaJualBersih = 0;
                let bonusTerjualCepat = 0;
                let pendapatan = 0;

                if (tanggalLakuDate) {
                    const selisihHari = Math.floor((tanggalLakuDate - tanggalMasukDate) / (1000 * 60 * 60 * 24));
                    
                    if (selisihHari < 7) {
                        hargaJualBersih = (barang.hargaBarang * 0.8) || 0;
                        bonusTerjualCepat = (barang.hargaBarang * 0.2) * 0.1;
                        pendapatan = hargaJualBersih + bonusTerjualCepat;
                    } else if (selisihHari < 30) {
                        hargaJualBersih = (barang.hargaBarang * 0.8) || 0;
                        pendapatan = hargaJualBersih;
                    } else if (selisihHari < 60 && selisihHari >= 30) {
                        hargaJualBersih = (barang.hargaBarang * 0.7) || 0;
                        pendapatan = hargaJualBersih;
                    }
                }

                const htmlRow = `
                    <tr>
                        <td>${barang.idBarang || '-'}</td>
                        <td>${barang.namaBarang || '-'}</td>
                        <td>${tanggalMasuk}</td>
                        <td>${tanggalLaku}</td>
                        <td>Rp ${hargaJualBersih.toLocaleString('id-ID')}</td>
                        <td>Rp ${bonusTerjualCepat.toLocaleString('id-ID')}</td>
                        <td>Rp ${pendapatan.toLocaleString('id-ID')}</td>
                    </tr>
                `;
                bodyRequest.innerHTML += htmlRow;
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
            const selected = document.getElementById('selectPenitip').value;
            if (selected) fetchRequest(selected);
        }

        function downloadLaporan() {
            const idPenitip = document.getElementById("selectPenitip").value;
            const tahun = document.getElementById("selectTahun").value;
            const bulan = document.getElementById("selectBulan").value;
            let url = "{{ route('nota.pdf.laporanUntukPenitip') }}";

            if (idPenitip && tahun && bulan) {
                url += `?idPenitip=${idPenitip}&tahun=${tahun}&bulan=${bulan}`;
            }else if(idPenitip && tahun) {
                url += `?idPenitip=${idPenitip}&tahun=${tahun}`;
            }else if(idPenitip && bulan) {
                url += `?idPenitip=${idPenitip}&bulan=${bulan}`;
            }else if (idPenitip) {
                url += `?idPenitip=${idPenitip}`;
            }
            window.open(url, "_blank");
        }
    </script>
</body>
</html>