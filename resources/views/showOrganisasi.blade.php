<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Penitip</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Styling for table with scrollable body */
        #penitipTableContainer {
            max-height: 400px;
            overflow-y: auto;
        }

        /* Fix the header so it stays visible while scrolling */
        #penitipTable thead th {
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

    <div class="container mt-4">
        <h2>Data Organisasi</h2>

        <!-- Form for Organisasi data -->
        <form id="OrganisasiForm">
            <input type="hidden" id="currentOrganisasiId">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="namaOrganisasi" class="form-label">Nama Organisasi</label>
                    <input type="text" class="form-control" id="namaOrganisasi" required>
                </div>                
                <div class="col-md-4">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" disabled>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" disabled>
                </div>
                <div class="col-md-4">
                    <label for="alamat" class="form-label">Alamat</label>
                    <input type="text" class="form-control" id="alamat" required>
                </div>
            </div>

            <div class="row">
                <div class="col-12 btn-container">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-danger" id="deleteButton">Delete</button>
                </div>
            </div>
        </form>
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
        <h3>Organisasi Data</h3>
        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Cari Organisasi">

        <div id="OrganisasiTableContainer">
            <table class="table table-bordered" id="OrganisasiTable">
                <thead>
                    <tr>
                        <th>ID Organisasi</th>
                        <th>Nama Organisasi</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Alamat</th>
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

            const tableBody = document.querySelector("#tableBody");
            const searchInput = document.getElementById("searchInput");
            const form = document.getElementById("OrganisasiForm");
            let OrganisasiData = [];
            let currentOrganisasiId = null;

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

            // Fetch Organisasi Data
            async function fetchOrganisasi(){
                try{
                    const response = await fetch("http://127.0.0.1:8000/api/organisasi", {
                        method: "GET",
                        headers: {
                            "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                            // 'Accept': 'application/json',
                            // "Content-Type": "application/json",
                            // "X-CSRF-TOKEN": csrfToken,
                        },
                    })
                    const data = await response.json();
                    if (data.status === true && data.data.length > 0) {
                        organisasiData = data.data;
                        renderTable(organisasiData);
                    } else {
                        alert("Gagal memuat data organisasi.");
                        console.error("Error loading data:", data);
                    }
                } catch (error) {
                    console.error("Error fetching penitip data:", error);
                    alert("An error occurred while fetching data. Check the console for details.");
                }
            }
  
            // Render the table with fetched data
            function renderTable(data) {
            tableBody.innerHTML = "";

            if (!data || data.length === 0) {
                const row = tableBody.insertRow();
                const cell = row.insertCell(0);
                cell.colSpan = 7;
                cell.classList.add("no-data-message");
                cell.innerText = "No organisasi data available.";
                return;
            }

            data.forEach(item => {
                if (item.deleted_at !== null) return;

                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${item.idOrganisasi}</td>
                    <td>${item.namaOrganisasi || '-'}</td>
                    <td>${item.username}</td>
                    <td>${item.email}</td>
                    <td>${item.alamat}</td>
                `;
                row.addEventListener("click", () => populateForm(item));
                tableBody.appendChild(row);
            });
        }


            // Populate the form when a row is clicked
            function populateForm(item) {
                document.getElementById("currentOrganisasiId").value = item.idOrganisasi;
                document.getElementById("namaOrganisasi").value = item.namaOrganisasi || '';  // Set idTopSeller
                document.getElementById("username").value = item.username || '';         // Set username
                document.getElementById("email").value = item.email || '';   // Set namaPenitip
                document.getElementById("alamat").value = item.alamat || '';                   // Set NIK
                
                // Store the current Penitip ID
                currentOrganisasiId = item.idOrganisasi;
            }

            // Save updated Penitip data
            form.addEventListener("submit", async function(e) {
                e.preventDefault();
                
                // Check if a penitip is selected
                if (!currentOrganisasiId) {
                    alert("Please select a organisasi from the table first.");
                    return;
                }
                
                const updatedData = {
                    namaOrganisasi: document.getElementById("namaOrganisasi").value,
                    username: document.getElementById("username").value,
                    email: document.getElementById("email").value,
                    alamat: document.getElementById("alamat").value
                };
                
                try {
                    console.log("Updating organisasi with ID:", currentOrganisasiId);
                    console.log("Update data:", updatedData);
                    
                    // First try with PUT method
                    let response = await fetch(`http://127.0.0.1:8000/api/organisasi/update/${currentOrganisasiId}`, {
                        method: "PUT",
                        headers: { 
                            "Accept": "application/json",
                            "Content-Type": "application/json",
                            "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify(updatedData)
                    });

                    // Check if the request was successful
                    if (response.ok) {
                        const result = await response.json();
                        console.log("Response:", result);
                        
                        if (result.status === true) {
                            showToast("Organisasi updated successfully!", "bg-success");
                            fetchOrganisasi(); // Refresh the table data
                        } else {
                            showToast(`Failed to update Organisasi: ${result.message || 'Unknown error'}`, "bg-danger");
                            console.error("Error updating Organisasi:", result);
                        }
                    } else {
                        // Log detailed error information
                        console.error("Server error:", response.status, response.statusText);
                        try {
                            const errorText = await response.text();
                            console.error("Error response:", errorText);
                            showToast(`Server error (${response.status}): ${errorText.substring(0, 100)}...`, "bg-danger");
                        } catch (parseError) {
                            showToast(`Server error (${response.status})`, "bg-danger");
                        }
                    }
                } catch (error) {
                    console.error("Client error updating Organisasi:", error);
                    showToast(`An error occurred while updating: ${error.message}`, "bg-danger");
                }
            });

            // Delete Penitip
           document.getElementById("deleteButton").addEventListener("click", async function() {
                if (!currentOrganisasiId) {
                    alert("Please select a organisasi from the table first.");
                    return;
                }

                if (confirm("Are you sure you want to delete this organisasi?")) {
                    try {
                        const response = await fetch(`http://127.0.0.1:8000/api/organisasi/delete/${currentOrganisasiId}`, {
                            method: "DELETE",
                            headers: { 
                                "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                                "Accept": "application/json",
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken,
                            }
                        });
                        
                        const result = await response.json();
                        
                        // Periksa apakah 'message' berisi kata 'deleted' dan pastikan status-nya benar
                        if (result.status === true) {
                            showToast("Organisasi deleted successfully!", "bg-success");

                            // Clear the form
                            document.getElementById("namaOrganisasi").value = "";
                            document.getElementById("username").value = "";
                            document.getElementById("email").value = "";
                            document.getElementById("alamat").value = "";

                            currentOrganisasiId = null;

                            fetchOrganisasi(); // Refresh the table
                        } else {
                            showToast("Failed to delete organisasi, but the operation was successful on the backend.", "bg-warning");
                            console.log("Server message:", result.message); // Debugging output
                        }
                    } catch (error) {
                        console.error("Error deleting organisasi:", error);
                        showToast("An error occurred while deleting.", "bg-danger");
                    }
                }
            });


            // Search functionality
            searchInput.addEventListener("input", function() {
                const searchTerm = this.value.toLowerCase();
                
                if (searchTerm === "") {
                    renderTable(organisasiData); // Show all data if search is empty
                    return;
                }
                
                // Filter the data based on search term
                const filteredData = organisasiData.filter(item => {
                    return (
                        (item.username && item.username.toLowerCase().includes(searchTerm)) ||
                        (item.namaOrganisasi && item.namaOrganisasi.toLowerCase().includes(searchTerm))||
                        (item.alamat && item.alamat.toLowerCase().includes(searchTerm)) ||
                        (item.email && item.email.toLowerCase().includes(searchTerm))
                    );
                });
                
                renderTable(filteredData);
            });

            // Initial fetch when the page loads
            fetchOrganisasi();
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>