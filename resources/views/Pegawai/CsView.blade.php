<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Penitip</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />

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
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    @include('layouts.navbar')



    <div class="container mt-4 ">
        <h2>Data Penitip</h2>

        <!-- Form for Penitip data -->
        <form id="penitipForm" class="mb-4">
            <input type="hidden" id="currentPenitipId" />
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="namaPenitip" class="form-label">Nama Penitip</label>
                    <input type="text" class="form-control" id="namaPenitip" required />
                </div>
                <div class="col-md-4">
                    <label for="nik" class="form-label">NIK</label>
                    <input type="text" class="form-control" id="nik" required />
                </div>
                <div class="col-md-4">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" required />
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="idTopSeller" class="form-label">ID Top Seller</label>
                    <input type="text" class="form-control" id="idTopSeller" disabled />
                </div>
                <div class="col-md-4">
                    <label for="idDompet" class="form-label">ID Dompet</label>
                    <input type="text" class="form-control" id="idDompet" disabled />
                </div>
            </div>

            <div class="row">
                <div class="col-12 btn-container">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-danger" id="deleteButton">Delete</button>
                    <button type="button" class="btn btn-primary" id="registerButton">Register Penitip</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Floating Register Form -->
    <div id="registerOverlay" class="overlay"></div>
    <div class="register-form-container" id="registerFormContainer">
        <h4>Register New Penitip</h4>
        <form id="registerForm">
            <div class="mb-3">
                <label for="registerNamaPenitip" class="form-label">Nama Penitip</label>
                <input type="text" class="form-control" id="registerNamaPenitip" required />
            </div>
            <div class="mb-3">
                <label for="registerNik" class="form-label">NIK</label>
                <input type="text" class="form-control" id="registerNik" required />
            </div>
            <div class="mb-3">
                <label for="registerUsername" class="form-label">Username</label>
                <input type="text" class="form-control" id="registerUsername" required />
            </div>
            <div class="mb-3">
                <label for="registerPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="registerPassword" required />
            </div>
            <div class="mb-3 d-flex gap-2">
                <button type="submit" class="btn btn-success">Register</button>
                <button type="button" class="btn btn-secondary" id="closeRegisterForm">Close</button>
            </div>
        </form>
    </div>

    <!-- Toast notification -->
    <div class="toast-container">
        <div
            class="toast align-items-center text-bg-primary border-0"
            role="alert"
            aria-live="assertive"
            aria-atomic="true"
            id="successToast"
        >
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">Action completed successfully!</div>
                <button
                    type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"
                    aria-label="Close"
                ></button>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <h3>Penitip Data</h3>
        <input
            type="text"
            id="searchInput"
            class="form-control mb-3"
            placeholder="Search by username or name"
        />

        <div id="penitipTableContainer">
            <table class="table table-bordered" id="penitipTable">
                <thead>
                    <tr>
                        <th>ID Penitip</th>
                        <th>ID Top Seller</th>
                        <th>ID Dompet</th>
                        <th>Username</th>
                        <th>Nama Penitip</th>
                        <th>NIK</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Data will be populated here from the server -->
                </tbody>
            </table>
        </div>
    </div>

    <footer class="bg-dark text-white text-center p-3">
        <p>&copy; 2025 Reusemart</p>
    </footer>

  <script>
        document.addEventListener("DOMContentLoaded", function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag

            const tableBody = document.querySelector("#tableBody");
            const searchInput = document.getElementById("searchInput");
            const form = document.getElementById("penitipForm");
            const registerButton = document.getElementById("registerButton");
            const closeRegisterForm = document.getElementById("closeRegisterForm");
            const registerFormContainer = document.getElementById("registerFormContainer");
            const registerOverlay = document.getElementById("registerOverlay");
            let penitipData = [];
            let currentPenitipId = null;

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

            // Fetch Penitip Data
            async function fetchPenitip() {
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/penitip", {
                        method: "GET",
                        headers: { "Authorization": `Bearer ${localStorage.getItem('authToken')}` },
                    });

                    const data = await response.json();
                    if (data.status === "success" && data.data.length > 0) {
                        penitipData = data.data;
                        renderTable(penitipData);
                    } else {
                        alert("Gagal memuat data penitip.");
                        console.error("Error loading data:", data);
                    }
                } catch (error) {
                    console.error("Error fetching penitip data:", error);
                    alert("An error occurred while fetching data. Check the console for details.");
                }
            }

            // Render the table with fetched data
            function renderTable(data) {
                tableBody.innerHTML = ""; // Clear the table before rendering new data

                // Prioritize penitips with idTopSeller first
                const sortedData = data.sort((a, b) => {
                    return (b.idTopSeller ? 1 : 0) - (a.idTopSeller ? 1 : 0);  // 1st priority: have idTopSeller
                });

                if (!data || data.length === 0) {
                    const row = tableBody.insertRow();
                    const cell = row.insertCell(0);
                    cell.colSpan = 7;
                    cell.classList.add("no-data-message"); // Add a class for empty state
                    cell.innerText = "No penitip data available.";
                    return;
                }

                sortedData.forEach(item => {
                    if (item.deleted_at !== null) return;
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${item.idPenitip}</td>
                        <td>${item.idTopSeller || '-'}</td>
                        <td>${item.idDompet}</td>
                        <td>${item.username}</td>
                        <td>${item.namaPenitip}</td>
                        <td>${item.nik}</td>
                    `;
                    row.addEventListener("click", () => populateForm(item));  // Add click event to the row
                    tableBody.appendChild(row);
                });
            }

            // Populate the form when a row is clicked
            function populateForm(item) {
                document.getElementById("currentPenitipId").value = item.idPenitip;
                document.getElementById("idTopSeller").value = item.idTopSeller || '';  // Set idTopSeller
                document.getElementById("idDompet").value = item.idDompet || '';        // Set idDompet
                document.getElementById("username").value = item.username || '';         // Set username
                document.getElementById("namaPenitip").value = item.namaPenitip || '';   // Set namaPenitip
                document.getElementById("nik").value = item.nik || '';                   // Set NIK
                
                // Store the current Penitip ID
                currentPenitipId = item.idPenitip;
            }

            // Save updated Penitip data
            form.addEventListener("submit", async function(e) {
                e.preventDefault();
                
                // Check if a penitip is selected
                if (!currentPenitipId) {
                    alert("Please select a penitip from the table first.");
                    return;
                }
                
                const updatedData = {
                    // idPenitip: currentPenitipId,
                    username: document.getElementById("username").value,
                    namaPenitip: document.getElementById("namaPenitip").value,
                    nik: document.getElementById("nik").value
                };
                
                try {
                    console.log("Updating penitip with ID:", currentPenitipId);
                    console.log("Update data:", updatedData);
                    
                    // First try with PUT method
                    let response = await fetch(`http://127.0.0.1:8000/api/penitip/update/${currentPenitipId}`, {
                        method: "PUT",
                        headers: { 
                            "Accept": "application/json",
                            "Content-Type": "application/json",
                            "Authorization": `Bearer ${localStorage.getItem('authToken')}`,
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify(updatedData)
                    });
                    // Check if the request was successful
                    if (response.ok) {
                        const result = await response.json();
                        console.log("Response:", result);
                        
                        if (result.status === "success") {
                            showToast("Penitip updated successfully!", "bg-success");
                            fetchPenitip(); // Refresh the table data
                        } else {
                            showToast(`Failed to update penitip: ${result.message || 'Unknown error'}`, "bg-danger");
                            console.error("Error updating penitip:", result);
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
                    console.error("Client error updating penitip:", error);
                    showToast(`An error occurred while updating: ${error.message}`, "bg-danger");
                }
            });

            // Delete Penitip
            document.getElementById("deleteButton").addEventListener("click", async function() {
                if (!currentPenitipId) {
                    alert("Please select a penitip from the table first.");
                    return;
                }
                
                if (confirm("Are you sure you want to delete this penitip?")) {
                    try {
                        const response = await fetch(`http://127.0.0.1:8000/api/penitip/delete/${currentPenitipId}`, {
                            method: "DELETE",
                            headers: { 
                                "Authorization": `Bearer ${localStorage.getItem('authToken')}`,
                                "X-CSRF-TOKEN": csrfToken
                            }
                        });
                        
                        const result = await response.json();
                        
                        if (result.status === "success") {
                            showToast("Penitip deleted successfully!", "bg-success");
                            
                            // Clear the form
                            document.getElementById("currentPenitipId").value = "";
                            document.getElementById("idTopSeller").value = "";
                            document.getElementById("idDompet").value = "";
                            document.getElementById("username").value = "";
                            document.getElementById("namaPenitip").value = "";
                            document.getElementById("nik").value = "";
                            
                            currentPenitipId = null;
                            
                            fetchPenitip(); // Refresh the table data
                        } else {
                            showToast("Failed to delete penitip!", "bg-danger");
                            console.error("Error deleting penitip:", result);
                        }
                    } catch (error) {
                        console.error("Error deleting penitip:", error);
                        showToast("An error occurred while deleting.", "bg-danger");
                    }
                }
            });

            // Search functionality
            searchInput.addEventListener("input", function() {
                const searchTerm = this.value.toLowerCase();
                
                if (searchTerm === "") {
                    renderTable(penitipData); // Show all data if search is empty
                    return;
                }
                
                // Filter the data based on search term
                const filteredData = penitipData.filter(item => {
                    return (
                        (item.username && item.username.toLowerCase().includes(searchTerm)) ||
                        (item.namaPenitip && item.namaPenitip.toLowerCase().includes(searchTerm))
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

            // Register New Penitip
            document.getElementById("registerForm").addEventListener("submit", async function (e) {
                e.preventDefault();
                
                const registerData = {
                    namaPenitip: document.getElementById("registerNamaPenitip").value,
                    nik: document.getElementById("registerNik").value,
                    username: document.getElementById("registerUsername").value,
                    password: document.getElementById("registerPassword").value,
                };
        
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/penitip/register", {
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
                        fetchPenitip(); // Re-fetch data after successful registration
                        showToast("Penitip registered successfully!", "bg-success");
                        
                        // Clear form fields
                        document.getElementById("registerUsername").value = "";
                        document.getElementById("registerPassword").value = "";
                        document.getElementById("registerNamaPenitip").value = "";
                        document.getElementById("registerNik").value = "";
                        
                        registerFormContainer.style.display = "none"; // Close the form
                        registerOverlay.style.display = "none"; // Hide the overlay
                    } else {
                        showToast("Failed to register penitip!", "bg-danger");
                    }
                } catch (error) {
                    console.error("Error during registration: "+error, error);

                    showToast("An error occurred during registration.", "bg-danger");
                }
            });

            // Initial fetch when the page loads
            fetchPenitip();
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
