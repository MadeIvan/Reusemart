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
        <h3>Data Claim Merchandise</h3>
        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by username or name">

        <div id="pegawaiTableContainer">
            <table class="table table-bordered" id="pegawaiTable">
                <thead>
                    <tr>
                        <th>ID Claim</th>
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag
            console.log("namaPegawai : ", localStorage.getItem('namaPegawai'));
            const tableBody = document.getElementById("tableBody");
            
            async function fetchPegawai() {
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/claimMerch", {
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
                        <td>${item.idPegawai || '-'}</td>
                        <td>${item.idMerchandise || '-'}</td>
                        <td>${item.idPembeli}</td>
                        <td>${item.tanggalAmbil || '-'}</td>
                    `;
                    row.addEventListener("click", () => {
                        currentItem = item;  // full item for edit modal
                        currentItemId = item.transaksiPenitipan.idTransaksiPenitipan; // id for printing
                        console.log("selected item:", currentItem);
                        actionModal.show();
                    });
                    tableBody.appendChild(row);
                });
            }

            renderTable();
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>