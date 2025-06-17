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
    <div class="container mt-4">
        <h2>Data Pegawai</h2>

        <!-- Form for Pegawai data -->
        <form id="PegawaiForm">
            <input type="hidden" id="currentPegawaiId">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="idPegawai" class="form-label">ID Pegawai</label>
                    <input type="text" class="form-control" id="idPegawai" disabled>
                </div>
                <div class="col-md-4">
                    <label for="namaPegawai" class="form-label">nama Pegawai</label>
                    <input type="text" class="form-control" id="namaPegawai" required>
                </div>
                
                <div class="col-md-4">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" required>
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
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-danger" id="deleteButton">Delete</button>
                    <button type="button" class="btn btn-primary" id="registerButton">Register Pegawai</button>
                    <button type="button" class="btn btn-warning" id="resetButton">Reset Password</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Floating Register Form -->
    <div id="registerOverlay" class="overlay"></div>
    <div class="register-form-container" id="registerFormContainer">
        <h4>Register Pegawai</h4>
        <form id="registerForm">
            <div class="mb-3">
                <label for="registerNamaPegawai" class="form-label">Nama Pegawai</label>
                <input type="text" class="form-control" id="registerNamaPegawai" required>
            </div>
            <div class="mb-3">
                <label for="registerJabatan" class="form-label">Jabatan</label>
                <select class="form-select" id="registerJabatan" required>
                    <option value="">...</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="registerUsername" class="form-label">Username</label>
                <input type="text" class="form-control" id="registerUsername" required>
            </div>
            <div class="mb-3">
                <label for="registerPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="registerPassword" required>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-success">Register</button>
                <button type="button" class="btn btn-secondary" id="closeRegisterForm">Close</button>
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
        <h3>Pegawai Data</h3>
        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by username or name">

        <div id="pegawaiTableContainer">
            <table class="table table-bordered" id="pegawaiTable">
                <thead>
                    <tr>
                        <th>ID Pegawai</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan</th>
                        <th>Username</th>
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
            const searchInput = document.getElementById("searchInput");
            const form = document.getElementById("PegawaiForm");
            const registerButton = document.getElementById("registerButton");
            const closeRegisterForm = document.getElementById("closeRegisterForm");
            const registerFormContainer = document.getElementById("registerFormContainer");
            const registerOverlay = document.getElementById("registerOverlay");
            let pegawaiData = [];
            let currentPegawaiId = null;
            
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

            async function fetchJabatanOptions() {
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/jabatan", {
                        method: "GET",
                        headers: {
                            "Accept": "application/json",
                            "Authorization": `Bearer ${localStorage.getItem('auth_token')}`
                        }
                    });

                    const result = await response.json();

                    if (result.status === true) {
                        const jabatanSelect = document.getElementById("registerJabatan");
                        result.data
                            .filter(j => j.namaJabatan !== 'Owner') // Exclude 'Owner'
                            .forEach(jabatan => {
                                const option = document.createElement("option");
                                option.value = jabatan.idJabatan;
                                option.text = jabatan.namaJabatan;
                                jabatanSelect.appendChild(option);
                            });
                    } else {
                        console.error("Failed to load jabatan:", result);
                    }
                } catch (error) {
                    console.error("Error fetching jabatan:", error);
                }
            }
            fetchJabatanOptions();


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
                // document.getElementById("password").value = item.password || '';   // Set namaPegawai
                
                // Store the current Pegawai ID
                currentPegawaiId = item.idPegawai;
            }

            // Save updated Pegawai data
            form.addEventListener("submit", async function(e) {
                e.preventDefault();
                
                // Check if a pegawai is selected
                if (!currentPegawaiId) {
                    alert("Please select a pegawai from the table first.");
                    return;
                }
                
                const updatedData = {
                    // idPegawai: currentPegawaiId,
                    namaPegawai: document.getElementById("namaPegawai").value,
                    username: document.getElementById("username").value,
                    
                    // nik: document.getElementById("nik").value
                };
                
                try {
                    console.log("Updating pegawai with ID:", currentPegawaiId);
                    console.log("Update data:", updatedData);
                    
                    // First try with PUT method
                    let response = await fetch(`http://127.0.0.1:8000/api/pegawai/update/${currentPegawaiId}`, {
                        method: "PUT",
                        headers: { 
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify(updatedData)
                    });
                    
                    // If PUT fails, try with POST and _method=PUT (for Laravel compatibility)
                    if (!response.ok) {
                        console.log("PUT request failed, trying with POST + _method=PUT");
                        const formData = new FormData();
                        formData.append('_method', 'PUT');
                        formData.append('username', updatedData.username);
                        formData.append('namaPegawai', updatedData.namaPegawai);
                        // formData.append('nik', updatedData.nik);
                        
                        response = await fetch(`http://127.0.0.1:8000/api/pegawai/${currentPegawaiId}`, {
                            method: "POST",
                            headers: {
                                "Accept": "application/json",
                                "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
                                "X-CSRF-TOKEN": csrfToken
                            },
                            body: formData
                        });
                    }
                    
                    // Check if the request was successful
                    if (response.ok) {
                        const result = await response.json();
                        console.log("Response:", result);
                        
                        if (result.status === true) {
                            showToast("Pegawai updated successfully!", "bg-success");
                            fetchPegawai(); // Refresh the table data
                            
                        } else {
                            showToast(`Failed to update pegawai: ${result.message || 'Unknown error'}`, "bg-danger");
                            console.error("Error updating pegawai:", result);
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
                    console.error("Client error updating pegawai:", error);
                    showToast(`An error occurred while updating: ${error.message}`, "bg-danger");
                }
            });

            // Delete Pegawai
            document.getElementById("deleteButton").addEventListener("click", async function() {
                if (!currentPegawaiId) {
                    alert("Please select a pegawai from the table first.");
                    return;
                }
                
                if (confirm("Are you sure you want to delete this pegawai?")) {
                    try {
                        const response = await fetch(`http://127.0.0.1:8000/api/pegawai/${currentPegawaiId}`, {
                            method: "DELETE",
                            headers: { 
                                "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
                                "X-CSRF-TOKEN": csrfToken
                            }
                        });
                        
                        const result = await response.json();
                        
                        if (result.status === true) {
                            showToast("Pegawai deleted successfully!", "bg-success");
                            
                            // Clear the form
                            document.getElementById("currentPegawaiId").value = "";
                            // document.getElementById("idTopSeller").value = "";
                            document.getElementById("namaPegawai").value = "";
                            document.getElementById("username").value = "";
                            document.getElementById("password").value = "";
                            // document.getElementById("nik").value = "";
                            
                            currentPegawaiId = null;
                            
                            fetchPegawai(); // Refresh the table data
                        } else {
                            showToast("Failed to delete pegawai!", "bg-danger");
                            console.error("Error deleting pegawai:", result);
                            fetchPegawai(); 
                        }
                    } catch (error) {
                        console.error("Error deleting pegawai:", error);
                        showToast("An error occurred while deleting.", "bg-danger");
                    }
                }
            });

             // Delete Pegawai
            document.getElementById("resetButton").addEventListener("click", async function() {
                if (!currentPegawaiId) {
                    alert("Please select a pegawai from the table first.");
                    return;
                }
                
                if (confirm("Are you sure you want to reset password this pegawai?")) {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); 
                        const response = await fetch(`http://127.0.0.1:8000/api/pegawai/reset-password/${currentPegawaiId}`, {
                            method: "PUT",
                            headers: { 
                                
                                "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": csrfToken
                            }
                        });
                        
                            console.log("Response status:", response.status);

                            const text = await response.text();
                            console.log("Response raw text:", text);

                            let result;
                            try {
                                result = JSON.parse(text);
                            } catch (e) {
                                console.error("Response is not valid JSON:", e);
                                throw new Error("Response is not valid JSON");
                            }
                            console.log("Response JSON:", result);

                            
                        if (result.status === true) {
                            showToast("Pegawai reset successfully!", "bg-success");
                            
                            // Clear the form
                            document.getElementById("currentPegawaiId").value = "";
                            // document.getElementById("idTopSeller").value = "";
                            document.getElementById("namaPegawai").value = "";
                            document.getElementById("username").value = "";
                            // document.getElementById("password").value = "";
                            // document.getElementById("nik").value = "";
                            
                            currentPegawaiId = null;
                            
                            fetchPegawai(); // Refresh the table data
                        } else {
                            showToast("Failed to reset password pegawai!", "bg-danger");
                            console.error("Error reset password pegawai:", result);
                            fetchPegawai(); 
                        }
                    } catch (error) {
                        console.error("Error reset password pegawai:", error);
                        showToast("An error occurred while reset password.", "bg-danger");
                    }
                }
            });

            // Search functionality
            searchInput.addEventListener("input", function() {
                const searchTerm = this.value.toLowerCase();
                
                if (searchTerm === "") {
                    renderTable(pegawaiData); // Show all data if search is empty
                    return;
                }
                
                // Filter the data based on search term
                const filteredData = pegawaiData.filter(item => {
                    return (
                        (item.username && item.username.toLowerCase().includes(searchTerm)) ||
                        (item.namaPegawai && item.namaPegawai.toLowerCase().includes(searchTerm))
                    );
                });
                
                renderTable(filteredData);
            });

            // Open Register Form
            registerButton.addEventListener("click", function () {
                registerFormContainer.style.display = "block"; // Show the form
                registerOverlay.style.display = "block"; // Show the overlay
            });

            // Close Register Form
            closeRegisterForm.addEventListener("click", function () {
                registerFormContainer.style.display = "none"; // Hide the form
                registerOverlay.style.display = "none"; // Hide the overlay
            });

            // Close Register Form if clicked outside of it
            registerOverlay.addEventListener("click", function () {
                registerFormContainer.style.display = "none"; // Hide the form
                registerOverlay.style.display = "none"; // Hide the overlay
            });

            // Register New Pegawai
            document.getElementById("registerForm").addEventListener("submit", async function (e) {
                e.preventDefault();

                const registerData = {
                    username: document.getElementById("registerUsername").value,
                    password: document.getElementById("registerPassword").value,
                    namaPegawai: document.getElementById("registerNamaPegawai").value,
                    idJabatan: document.getElementById("registerJabatan").value,
                };

                try {
                    const response = await fetch("http://127.0.0.1:8000/api/pegawai/register", {
                        method: "POST",
                        headers: { 
                            "Content-Type": "application/json", 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken 
                        },
                        body: JSON.stringify(registerData),
                    });
                    const result = await response.json();
                    if (result.status === true) {
                        fetchPegawai(); // Re-fetch     data after successful registration
                        showToast("Pegawai registered successfully!", "bg-success");
                        
                        // Clear form fields
                        document.getElementById("registerUsername").value = "";
                        document.getElementById("registerPassword").value = "";
                        document.getElementById("registerNamaPegawai").value = "";
                        document.getElementById("registerJabatan").value = "";
                        
                        registerFormContainer.style.display = "none"; // Close the form
                        registerOverlay.style.display = "none"; // Hide the overlay
                    } else {
                        showToast("Failed to register pegawai!", "bg-danger");
                    }
                } catch (error) {
                    console.error("Error during registration:", error);
                    showToast("An error occurred during registration.", "bg-danger");
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