<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Pegawai</title>

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
        <h2>Request Donasi</h2>
        


        <!-- Form for Pegawai data -->
        <form id="DonasiForm">
            <input type="hidden" id="currentDonasiId">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="idDonasi" class="form-label">ID Donasi</label>
                    <input type="text" class="form-control" id="idDonasi" disabled>
                </div>
                <div class="col-md-4">
                    <label for="statusDonasi" class="form-label">Status</label>
                    <input type="text" class="form-control" id="statusDonasi" disabled>
                </div>
                
                <div class="col-md-4">
                    <label for="idBarang" class="form-label">Barang</label>
                    <select class="form-select" id="idBarang" required>
                        <option value="">-- Select Barang --</option>
                        <!-- Options will be dynamically populated here -->
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="penerimaDonasi" class="form-label">Penerima</label>
                    <input type="text" class="form-control" id="penerimaDonasi" >
                </div>

            </div>

            <!-- <div class="row mb-3">
                <div class="col-md-4">
                    <label for="password" class="form-label">password</label>
                    <input type="text" class="form-control" id="password" disabled>
                </div>
            </div> -->

            <div class="row">
                <div class="col-12 btn-container">
                    <button type="submit" class="btn btn-success" id="donasiButton">Simpan</button>
                    <!-- <button type="button" class="btn btn-danger" id="deleteDonasiButton">Delete Request</button> -->
                </div>
            </div>
        </form>
            <input type="text" id="searchInputDonasi" class="form-control mb-3" placeholder="Search by Organisasi">
                <div id="donasiTableContainer">
            <table class="table table-bordered" id="donasiTable">
                <thead>
                    <tr>
                        <th>ID Request</th>
                        <th>Nama Organisasi</th>
                        <th>Barang Request</th>
                        <th>Status</th>
                        <!-- <th>Password</th>  -->
                    </tr>
                </thead>
                <tbody id="tableDonasiBody">
                    <!-- Data will be populated here from the server -->
                </tbody>
            </table>
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

    

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag


            ///Owner Part
            const tableDonasiBody = document.getElementById("tableDonasiBody");
            const searchInputDonasi = document.getElementById("searchInputDonasi");
            const form = document.getElementById("DonasiForm");
            const DonasiButton = document.getElementById("DonasiButton");
            const closeRegisterForm = document.getElementById("closeRegisterForm");
            // const registerFormContainer = document.getElementById("registerFormContainer");
            // const registerOverlay = document.getElementById("registerOverlay");
            let DonasiData = [];
            let currentDonasiiId = null;
            const selectedBarangId = document.getElementById("idBarang").value;


            ///Owner Part
            document.getElementById("donasiButton").addEventListener("click", async function(e) {
    e.preventDefault();  // Prevent form submission

    // Get values from the form
    const idBarang = document.getElementById("idBarang").value;
    const penerimaDonasi = document.getElementById("penerimaDonasi").value;
    const idDonasi = document.getElementById("idDonasi").value;
    const statusDonasi = document.getElementById("statusDonasi").value;

    // Validate inputs (e.g., ensure fields are not empty)
    if (!idBarang || !penerimaDonasi || !statusDonasi) {
        alert("Please fill in all fields.");
        return;
    }

    // Prepare the data to be sent
    const donasiData = {
        idBarang: idBarang,
        idRequest: idDonasi, 
        namaPenerima: penerimaDonasi, // Assuming you want to link the idDonasi to idRequest
        statusDonasi: 'Diterima',
        tanggalDonasi : new Date().toISOString(),
    };

   try {
    const response = await fetch("http://127.0.0.1:8000/api/transaksi-donasi", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept" : "application/json",
            "Authorization": `Bearer ${localStorage.getItem('authToken')}`,
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify(donasiData),
    });

   
    const responseText = await response.text();
    console.log("Response Text:", responseText);

    const result = JSON.parse(responseText); // This might throw an error if the response is HTML instead of JSON
    if (response.ok) {
        alert("Transaksi Donasi created successfully!");
        console.log(result);  
        fetchDonasi();// Check the result of the API request
    } else {
        alert("Failed to create Transaksi Donasi.");
        console.error(result.message || "Unknown error");
    }
} catch (error) {
    console.error("Error creating transaksi donasi:", error);
    alert("An error occurred while creating the transaksi donasi.");
}

});

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
            // fetch data rerquest donasi
            async function fetchDonasi() {
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/donasi", {
                        method: "GET",
                        headers: { "Authorization": `Bearer ${localStorage.getItem('authToken')}` },
                    });
                    
                    const data = await response.json();
                    console.log("Raw response:", response);
                    console.log("Response JSON:", data);    
                    console.log("Token:", localStorage.getItem('authToken'));

                    if (data.status === true && data.data.length > 0) {
                        DonasiData = data.data;
                        renderTableDonasi(DonasiData);
                    } else {
                        alert("Gagal memuat data Donasi.");
                        console.error("Error loading data:", data);
                    }
                } catch (error) {
                    console.error("Error fetching pegawai data:", error);
                    alert("An error occurred while fetching data. Check the console for details.");
                }
            }
             function renderTableDonasi(data) {
                tableDonasiBody.innerHTML = ""; // Clear the table before rendering new data

                // Prioritize pegawais with idTopSeller first
                // const sortedData = data.sort((a, b) => {
                //     return (b.idTopSeller ? 1 : 0) - (a.idTopSeller ? 1 : 0);  // 1st priority: have idTopSeller
                // });

                if (!data || data.length === 0) {
                    const row = tableDonasiBody.insertRow();
                    const cell = row.insertCell(0);
                    cell.colSpan = 4;
                    cell.classList.add("no-data-message"); // Add a class for empty state
                    cell.innerText = "No Request Donasi data available.";
                    return;
                }

                data.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${item.idRequest}</td>
                        <td>${item.organisasi?.namaOrganisasi || '-'}</td>
                        <td>${item.barangRequest || '-'}</td>
                        <td>${item.status}</td>
                        
                    `;
                    row.addEventListener("click", () => populateForm(item));  // Add click event to the row
                    tableDonasiBody.appendChild(row);
                });
            }

            // Fetch Pegawai Data
            
            async function fetchBarangOptions() {
    try {
        const response = await fetch("http://127.0.0.1:8000/api/barang/available", {
            method: "GET",
            headers: {
                "Authorization": `Bearer ${localStorage.getItem('authToken')}`,
                "Accept": "application/json"
            }
        });

        const result = await response.json();

        if (result.status === true) {
            const barangSelect = document.getElementById("idBarang");

            // Clear the existing options
            barangSelect.innerHTML = '<option value="">-- Select Barang --</option>';

            // Add options to the select dropdown
            
            result.data.forEach(barang => {
                const option = document.createElement("option");
                option.value = barang.idBarang;
                option.textContent = barang.namaBarang;
                barangSelect.appendChild(option);
            });
        } else {
            console.error("Failed to load available barang:", result);
        }
    } catch (error) {
        console.error("Error fetching barang options:", error);
    }
}

            

            searchInputDonasi.addEventListener("input", function() {
                const searchTerm = this.value.toLowerCase();
                
                if (searchTerm === "") {
                    renderTableDonasi(DonasiData); // Show all data if search is empty
                    return;
                }
                
                // Filter the data based on search term
                const filteredData = DonasiData.filter(item => {
    // Check if either idOrganisasi or namaOrganisasi contains the search term
                    return (
                        (item.idOrganisasi && item.idOrganisasi.toLowerCase().includes(searchTerm)) ||  // Check idOrganisasi
                        (item.organisasi?.namaOrganisasi && item.organisasi?.namaOrganisasi.toLowerCase().includes(searchTerm))  // Check namaOrganisasi
                    );
                });
                
                renderTableDonasi(filteredData);
            });
        
           
            
            // Populate the form when a row is clicked
            function populateForm(item) {
                console.log(item);  // Log to check the structure of 'item'

                document.getElementById("idDonasi").value = item.idRequest;
                document.getElementById("statusDonasi").value = item.status || '';
                
                // Safely access 'namaPenerima' inside 'transaksidonasi'
                document.getElementById("penerimaDonasi").value = item.transaksi_donasi?.namaPenerima || 'No recipient';  // Fallback if undefined

                // Store the current Donasi ID
                currentDonasiId = item.idDonasi;
            }

            // Save updated Pegawai data
            

           

            

            
            
            
            // Initial fetch when the page loads
            
            fetchDonasi();
            fetchBarangOptions();
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>