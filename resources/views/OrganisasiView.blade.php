<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Request Donasi</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Styling for table with scrollable body */
        #reqDonasiTableContainer {
            max-height: 400px;
            overflow-y: auto;
        }

        /* Fix the header so it stays visible while scrolling */
        #reqDonasiTable thead th {
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
        <h2>REQUEST DONASI</h2>
        
        <!-- Form for ReqDonasi data -->
        <form id="reqDonasiForm">
            <input type="hidden" id="currentReqId">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="idTransaksiDonasi" class="form-label">ID Transaksi Donasi</label>
                    <input type="text" class="form-control" id="idTransaksiDonasi" disabled>
                </div>
                <div class="col-md-4">
                    <label for="barangRequest" class="form-label">Barang Request</label>
                    <input type="text" class="form-control" id="barangRequest" required>
                </div>
                <div class="col-md-4">
                    <label for="tanggalRequest" class="form-label">Tanggal Request</label>
                    <input type="date" class="form-control" id="tanggalRequest" disabled>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <input type="text" class="form-control" id="status" disabled>
                </div>
                <div class="col-md-4">
                    <label for="idOrganisasi" class="form-label">ID Organisasi</label>
                    <input type="text" class="form-control" id="idOrganisasi" disabled>
                </div>
            </div>

            <div class="row">
                <div class="col-12 btn-container">
                    <button type="submit" class="btn btn-success">Update Request</button>
                    <button type="button" class="btn btn-danger" id="deleteButton">Delete</button>
                    <button type="button" class="btn btn-primary" id="createButton">Create Request</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Floating Create Form -->
    <div id="createOverlay" class="overlay"></div>
    <div class="register-form-container" id="createFormContainer">
        <h4>Create New Request Donasi</h4>
        <form id="createForm">
            <div class="mb-3">
                <label for="createBarangRequest" class="form-label">Barang Request</label>
                <input type="text" class="form-control" id="createBarangRequest" required>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-success">Create</button>
                <button type="button" class="btn btn-secondary" id="closeCreateForm">Close</button>
            </div>
        </form>
    </div>

    <!-- Toast notification -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true" id="notificationToast">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">
                    Action completed successfully!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <h3>Request Donasi Data</h3>
        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by ID Transaksi or Barang Request">

        <div id="reqDonasiTableContainer">
            <table class="table table-bordered" id="reqDonasiTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ID Transaksi Donasi</th>
                        <th>ID Organisasi</th>
                        <th>Barang Request</th>
                        <th>Tanggal Request</th>
                        <th>Status</th>
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
            const auth_token = localStorage.getItem('auth_token');
            const userRole = localStorage.getItem('user_role');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Bootstrap toast initialization
            const toastElement = document.getElementById('notificationToast');
            const bootstrapToast = new bootstrap.Toast(toastElement);

            // Check user role
            if (userRole !== 'organisasi') {
                console.log('User is not Organisasi');
                showToast(`Your user role is conflicted, Redirecting...`, "bg-danger");
                setTimeout(() => {
                    window.location.href = '/UsersLogin';
                }, 2000);
            }

            const tableBody = document.getElementById("tableBody");
            const searchInput = document.getElementById("searchInput");
            const form = document.getElementById("reqDonasiForm");
            const createButton = document.getElementById("createButton");
            const closeCreateForm = document.getElementById("closeCreateForm");
            const createFormContainer = document.getElementById("createFormContainer");
            const createOverlay = document.getElementById("createOverlay");
            
            let reqDonasiData = [];
            let currentReqId = null;
            let userOrganisasiId = null;

            // Toast function
            function showToast(message, bgColor = 'bg-primary') {
                const toast = document.getElementById('notificationToast');
                const toastMessage = document.getElementById('toastMessage');
                
                // Set message
                toastMessage.textContent = message;
                
                // Update background color
                toast.className = `toast align-items-center text-white border-0 ${bgColor}`;
                
                // Show toast using Bootstrap
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
            }

            // Get current user's organisasi ID
            async function getUserOrganisasiId() {
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/user/profile", {
                        method: "GET",
                        headers: { 
                            "Authorization": `Bearer ${auth_token}`,
                            "Accept": "application/json" 
                        },
                    });

                    const data = await response.json();
                    if (data.status === "success") {
                        userOrganisasiId = data.data.idOrganisasi;
                        document.getElementById("idOrganisasi").value = userOrganisasiId;
                        return userOrganisasiId;
                    } else {
                        console.error("Error fetching user profile:", data);
                        return null;
                    }
                } catch (error) {
                    console.error("Error fetching user profile:", error);
                    return null;
                }
            }

            // Fetch reqDonasi data
            async function fetchReqDonasi() {
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/reqdonasi", {
                        method: "GET",
                        headers: { 
                            "Authorization": `Bearer ${auth_token}`,
                            "Accept": "application/json"
                        },
                    });

                    const data = await response.json();
                    if (data.status === "success") {
                        reqDonasiData = data.data;
                        renderTable(reqDonasiData);
                    } else {
                        console.error("Error loading data:", data);
                        showToast("Failed to load request donasi data.", "bg-danger");
                    }
                } catch (error) {
                    console.error("Error fetching req donasi data:", error);
                    showToast("An error occurred while fetching data.", "bg-danger");
                }
            }

            // Format date for display
            function formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString();
            }

            // Render the table with fetched data
            function renderTable(data) {
                tableBody.innerHTML = "";

                if (!data || data.length === 0) {
                    const row = tableBody.insertRow();
                    const cell = row.insertCell(0);
                    cell.colSpan = 6;
                    cell.classList.add("no-data-message");
                    cell.innerText = "No request donasi data available.";
                    return;
                }

                data.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${item.idOrganisasi|| '-'}</td>
                        <td>${item.idTransaksiDonasi || '-'}</td>
                        <td>${item.barangRequest || '-'}</td>
                        <td>${formatDate(item.tanggalRequest)}</td>
                        <td>${item.status || '-'}</td>
                    `;
                    row.addEventListener("click", () => populateForm(item));
                    tableBody.appendChild(row);
                });
            }

            // Populate the form when a row is clicked
            function populateForm(item) {
                document.getElementById("currentReqId").value = item.id;
                document.getElementById("idTransaksiDonasi").value = item.idTransaksiDonasi || '';
                document.getElementById("idOrganisasi").value = item.idOrganisasi || '';
                document.getElementById("barangRequest").value = item.barangRequest || '';
                document.getElementById("tanggalRequest").value = item.tanggalRequest || '';
                document.getElementById("status").value = item.status || 'pending';
                currentReqId = item.id;
            }

            // Update ReqDonasi data
            form.addEventListener("submit", async function(e) {
                e.preventDefault();

                if (!currentReqId) {
                    showToast("Please select a request from the table first.", "bg-warning");
                    return;
                }

                const updatedData = {
                    barangRequest: document.getElementById("barangRequest").value
                };

                try {
                    const response = await fetch(`http://127.0.0.1:8000/api/reqdonasi/${currentReqId}`, {
                        method: "PUT",
                        headers: { 
                            "Accept": "application/json",
                            "Content-Type": "application/json",
                            "Authorization": `Bearer ${auth_token}`,
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify(updatedData)
                    });

                    if (response.ok) {
                        const result = await response.json();

                        if (result.status === "success") {
                            showToast("Request donasi updated successfully!", "bg-success");
                            fetchReqDonasi();
                        } else {
                            showToast(`Failed to update request: ${result.message || 'Unknown error'}`, "bg-danger");
                        }
                    } else {
                        console.error("Server error:", response.status, response.statusText);
                        try {
                            const errorText = await response.text();
                            showToast(`Server error (${response.status}): ${errorText.substring(0, 100)}...`, "bg-danger");
                        } catch (parseError) {
                            showToast(`Server error (${response.status})`, "bg-danger");
                        }
                    }
                } catch (error) {
                    console.error("Client error updating request:", error);
                    showToast(`An error occurred while updating: ${error.message}`, "bg-danger");
                }
            });

            // Delete ReqDonasi
            document.getElementById("deleteButton").addEventListener("click", async function() {
                if (!currentReqId) {
                    showToast("Please select a request from the table first.", "bg-warning");
                    return;
                }

                if (confirm("Are you sure you want to delete this request?")) {
                    try {
                        const response = await fetch(`http://127.0.0.1:8000/api/reqdonasi/${currentReqId}`, {
                            method: "DELETE",
                            headers: { 
                                "Authorization": `Bearer ${auth_token}`,
                                "X-CSRF-TOKEN": csrfToken
                            }
                        });

                        const result = await response.json();

                        if (result.status === "success") {
                            showToast("Request donasi deleted successfully!", "bg-success");

                            // Clear the form
                            document.getElementById("currentReqId").value = "";
                            document.getElementById("idTransaksiDonasi").value = "";
                            document.getElementById("barangRequest").value = "";
                            document.getElementById("tanggalRequest").value = "";
                            document.getElementById("status").value = "";

                            currentReqId = null;

                            fetchReqDonasi();
                        } else {
                            showToast("Failed to delete request!", "bg-danger");
                            console.error("Error deleting request:", result);
                        }
                    } catch (error) {
                        console.error("Error deleting request:", error);
                        showToast("An error occurred while deleting.", "bg-danger");
                    }
                }
            });

            // Search functionality
            searchInput.addEventListener("input", function() {
                const searchTerm = this.value.toLowerCase();

                if (searchTerm === "") {
                    renderTable(reqDonasiData);
                    return;
                }

                const filteredData = reqDonasiData.filter(item => {
                    return (
                        (item.idTransaksiDonasi && item.idTransaksiDonasi.toLowerCase().includes(searchTerm)) ||
                        (item.barangRequest && item.barangRequest.toLowerCase().includes(searchTerm))
                    );
                });

                renderTable(filteredData);
            });

            // Open Create Form
            createButton.addEventListener("click", function () {
                document.getElementById("createBarangRequest").value = "";
                createFormContainer.style.display = "block";
                createOverlay.style.display = "block";
            });

            // Close Create Form
            closeCreateForm.addEventListener("click", function () {
                createFormContainer.style.display = "none";
                createOverlay.style.display = "none";
            });

            // Close form if clicked outside
            createOverlay.addEventListener("click", function () {
                createFormContainer.style.display = "none";
                createOverlay.style.display = "none";
            });

            // Create New Request Donasi
            document.getElementById("createForm").addEventListener("submit", async function (e) {
                e.preventDefault();

                // Get organisasi ID if not already set
                if (!userOrganisasiId) {
                    await getUserOrganisasiId();
                }

                const createData = {
                    barangRequest: document.getElementById("createBarangRequest").value
                };

                try {
                    const response = await fetch("http://127.0.0.1:8000/api/create/reqdonasi", {
                        method: "POST",
                        headers: { 
                            "Content-Type": "application/json", 
                            "Accept": "application/json",
                            "Authorization": `Bearer ${auth_token}`,
                            "X-CSRF-TOKEN": csrfToken 
                        },
                        body: JSON.stringify(createData),
                    });

                    const result = await response.json();
                    if (result.status === "success") {
                        fetchReqDonasi();
                        showToast("Request donasi created successfully!", "bg-success");

                        // Clear form field
                        document.getElementById("createBarangRequest").value = "";

                        createFormContainer.style.display = "none";
                        createOverlay.style.display = "none";
                    } else {
                        showToast(`Failed to create request: ${result.message || 'Unknown error'}`, "bg-danger");
                    }
                } catch (error) {
                    console.error("Error during creation:", error);
                    showToast("An error occurred during creation.", "bg-danger");
                }
            });

            // Initialize
            getUserOrganisasiId().then(() => {
                fetchReqDonasi();
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>