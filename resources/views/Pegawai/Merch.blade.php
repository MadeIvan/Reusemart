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
    @include('layouts.navbar')
<div class="container mt-4" style = "margin-top: 5% !important; margin-left: 5% !important;" >



    <div class="modal fade" id="addClaimModal" tabindex="-1" aria-labelledby="addClaimModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClaimModalLabel">Detail Claim Merchandise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addClaimForm">
                    <div class="mb-3">
                        <label for="idPegawai1" class="form-label">Petugas Gudang</label> 
                        <input type="text" class="form-control" id="idPegawai1" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggalPenitipan" class="form-label">Tanggal Claim</label>
                        <input type="date" class="form-control" id="tanggalClaim" disabled>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"  id="saveButton">Save</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <h2>Data Merchandise</h2>
        
        <div id="merchTableContainer">
            <table class="table table-bordered" id="merchTable">
                <thead>
                    <tr>
                        <th>ID Merchandise</th>
                        <th>Nama Merchandise</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody id="merchTableBody">
                    <!-- Data will be populated here from the server -->
                </tbody>
            </table>
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
        <h3 id="tableTitle">Data Claim Merchandise</h3>
        <!-- <select id="searchInput" class="form-select mb-3">
            <option value="Semua">Semua</option>
            <option value="BelumDiambil">BelumDiambil</option>
        </select> -->
        <div class="btn-group mb-3">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Filter
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item filter-option" data-filter="Semua">Semua</a></li>
                <li><a class="dropdown-item filter-option" data-filter="BelumDiambil">BelumDiambil</a></li>
            </ul>
        </div>

        <div id="pegawaiTableContainer">
            <table class="table table-bordered" id="pegawaiTable">
                <thead>
                    <tr>
                        <th>ID Claim</th>
                        <th>Nama PJ</th>
                        <th>Nama Merchandise</th>
                        <th>Nama Pembeli</th>
                        <th>Tanggal Ambil</th>
                        <!-- <th>Password</th>  -->
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Data will be populated here from the server -->
                </tbody>
            </table>
        </div>
    </div>
</div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag
            console.log("namaPegawai : ", localStorage.getItem('namaPegawai'));
            console.log("idPegawai : ", localStorage.getItem('idPegawai'));
            const tableBody = document.getElementById("tableBody");
            const merchTableBody = document.getElementById("merchTableBody");
            let currentItem;
            let currentItemId;
            const addClaimModal = new bootstrap.Modal(document.getElementById('addClaimModal'));

            
            async function fetchPegawai() {
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/getClaim", {
                        method: "GET",
                        headers: { "Authorization": `Bearer ${localStorage.getItem('auth_token')}` },
                    });
                    
                    const data = await response.json();
                    
                    console.log("Raw response:", response);
                    console.log("Response JSON:", data);    
                    console.log("Token:", localStorage.getItem('auth_token'));

                    if (Array.isArray(data) && data.length > 0) {
                        pegawaiData = data;
                        renderTable(pegawaiData);
                    } else {
                        alert("Gagal memuat data Merch.");
                        console.error("Error loading data:", data);
                    }
                } catch (error) {
                    console.error("Error fetching Merch data:", error);
                    alert("An error occurred while fetching data. Check the console for details.");
                }
            }
            
            function renderTable(data) {
                tableBody.innerHTML = ""; // Clear the table before rendering new data
                data.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td id="${item.idClaim}">${item.idClaim}</td>
                        <td>${item.pegawai ? item.pegawai.namaPegawai : '-'}</td>
                        <td>${item.merchandise ? item.merchandise.nama : '-'}</td>
                        <td>${item.pembeli ? item.pembeli.namaPembeli : '-'}</td>
                        <td>${item.tanggalAmbil || '-'}</td>
                    `;
                    row.addEventListener("click", () => {
                        currentItem = item;  // full item for edit modal
                        currentItemId = item.idClaim; // id for printing
                        console.log("selected item:", currentItem);
                        document.getElementById("idPegawai1").value = item.pegawai ? item.pegawai.namaPegawai : '-';
                        document.getElementById("tanggalClaim").value = item.tanggalAmbil || '-';
                        const idPegawai = item.idPegawai;
                        const tanggalAmbil = item.tanggalAmbil;

                        const idPegawaiField = document.getElementById("idPegawai1");
                        const tanggalClaimField = document.getElementById("tanggalClaim");
                        const saveButton = document.getElementById("saveButton");

                        if (!idPegawai && !tanggalAmbil) {
                            idPegawaiField.required = false;
                            tanggalClaimField.required = true;
                            idPegawaiField.disabled = true;
                            tanggalClaimField.disabled = false;
                            saveButton.style.display = "inline-block";
                            if (!idPegawai) {
                                idPegawaiField.value = ''; // clear any existing value
                                idPegawaiField.placeholder = localStorage.getItem("namaPegawai") || "Nama Pegawai";
                            } else {
                                idPegawaiField.value = item.pegawai ? item.pegawai.namaPegawai : '-'; // populate value
                                idPegawaiField.placeholder = ''; // remove placeholder
                            }
                        } else {
                            idPegawaiField.required = false;
                            tanggalClaimField.required = false;
                            idPegawaiField.disabled = true;
                            tanggalClaimField.disabled = true;
                            saveButton.style.display = "none";

                            
                        }
                        addClaimModal.show();
                    });
                    tableBody.appendChild(row);
                });
            }
            document.querySelectorAll('.filter-option').forEach(item => {
                item.addEventListener('click', function(event) {
                    event.preventDefault(); // prevent default anchor behavior
                    const selectedFilter = this.getAttribute('data-filter');
                    console.log("Selected filter:", selectedFilter);

                    
                    const tableTitle = document.getElementById('tableTitle');
                    if (selectedFilter === 'BelumDiambil') {
                        tableTitle.textContent = "Data Claim Merchandise (Belum diverifikasi)";
                    } else {
                        tableTitle.textContent = "Semua Data Claim Merchandise";
                    }

                    let filteredData;
                    if (selectedFilter === 'BelumDiambil') {
                        filteredData = pegawaiData.filter(item => !item.tanggalAmbil);
                    } else {
                        filteredData = pegawaiData;
                    }
                    renderTable(filteredData);
                });
            });

            async function fetchMerch() {
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/getMerch", {
                        method: "GET",
                        headers: { "Authorization": `Bearer ${localStorage.getItem('auth_token')}` },
                    });
                    
                    const data = await response.json();
                    
                    console.log("Raw response:", response);
                    console.log("Response JSON:", data);    
                    console.log("Token:", localStorage.getItem('auth_token'));

                    if (Array.isArray(data) && data.length > 0) {
                        pegawaiData = data;
                        renderTableMerch(pegawaiData);
                    } else {
                        alert("Gagal memuat data Merch.");
                        console.error("Error loading data:", data);
                    }
                } catch (error) {
                    console.error("Error fetching Merch data:", error);
                    alert("An error occurred while fetching data. Check the console for details.");
                }
            }
            
            function renderTableMerch(data) {
                merchTableBody.innerHTML = ""; // Clear the table before rendering new data
                data.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td id="${item.idMerchandise}">${item.idMerchandise}</td>
                        <td>${item.nama}</td>
                        <td>${item.jumlahSatuan}</td>

                    `;
                    row.addEventListener("click", () => {
                        currentItem = item;  // full item for edit modal
                        currentItemId = item.idClaim; // id for printing
                        console.log("selected item:", currentItem);
                        // actionModal.show();
                    });
                    merchTableBody.appendChild(row);
                });
            }
            async function updateClaimMerchandise(idClaim, idPegawai, tanggalAmbil) {
                try {
                    const token = localStorage.getItem('auth_token');
                    const response = await fetch(`http://127.0.0.1:8000/api/saveClaim/${idClaim}`, {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            "Authorization": `Bearer ${token}`,
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({
                            idPegawai: idPegawai,
                            tanggalAmbil: tanggalAmbil
                        })
                    });

                    const result = await response.json();
                    if (response.ok) {
                        console.log("Claim Merchandise updated successfully:", result);
                        fetchPegawai(); // reload data
                        // showSuccessToast("Claim Merchandise updated successfully!");
                    } else {
                        console.error("Error updating Claim Merchandise:", result);
                        alert("Failed to update Claim Merchandise.");
                    }
                } catch (error) {
                    console.error("Error updating Claim Merchandise:", error);
                    alert("An error occurred while updating Claim Merchandise.");
                }
            }
            document.getElementById("addClaimForm").addEventListener("submit", function(event) {
                event.preventDefault();
                const idPegawai = localStorage.getItem("idPegawai");
                const tanggalAmbil = document.getElementById("tanggalClaim").value;
                if (!currentItemId) {
                    alert("No selected Claim Merchandise.");
                    return;
                }
                const confirmUpdate = confirm("Are you sure you want to update this Claim Merchandise?");
                if (confirmUpdate) {
                    updateClaimMerchandise(currentItemId, idPegawai, tanggalAmbil);
                    addClaimModal.hide();
                    fetchMerch();
                    fetchPegawai();
                } else {
                    addClaimModal.hide();
                    console.log("Update cancelled by user.");
                }
            });


            fetchMerch();
            fetchPegawai();
            // renderTable(data);
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>