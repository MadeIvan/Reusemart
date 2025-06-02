<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu Perdonasian</title>

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
                    <button type="submit" class="btn btn-success" id="addBarangButton">Tambah barang</button>
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
                <label for="idPegawai1" class="form-label">ID Pegawai 1</label>
                <input type="text" class="form-control" id="idPegawai1" required>
            </div>
            <div class="mb-3">
                <label for="idPegawai2" class="form-label">ID Pegawai 2</label>
                <input type="text" class="form-control" id="idPegawai2" required>
            </div>
            <div class="mb-3">
                <label for="idPenitip" class="form-label">ID Penitip</label>
                <input type="text" class="form-control" id="idPenitip" required>
            </div>
            <div class="mb-3">
                <label for="tanggalPenitipan" class="form-label">Tanggal Penitipan</label>
                <input type="date" class="form-control" id="tanggalPenitipan" required>
            </div>
            <div class="mb-3">
                <label for="tanggalPenitipanSelesai" class="form-label">Tanggal Penitipan Selesai</label>
                <input type="date" class="form-control" id="tanggalPenitipanSelesai" disabled required>
            </div>
            <div class="mb-3">
                <label for="totalHarga" class="form-label">Total Harga</label>
                <input type="number" class="form-control" id="totalHarga" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
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

            // When the "Tambah barang" button is clicked, open the modal
            addBarangButton.addEventListener("click", function () {
                
                // Set the tanggalPenitipan to the current date when the modal opens
                const today = new Date().toISOString().split("T")[0]; // Format to YYYY-MM-DD
                document.getElementById("tanggalPenitipan").value = today;

                addBarangModal.show();
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

                const donasiData = {
                    idPegawai1: document.getElementById("idPegawai1").value,
                    idPegawai2: document.getElementById("idPegawai2").value,
                    idPenitip: document.getElementById("idPenitip").value,
                    tanggalPenitipan: document.getElementById("tanggalPenitipan").value,
                    tanggalPenitipanSelesai: document.getElementById("tanggalPenitipanSelesai").value,
                    totalHarga: document.getElementById("totalHarga").value,
                };

                try {
                    const response = await fetch("http://127.0.0.1:8000/api/transaksi-penitipan", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(donasiData),
                    });

                    const result = await response.json();

                    if (response.ok) {
                        alert("Transaksi Penitipan created successfully!");
                        addBarangModal.hide(); // Close the modal
                        fetchData(); // Refresh the data if needed
                    } else {
                        alert("Failed to create transaksi penitipan.");
                        console.error(result.message || "Unknown error");
                    }
                } catch (error) {
                    console.error("Error creating transaksi penitipan:", error);
                    alert("An error occurred while creating the transaksi penitipan.");
                }
            });


            // Initial fetch when the page loads
            fetchPegawai();
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>