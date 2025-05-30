<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu Gudang</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Styling for table with scrollable body */
        #pegawaiTableContainer {
            max-height: 400px;
            overflow-y: auto;
        }

        /* Fix the header so it stays visible while scrolling */
        #pegawaiTable thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 1;
        }

        /* Center the "No data" message */
        .no-data-message {
            text-align: center;
            color: #6c757d;
        }

        /* Styling for form and buttons */
        .btn-container {
            display: flex;
            gap: 10px;
        }

        /* Styling for the floating form */
        .register-form-container {
            display: none;
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 10px;
            z-index: 999;
            width: 60%;
        }

        /* Overlay background */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 998;
            display: none;
        }

        /* Toast Styling */
        .toast-container {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>

    <div class="container mt-4">
        <h2>Menu Gudang</h2>

        <!-- Form for Pegawai data -->
        <form id="PegawaiForm">
            

            <div class="row">
                <div class="col-12 btn-container">
                    <button type="button" class="btn btn-success" id="addBarangButton">Tambah barang</button>
                    <button type="button" class="btn btn-danger" id="deleteButton">Delete</button>
                </div>
            </div>
        </form>
    </div>


    <div class="modal fade" id="addBarangModal" tabindex="-1" aria-labelledby="addBarangModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="addBarangModalLabel">Tambah Transaksi Penitipan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="addBarangForm">
            <div class="mb-3">
                <label for="idPegawai1" class="form-label">Petugas Gudang</label> //done
                <input type="text" class="form-control" id="idPegawai1" disabled>
            </div>
            <div class="mb-3">
                <label for="idPegawai2" class="form-label">Hunter</label>
                <select class="form-select" id="idPegawai2">
                    <option value=""> --- </option>
                </select>
            </div>
            <div class="mb-3">
                <label for="idPenitip" class="form-label">Penitip</label>
                <select class="form-select" id="idPenitip" required>
                    <option value=""> --- </option>
                </select>
            </div>
            <div class="mb-3">
                <label for="tanggalPenitipan" class="form-label">Tanggal Penitipan</label>
                <input type="date" class="form-control" id="tanggalPenitipan" required>
            </div>
            <div class="mb-3">
                <label for="tanggalPenitipanSelesai" class="form-label">Tanggal Penitipan Selesai</label>
                <input type="date" class="form-control" id="tanggalPenitipanSelesai" disabled >
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Next</button>
            </div>
            </form>
        </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="addBarangDetailModal" tabindex="-1" aria-labelledby="addBarangDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBarangDetailModalLabel">Tambah Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addBarangDetailForm">
                <div class="mb-3">
                    <label for="idBarang" class="form-label">ID Barang</label>
                    <input type="text" class="form-control" id="idBarang" required>
                </div>
                <!-- <div class="mb-3">
                    <label for="idTransaksiDonasi" class="form-label">ID Transaksi Donasi</label>
                    <input type="text" class="form-control" id="idTransaksiDonasi" >
                </div> -->
                <div class="mb-3">
                    <label for="namaBarang" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="namaBarang" required>
                </div>
                <div class="mb-3">
                    <label for="beratBarang" class="form-label">Berat Barang</label>
                    <input type="number" class="form-control" id="beratBarang" required>
                </div>
                <div class="mb-3">
                    <label for="garansiBarang" class="form-label">Garansi Barang</label>
                    <select class="form-select" id="garansiBarang" required>
                    <option value="1">Ya</option>
                    <option value="0">Tidak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="periodeGaransi" class="form-label">Periode Garansi (bulan)</label>
                    <input type="number" class="form-control" id="periodeGaransi">
                </div>
                <div class="mb-3">
                    <label for="hargaBarang" class="form-label">Harga Barang</label>
                    <input type="number" class="form-control" id="hargaBarang" required>
                </div>
                <div class="mb-3">
                    <label for="haveHunter" class="form-label">Barang dengan Hunter?</label>
                    <select class="form-select" id="haveHunter" required>
                    <option value="1">Ya</option>
                    <option value="0">Tidak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="statusBarang" class="form-label">Status Barang</label>
                    <input type="text" class="form-control" id="statusBarang" value="Tersedia" disabled>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Upload Gambar</label>
                    <input type="file" class="form-control" id="image">
                </div>
                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <input type="text" class="form-control" id="kategori" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan Barang</button>
                </div>
                </form>
            </div>
            </div>
        </div>
        </div>

    <!-- Toast notification -->
    <div class="toast-container">
        <div class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true" id="successToast">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">
                    Action completed successfully!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <h3>Data Transaksi Penitipan</h3>
        <div id="pegawaiTableContainer">
            <table class="table table-bordered" id="pegawaiTable">
                <thead>
                    <tr>
                        <th>ID transaksi</th>
                        <th>Penanggung Jawab</th>
                        <th>Barang</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal kadaluarsa</th>
                        <!-- <th>Password</th>  -->
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Data will be populated here from the server -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag

            const tableBody = document.getElementById("tableBody");
            // const searchInput = document.getElementById("searchInput");
            // const form = document.getElementById("PegawaiForm");
            // const registerButton = document.getElementById("registerButton");
            // const closeRegisterForm = document.getElementById("closeRegisterForm");
            // const registerFormContainer = document.getElementById("registerFormContainer");
            // const registerOverlay = document.getElementById("registerOverlay");
            // let pegawaiData = [];
            // let currentPegawaiId = null;
            
            // Toast functionality
            function showToast(message, bgColor = 'bg-primary') {
                const toast = document.getElementById('successToast');
                const toastMessage = document.getElementById('toastMessage');
                
                // Set message
                toastMessage.textContent = message;
                
                // Update background color
                toast.className = `toast align-items-center text-white border-0 ${bgColor}`;
                
                // Show toast using Bootstrap's toast API
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
            }

            // Fetch Pegawai Data
            async function fetchPegawai() {
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/pegawai", {
                        method: "GET",
                        headers: { "Authorization": `Bearer ${localStorage.getItem('auth_token')}` },
                    });
                    
                    const data = await response.json();
                    console.log("Raw response:", response);
                    console.log("Response JSON:", data);    
                    console.log("Token:", localStorage.getItem('auth_token'));

                    if (data.status === true && data.data.length > 0) {
                        pegawaiData = data.data;
                        renderTable(pegawaiData);
                    } else {
                        alert("Gagal memuat data pegawai.");
                        console.error("Error loading data:", data);
                    }
                } catch (error) {
                    console.error("Error fetching pegawai data:", error);
                    alert("An error occurred while fetching data. Check the console for details.");
                }
            }



            // Render the table with fetched data
            function renderTable(data) {
                tableBody.innerHTML = ""; // Clear the table before rendering new data

                // Prioritize pegawais with idTopSeller first
                // const sortedData = data.sort((a, b) => {
                //     return (b.idTopSeller ? 1 : 0) - (a.idTopSeller ? 1 : 0);  // 1st priority: have idTopSeller
                // });

                if (!data || data.length === 0) {
                    const row = tableBody.insertRow();
                    const cell = row.insertCell(0);
                    cell.colSpan = 4;
                    cell.classList.add("no-data-message"); // Add a class for empty state
                    cell.innerText = "No pegawai data available.";
                    return;
                }

                data.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${item.idPegawai}</td>
                        <td>${item.namaPegawai || '-'}</td>
                        <td>${item.jabatan?.namaJabatan || '-'}</td>
                        <td>${item.username}</td>
                        
                    `;
                    row.addEventListener("click", () => populateForm(item));  // Add click event to the row
                    tableBody.appendChild(row);
                });
            }

            // Populate the form when a row is clicked
            function populateForm(item) {
                // document.getElementById("")
                document.getElementById("idPegawai").value = item.idPegawai;
                document.getElementById("namaPegawai").value = item.namaPegawai || '';  // Set idTopSeller        // Set idDompet
                document.getElementById("username").value = item.username || '';         // Set username
                document.getElementById("password").value = item.password || '';   // Set namaPegawai
                
                // Store the current Pegawai ID
                currentPegawaiId = item.idPegawai;
            }
            const addBarangButton = document.getElementById("addBarangButton");
            const addBarangModal = new bootstrap.Modal(document.getElementById("addBarangModal"));
            const addBarangForm = document.getElementById("addBarangForm");
            const namaPegawai = localStorage.getItem('namaPegawai');
            const idPegawai = localStorage.getItem('idPegawai');

            console.log("Retrieved namaPegawai:", namaPegawai);
            console.log("Retrieved IdPegawai:", idPegawai);
            const user_role = localStorage.getItem('user_role');
            console.log("Retrieved rolei:", user_role);
            async function fetchHunters() {
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/pegawaiGethunters", {
                        method: "GET",
                        headers: {
                            "Authorization": `Bearer ${localStorage.getItem('auth_token')}`
                        }
                    });

                    const data = await response.json();
                    console.log("Fetched Hunters:", data);

                    const hunterSelect = document.getElementById("idPegawai2");
                    hunterSelect.innerHTML = `<option value=""> --- </option>`; // reset dropdown

                    if (data.status && data.data.length > 0) {
                        data.data.forEach(hunter => {
                            const option = document.createElement("option");
                            option.value = hunter.idPegawai;
                            option.textContent = hunter.namaPegawai;
                            hunterSelect.appendChild(option);
                        });
                    } else {
                        console.warn("No hunters available");
                    }
                } catch (error) {
                    console.error("Error fetching hunters:", error);
                    alert("Gagal memuat data Hunter");
                }
            }
            // When the "Tambah barang" button is clicked, open the modal
            window.lastCreatedBarangId = null;
            addBarangButton.addEventListener("click", function () {
                const addBarangDetailModal = new bootstrap.Modal(document.getElementById("addBarangDetailModal"));
                addBarangDetailModal.show();
            });
            const addBarangDetailForm = document.getElementById("addBarangDetailForm");
            addBarangDetailForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append("idBarang", document.getElementById("idBarang").value);
            // formData.append("idTransaksiDonasi", document.getElementById("idTransaksiDonasi").value);
            formData.append("namaBarang", document.getElementById("namaBarang").value);
            formData.append("beratBarang", document.getElementById("beratBarang").value);
            formData.append("garansiBarang", document.getElementById("garansiBarang").value);
            formData.append("periodeGaransi", document.getElementById("periodeGaransi").value);
            formData.append("hargaBarang", document.getElementById("hargaBarang").value);
            formData.append("haveHunter", document.getElementById("haveHunter").value);
            formData.append("statusBarang", document.getElementById("statusBarang").value);
            formData.append("kategori", document.getElementById("kategori").value);

            const imageInput = document.getElementById("image");
            if (imageInput.files.length > 0) {
                formData.append("image", imageInput.files[0]);
            }

            try {
                const response = await fetch("http://127.0.0.1:8000/api/barang", {
                    method: "POST",
                    headers: {
                        "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.status) {
                    alert("Barang created successfully!");

                    // Save last created idBarang
                    window.lastCreatedBarangId = result.data.idBarang;

                    // Close Barang modal
                    const addBarangDetailModal = bootstrap.Modal.getInstance(document.getElementById("addBarangDetailModal"));
                    addBarangDetailModal.hide();

                    // Open Transaksi Penitipan modal
                    const addBarangModal = new bootstrap.Modal(document.getElementById("addBarangModal"));
                    document.getElementById("idPegawai1").value = namaPegawai || '';
                    document.getElementById("tanggalPenitipan").value = new Date().toISOString().split("T")[0];

                    fetchHunters();
                    fetchPenitip();

                    addBarangModal.show();
                } else {
                    alert("Failed to create Barang.");
                    console.error(result.message || "Unknown error");
                }
            } catch (error) {
                console.error("Error creating Barang:", error);
                alert("An error occurred while creating the Barang.");
            }
        });

            // Handle date calculation (30 days after tanggalPenitipan)
            document.getElementById("tanggalPenitipan").addEventListener("change", function () {
                const tanggalPenitipan = new Date(this.value);
                const tanggalPenitipanSelesai = new Date(tanggalPenitipan);
                tanggalPenitipanSelesai.setDate(tanggalPenitipan.getDate() + 30); // Add 30 days
                document.getElementById("tanggalPenitipanSelesai").value = tanggalPenitipanSelesai.toISOString().split("T")[0]; // Format to YYYY-MM-DD
            });

            // Handle form submission (when the user clicks 'Save')
            addBarangForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            if (!window.lastCreatedBarangId) {
                alert("ID Barang not found! Please create Barang first.");
                return;
            }

            const now = new Date();
            const tanggalPenitipan = now.toISOString().slice(0, 19).replace('T', ' '); // Format: YYYY-MM-DD HH:MM:SS

            const tanggalPenitipanSelesaiObj = new Date(now);
            tanggalPenitipanSelesaiObj.setDate(now.getDate() + 30);
            const tanggalPenitipanSelesai = tanggalPenitipanSelesaiObj.toISOString().split('T')[0]; // Format: YYYY-MM-DD

            const idPegawai2Value = document.getElementById("idPegawai2").value;
            const transaksiData = {
                
                idPegawai1: localStorage.getItem('idPegawai'),
                // idPegawai2: idPegawai2Value !== "" ? idPegawai2Value : null,
                idPenitip: document.getElementById("idPenitip").value,
                // tanggalPenitipan: tanggalPenitipan,
                // tanggalPenitipanSelesai: tanggalPenitipanSelesai,
                totalHarga: document.getElementById("hargaBarang").value,
                idBarang: window.lastCreatedBarangId,
            };
            console.log("Transaksi Data:", transaksiData);
            try {
                const response = await fetch("http://127.0.0.1:8000/api/addTransaksiPenitipan", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify(transaksiData)
                });

                const result = await response.json();

                if (response.ok && result.status) {
                    alert("Transaksi Penitipan created successfully!");

                    // Close modal
                    const addBarangModal = bootstrap.Modal.getInstance(document.getElementById("addBarangModal"));
                    addBarangModal.hide();

                    // Reset window.lastCreatedBarangId
                    window.lastCreatedBarangId = null;

                } else {
                    alert("Failed to create Transaksi Penitipan.");
                    console.error(result.message || "Unknown error");
                }
            } catch (error) {
                console.error("Error creating Transaksi Penitipan:", error);
                alert("An error occurred while creating the Transaksi Penitipan.");
            }
        });

            async function fetchPenitip() {
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/getpenitip", {
                        method: "GET",
                        headers: {
                            "Authorization": `Bearer ${localStorage.getItem('auth_token')}`
                        }
                    });

                    const data = await response.json();
                    console.log("Fetched Penitip:", data);

                    const penitipSelect = document.getElementById("idPenitip");
                    penitipSelect.innerHTML = `<option value="">Pilih Penitip</option>`; // reset dropdown

                    if (data.status && data.data.length > 0) {
                        data.data.forEach(penitip => {
                            const option = document.createElement("option");
                            option.value = penitip.idPenitip;
                            option.textContent = penitip.username;
                            penitipSelect.appendChild(option);
                        });
                    } else {
                        console.warn("No Penitip available");
                    }
                } catch (error) {
                    console.error("Error fetching Penitip:", error);
                    alert("Gagal memuat data Penitip");
                }
            }

            // Initial fetch when the page loads
            fetchPenitip();
            fetchPegawai();
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>