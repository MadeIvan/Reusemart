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
@include('layouts.navbar')
    <div class="container mt-4" style = "margin-top: 5% !important; margin-left: 5% !important;"  >
        <h2>Menu Gudang</h2>

        <!-- Form for Pegawai data -->
        <form id="PegawaiForm" >
            

            <div class="row">
                <div class="col-12 btn-container">
                    <button type="button" class="btn btn-success" id="addBarangButton">Tambah barang</button>
                    <button type="button" class="btn btn-primary" id="updatestatus">Update Status</button>
                </div>
            </div>
        </form>



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
                <label for="tanggalPenitipan" class="form-label">Tanggal Penitipan Selesai</label>
                <input type="text" class="form-control" id="tanggalPenitipan" disabled >
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
                    <input type="text" class="form-control" id="idBarang" disabled>
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
                    <input type="number" step="0.01" min ="0" class="form-control" id="beratBarang" required>
                </div>
                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select class="form-select" id="kategori" required>
                        <option value="">---</option>
                        <option value="Elektronik & Gadget">Elektronik & Gadget</option>
                        <option value="Pakaian & Aksesori">Pakaian & Aksesori</option>
                        <option value="Perabotan Rumah Tangga">Perabotan Rumah Tangga</option>
                        <option value="Buku, Alat Tulis, & Peralatan Sekolah">Buku, Alat Tulis, & Peralatan Sekolah</option>
                        <option value="Hobi, Mainan, & Koleksi">Hobi, Mainan, & Koleksi</option>
                        <option value="Perlengkapan Bayi & Anak">Perlengkapan Bayi & Anak</option>
                        <option value="Otomotif & Aksesori">Otomotif & Aksesori</option>
                        <option value="Perlengkapan Taman & Outdoor">Perlengkapan Taman & Outdoor</option>
                        <option value="Peralatan Kantor & Industri">Peralatan Kantor & Industri</option>
                        <option value="Kosmetik & Perawatan Diri">Kosmetik & Perawatan Diri</option>
                    </select>
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
                    <input type="date" class="form-control" id="periodeGaransi">
                </div>
                <div class="mb-3">
                    <label for="hargaBarang" class="form-label">Harga Barang</label>
                    <input type="number" class="form-control" min ="0" id="hargaBarang" required>
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
                    
                    <!-- Drag & Drop area -->
                    <div id="drop-area" 
                        style="border: 2px dashed #ccc; padding: 20px; text-align: center; cursor: pointer;">
                        <p>Drag & drop images here or click to select files</p>
                        <input type="file" class="form-control" id="image" multiple accept="image/*" style="display:none;">
                    </div>

                    <!-- Preview container for selected images -->
                    <div id="preview" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px;"></div>

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
    



    <div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Aksi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex justify-content-around">
        <button id="printNotaBtn" type="button" class="btn btn-warning">Cetak Nota</button>
        <button id="editBtn" type="button" class="btn btn-primary">Edit Data</button>
      </div>
    </div>
  </div>
</div>
    <!-- Toast notification
    
    
    
    -->
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
         <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search Data Transaksi">
        <div id="pegawaiTableContainer">
            <table class="table table-bordered" id="pegawaiTable">
                <thead>
                    <tr>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Nama Penitip</th>
                        <th>Status</th>
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
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const pegawaiDataString = localStorage.getItem("pegawaiData");
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag
            let currentEditItem = null;
            const tableBody = document.getElementById("tableBody");
            const formData = new FormData();
            const actionModal = new bootstrap.Modal(document.getElementById('actionModal'), {});
            // const searchInput = document.getElementById("searchInput");
            // const form = document.getElementById("PegawaiForm");
            // const registerButton = document.getElementById("registerButton");
            // const closeRegisterForm = document.getElementById("closeRegisterForm");
            // const registerFormContainer = document.getElementById("registerFormContainer");
            // const registerOverlay = document.getElementById("registerOverlay");
            // let pegawaiData = [];
            // let currentPegawaiId = null;
            
            // SEARCH
            let fileNamesArray = [];
            let selectedHunterId = null;
            const hunterSelected = document.getElementById('idPegawai2');
                hunterSelected.addEventListener('change', function() {
                selectedHunterId = this.value;
                console.log('Selected Hunter ID:', selectedHunterId);
            });

            


            const penitipSelect = document.getElementById('idPenitip');
            penitipSelect.addEventListener('change', function() {
                selectedPenitipId = this.value;
                console.log('Selected Penitip ID:', selectedPenitipId);
            });
            const searchInput = document.getElementById("searchInput");
            let pegawaiData = []; // to store fetched data globally
            function deepSearch(obj, searchTerm) {
                if (obj === null || obj === undefined) return false;

                if (typeof obj === 'string' || typeof obj === 'number' || typeof obj === 'boolean') {
                    return String(obj).toLowerCase().includes(searchTerm);
                }

                if (Array.isArray(obj)) {
                    return obj.some(item => deepSearch(item, searchTerm));
                }

                if (typeof obj === 'object') {
                    return Object.values(obj).some(value => deepSearch(value, searchTerm));
                }

                return false;
            }
            searchInput.addEventListener("input", function () {
                const searchTerm = this.value.trim().toLowerCase();

                if (!searchTerm) {
                    renderTable(pegawaiData);
                    return;
                }

                const filteredData = pegawaiData.filter(item => deepSearch(item, searchTerm));

                renderTable(filteredData);
            });
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
                    const response = await fetch("http://127.0.0.1:8000/api/indexall", {
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
                        alert("Gagal memuat data barang.");
                        console.error("Error loading data:", data);
                    }
                } catch (error) {
                    console.error("Error fetching pegawai data:", error);
                    alert("An error occurred while fetching data. Check the console for details.");
                }
            }



            // Render the table with fetched data
            let currentItem = null;     // full item object for editing
            let currentItemId = null;   // just id for printing

            function renderTable(data) {
                tableBody.innerHTML = ""; // Clear the table before rendering new data
                data.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td id="${item.idBarang}">${item.idBarang}</td>
                        <td>${item.namaBarang || '-'}</td>
                        <td>${item.namaPenitip || '-'}</td>
                        <td>${item.statusBarang}</td>
                        <td>${item.tanggalPenitipanSelesai || '-'}</td>
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

            // Print Nota button
            document.getElementById('printNotaBtn').addEventListener('click', () => {
                if (!currentItemId) return;
                const url = `/api/nota-penitipan/${currentItemId}/pdf`;
                window.open(url, '_blank');
                actionModal.hide();
            });

            // Edit button
            document.getElementById('editBtn').addEventListener('click', () => {
                if (!currentItem) return;
                openEditModal(currentItem); // pass full item object
                actionModal.hide();
            });

            const pegawai = JSON.parse(pegawaiDataString);
            const addBarangButton = document.getElementById("addBarangButton");
            const addBarangModal = new bootstrap.Modal(document.getElementById("addBarangModal"));
            const addBarangForm = document.getElementById("addBarangForm");
            const namaPegawai = pegawai.namaPegawai
            const idPegawai = pegawai.idPegawai 
            let barangData={};  

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
                addBarangDetailForm.reset();
                let kategoriSelect ='';
                // toggleGaransiInputs();
                resetImagePreview();
                // addBarangDetailModal.reset();
                addBarangDetailModal.show();
            });
            const addBarangDetailForm = document.getElementById("addBarangDetailForm");
            
            addBarangDetailForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            if (currentEditItem) {
                document.getElementById("idPegawai1").value = currentEditItem.idPegawai1 || '';  // adjust property names
                document.getElementById("idPegawai2").value = currentEditItem.idPegawai2 || '';
                document.getElementById("idPenitip").value = currentEditItem.transaksiPenitipan?.penitip?.username || '';
                document.getElementById("tanggalPenitipan").value = currentEditItem.tanggalPenitipanSelesai || '';
                // etc, fill all needed fields
            }
            if (filesArray.length < 2 || filesArray.length > 5 ) {
                e.preventDefault();
                alert('Please upload at least 2 images and no more than 5 images :D');
                return false;
            }
            console.log("idBarang value b4 form : ",  document.getElementById("idBarang").value )
                
            barangData = {
                idBarang: document.getElementById("idBarang").value,
                namaBarang: document.getElementById("namaBarang").value,
                beratBarang: document.getElementById("beratBarang").value,
                garansiBarang: document.getElementById("garansiBarang").value,
                periodeGaransi: document.getElementById("periodeGaransi").value,
                hargaBarang: document.getElementById("hargaBarang").value,
                haveHunter: document.getElementById("haveHunter").value,
                statusBarang: document.getElementById("statusBarang").value,
                kategori: document.getElementById("kategori").value,
                garansiBarang: document.getElementById("garansiBarang").value,
            };
            window.lastCreatedBarangId = document.getElementById("idBarang").value;
            console.log("data barang", barangData);

            const firstModal = bootstrap.Modal.getInstance(document.getElementById("addBarangDetailModal"));
            firstModal.hide();
            toggleHunterSelect();
            // let hunterSelect = document.getElementById("haveHunter").value;
            addBarangDetailForm.reset();
            resetImagePreview();
           

            const addBarangModal = new bootstrap.Modal(document.getElementById("addBarangModal"));
            document.getElementById("idPegawai1").value = namaPegawai || '';
            const today = new Date();
            const plus30Days = new Date(today);
            plus30Days.setDate(today.getDate() + 30);

            
            

            document.getElementById("tanggalPenitipan").value = plus30Days.toISOString().split("T")[0];
            fetchHunters();
            fetchPenitip();
            addBarangModal.show();
            // secondModal.show();
            // const imageInput = document.getElementById("image");
            // if (imageInput.files.length > 0) {
            //     formData.append("image", imageInput.files[0]);
            // }

           
        });

            // Handle date calculation (30 days after tanggalPenitipan)
            document.getElementById("tanggalPenitipan").addEventListener("change", function () {
                const tanggalPenitipan = new Date(this.value);
                const tanggalPenitipanSelesai = new Date(tanggalPenitipan);
                tanggalPenitipanSelesai.setDate(tanggalPenitipan.getDate() + 30); // Add 30 days
                const day = String(tanggalPenitipanSelesai.getDate()).padStart(2, '0');
                const month = String(tanggalPenitipanSelesai.getMonth() + 1).padStart(2, '0'); // Months are 0-based
                const year = String(tanggalPenitipanSelesai.getFullYear()).slice(-2); // Last two digits

                const formattedDate = `${year}/${month}/${day}`;
                // document.getElementById("idPegawai1").value = namaPegawai || '';
                document.getElementById("tanggalPenitipanSelesai").value = formattedDate || '';
            });

            // Handle form submission (when the user clicks 'Save')
            addBarangForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            resetImagePreview();
            addBarangForm.reset();
            
            
            const confirmed = window.confirm("Yakin?");
            if (!confirmed) {
                // User clicked Cancel, stop here
                addBarangDetailModal.close();
                return;
            }
            
            // Log all formData entries
            

            try {
                console.log("barang data : ",barangData);
                const response = await fetch("http://127.0.0.1:8000/api/barang", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify(barangData),
                });

                const result = await response.json();
                
                if (response.ok && result.status) {
                    // alert("Barang created successfully!");

                    // Save last created idBarang
                    window.lastCreatedBarangId = result.data.idBarang;

                    // Close Barang modal
                    const addBarangDetailModal = bootstrap.Modal.getInstance(document.getElementById("addBarangDetailModal"));
                    addBarangDetailModal.hide();
                    toggleGaransiInputs();

                    // Open Transaksi Penitipan modal
                    

                    
                } else {
                    alert("Failed to create Barang.");
                    console.error(result.message || "Unknown error");
                    // console.log(formData);
                }
            } catch (error) {
                console.log("Form Data:", formData);
                console.error("Error creating Barang:", error);
                alert("An error occurred while creating the Barang.");
            }

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
                
                idPegawai1: pegawai.idPegawai,
                // idPegawai2: idPegawai2Value !== "" ? idPegawai2Value : null,
                idPenitip: selectedPenitipId,
                // tanggalPenitipan: tanggalPenitipan,
                // tanggalPenitipanSelesai: tanggalPenitipanSelesai,
                totalHarga:barangData.hargaBarang,
                idBarang: window.lastCreatedBarangId,
            };
            if (selectedHunterId && selectedHunterId.trim() !== '') {
                transaksiData.idPegawai2 = selectedHunterId;
            }       
            const idBarangValue = window.lastCreatedBarangId;
           
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

                const payload = {
                    id: idBarangValue,  // some id you want to send
                    image1: fileNamesArray[0] || null,
                    image2: fileNamesArray[1] || null,
                    image3: fileNamesArray[2] || null,
                    image4: fileNamesArray[3] || null,
                    image5: fileNamesArray[4] || null,
                };
                console.log("list image on payload : ",payload);
                // Send POST request using fetch API
                fetch('http://127.0.0.1:8000/api/addimages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
                    "X-CSRF-TOKEN": csrfToken
                    // add authorization headers if needed here
                },
                body: JSON.stringify(payload),
                })
                    .then(response => response.json())
                    .then(data => {
                    console.log('Success:', data);
                })
                    .catch(error => {
                    console.error('Error:', error);
                });

                if (response.ok && result.status) {
                    alert("Transaksi Penitipan created successfully!");

                    // Close modal
                    const addBarangModal = bootstrap.Modal.getInstance(document.getElementById("addBarangModal"));
                    addBarangModal.hide();
                    toggleGaransiInputs();

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
            fetchPegawai();
            toggleHunterSelect();
        });

            async function fetchPenitip(selectedUsername = null) {
                const penitipSelect = document.getElementById("idPenitip");
                console.log("penitip id 1 :", penitipSelect.value);
                penitipSelect.innerHTML = `<option value="">Pilih Penitip</option>`;
                try {
                    const response = await fetch("http://127.0.0.1:8000/api/getpenitip", {
                        method: "GET",
                        headers: {
                            "Authorization": `Bearer ${localStorage.getItem('auth_token')}`
                        }
                    });

                    const data = await response.json();
                    console.log("Fetched Penitip:", data);

                    // const penitipSelect = document.getElementById("idPenitip");
                    // penitipSelect.innerHTML = `<option value="">Pilih Penitip</option>`; // reset dropdown

                    if (data.status && data.data.length > 0) {
                        data.data.forEach(penitip => {
                            const option = document.createElement("option");
                            option.value = penitip.idPenitip;
                            option.textContent = penitip.username;
                            penitipSelect.appendChild(option);
                        });

                        if (selectedUsername) {
                            penitipSelect.value = selectedUsername;
                            console.log("penitip id 2 :", penitipSelect);
                            
                        }
                    } else {
                        console.warn("No Penitip available");
                    }
                } catch (error) {
                    console.error("Error fetching Penitip:", error);
                    alert("Gagal memuat data Penitip");
                }
            }
            const dropArea = document.getElementById('drop-area');
            const fileInput = document.getElementById('image');
            const preview = document.getElementById('preview');
            

            let filesArray = []; // Store selected files here
            // let fileNamesArray = [];
            dropArea.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', () => {
                handleFiles(fileInput.files);
            });

            // Drag and drop handlers (same as before)
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                dropArea.style.borderColor = '#000';
                dropArea.style.backgroundColor = '#f0f0f0';
                }, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                dropArea.style.borderColor = '#ccc';
                dropArea.style.backgroundColor = '#fff';
                }, false);
            });
            dropArea.addEventListener('drop', e => {
                const dt = e.dataTransfer;
                handleFiles(dt.files);
            });

            
            function handleFiles(files) {
                for (const file of files) {
                if (!file.type.startsWith('image/')) {
                    alert('Only image files are allowed!');
                    continue;
                }
                filesArray.push(file);
                fileNamesArray.push(file.name); 
                console.log("list patn : ", fileNamesArray);
                previewFile(file);
                }
                toggleDropAreaVisibility();
            }

            function previewFile(file) {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onloadend = function() {
                const container = document.createElement('div');
                container.style.position = 'relative';
                container.style.width = '100px';
                container.style.height = '100px';

                // Create the image element
                const img = document.createElement('img');
                img.src = reader.result;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '8px';
                img.style.cursor = 'pointer';

                // Click to show fullscreen image
                img.addEventListener('click', () => {
                    openFullscreenPreview(reader.result);
                });

                // Create delete button
                const btn = document.createElement('button');
                btn.innerHTML = '&times;';
                btn.style.position = 'absolute';
                btn.style.top = '2px';
                btn.style.right = '2px';
                btn.style.background = 'rgba(255, 0, 0, 0.6)';
                btn.style.color = 'white';
                btn.style.border = 'none';
                btn.style.borderRadius = '50%';
                btn.style.width = '20px';
                btn.style.height = '20px';
                btn.style.cursor = 'pointer';
                btn.title = 'Delete image';

                btn.addEventListener('click', () => {
                    const index = filesArray.indexOf(file);
                    if (index > -1) {
                    filesArray.splice(index, 1); // Remove from files array
                    }
                    container.remove(); // Remove from preview
                    toggleDropAreaVisibility();
                    // updateFileInput();  // Optional: sync input files if needed
                });

                container.appendChild(img);
                container.appendChild(btn);
                preview.appendChild(container);
                toggleDropAreaVisibility();
                };
            }

            // Fullscreen preview modal
            function openFullscreenPreview(src) {
                // Create overlay
                const overlay = document.createElement('div');
                overlay.style.position = 'fixed';
                overlay.style.top = 0;
                overlay.style.left = 0;
                overlay.style.width = '100vw';
                overlay.style.height = '100vh';
                overlay.style.backgroundColor = 'rgba(0,0,0,0.8)';
                overlay.style.display = 'flex';
                overlay.style.justifyContent = 'center';
                overlay.style.alignItems = 'center';
                overlay.style.cursor = 'zoom-out';
                overlay.style.zIndex = 9999;

                // Create full image
                const fullImg = document.createElement('img');
                fullImg.src = src;
                fullImg.style.maxWidth = '90vw';
                fullImg.style.maxHeight = '90vh';
                fullImg.style.borderRadius = '8px';
                fullImg.style.boxShadow = '0 0 20px rgba(255,255,255,0.3)';

                overlay.appendChild(fullImg);

                // Click anywhere to close
                overlay.addEventListener('click', () => {
                document.body.removeChild(overlay);
                });

                document.body.appendChild(overlay);
            }
           function toggleDropAreaVisibility() {
    const dropArea = document.getElementById('drop-area');
    const preview = document.getElementById('preview');

    // Count how many images are previewed (both existing and new)
    const hasImages = preview.children.length > 0;

    if (hasImages) {
        dropArea.style.display = 'none';
    } else {
        dropArea.style.display = 'block';
    }
}

            
            function openEditModal(item) {
                document.getElementById('preview').innerHTML = '';
                currentEditItem = item;
                console.log("current:",currentEditItem);
                console.log("openEditModal called with item:", item);
                const username = item.transaksiPenitipan?.penitip?.username || '';
                fetchPenitip(username);
                console.log(username);
                
                // Show modal
                const modalEl = document.getElementById("addBarangDetailModal");
                const modal = new bootstrap.Modal(modalEl);
                
                modal.show();
                if (currentEditItem) {
                    document.getElementById("idPegawai1").value = currentEditItem.idPegawai1 || '';  // adjust property names
                    document.getElementById("idPegawai2").value = currentEditItem.idPegawai2 || '';
                    document.getElementById("idPenitip").value = username || '';
                    document.getElementById("tanggalPenitipan").value = currentEditItem.tanggalPenitipanSelesai || '';
                    // etc, fill all needed fields
                }
                
                // Fill inputs with item data
                document.getElementById("idBarang").value = item.idBarang || '';
                document.getElementById("namaBarang").value = item.namaBarang || '';
                document.getElementById("beratBarang").value = item.beratBarang || '';
                document.getElementById("garansiBarang").value = item.garansiBarang != null ? item.garansiBarang.toString() : '';
                document.getElementById("periodeGaransi").value = item.periodeGaransi || '';
                document.getElementById("hargaBarang").value = item.hargaBarang || '';
                document.getElementById("haveHunter").value = item.haveHunter != null ? item.haveHunter.toString() : '';
                document.getElementById("statusBarang").value = item.statusBarang || 'Tersedia';
                document.getElementById("kategori").value = item.kategori || '';

                // Clear existing previews
                document.getElementById('preview').innerHTML = '';
                console.log("item.imagesBarang object:", item.imagesBarang);
                // Handle existing images
                let images = [];

                // If item.imagesBarang exists, get images from image1 to image5
                if (item.imagesBarang) {
                    images = [
                        item.imagesBarang.image1,
                        item.imagesBarang.image2,
                        item.imagesBarang.image3,
                        item.imagesBarang.image4,
                        item.imagesBarang.image5,
                    ].filter(src => src && src.trim() !== ''); // filter out empty/null
                }
                const baseUrl = "http://127.0.0.1:8000/";
                console.log("Images array:", images);
                images = images.map(src => baseUrl + src);
                images.forEach(src => {
                    previewExistingImage(src);
                });

    // Reset filesArray and fileNamesArray if you use them to track new uploads
                filesArray = [];
                fileNamesArray = [];
                toggleDropAreaVisibility();
            }
            function previewExistingImage(src) {
                console.log("Previewing image src:", src);
                const preview = document.getElementById('preview');

                const container = document.createElement('div');
                container.style.position = 'relative';
                container.style.width = '100px';
                container.style.height = '100px';

                // Create the image element
                const img = document.createElement('img');
                img.src = src;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '8px';
                img.style.cursor = 'pointer';

                // Click to show fullscreen image
                img.addEventListener('click', () => {
                    openFullscreenPreview(src);
                });

            // Create delete button
                const btn = document.createElement('button');
                btn.innerHTML = '&times;';
                btn.style.position = 'absolute';
                btn.style.top = '2px';
                btn.style.right = '2px';
                btn.style.background = 'rgba(255, 0, 0, 0.6)';
                btn.style.color = 'white';
                btn.style.border = 'none';
                btn.style.borderRadius = '50%';
                btn.style.width = '20px';
                btn.style.height = '20px';
                btn.style.cursor = 'pointer';
                btn.title = 'Delete image';

                // Optional: add delete behavior (depends on your app logic)
                btn.addEventListener('click', () => {
                    container.remove();
                    // If you keep track of existing images separately, remove it there too
                    toggleDropAreaVisibility();
                });

                container.appendChild(img);
                container.appendChild(btn);
                preview.appendChild(container);
        }
        function generateIdBarangPrefix(namaBarang) {
  // Get first letters of each word in uppercase
            return namaBarang.trim().charAt(0).toUpperCase();
            // return namaBarang
            //     .split(' ')
            //     .map(word => word.charAt(0).toUpperCase())
            //     .join('');
        }
        document.getElementById('namaBarang').addEventListener('input', async function () {
            const namaBarang = this.value.trim();
            if (namaBarang.length === 0) {
                document.getElementById('idBarang').value = '';
                return;
            }

            const prefix = generateIdBarangPrefix(namaBarang);

            // Call backend to get next available number for this prefix
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/generate-idbarang?prefix=${prefix}`, {
                headers: {
                    "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
                }
                });
                const data = await response.json();

                if (data.nextId) {
                document.getElementById('idBarang').value = data.nextId; // e.g. MB1, MB2, etc.
                } else {
                document.getElementById('idBarang').value = prefix + '1'; // fallback
                }
            } catch (error) {
                console.error('Error fetching next idBarang:', error);
                document.getElementById('idBarang').value = prefix + '1';
            }
            });
            const haveHunterSelect = document.getElementById('haveHunter');
            const hunterSelect = document.getElementById('idPegawai2');
            function toggleHunterSelect() {
                console.log("have Hunter value func inside : ", haveHunterSelect.value);
            if (haveHunterSelect.value === '1') { // 'Ya'
                hunterSelect.disabled = false;
                hunterSelect.required = true;
            } else  {
                hunterSelect.disabled = true;
                hunterSelect.required = false;
                hunterSelect.value = ""; // reset selection if disabled
            }
            }

            // Initial toggle on page load
            toggleHunterSelect();

            // Listen for changes on haveHunter select
            haveHunterSelect.addEventListener('change', toggleHunterSelect);


            const kategoriSelect = document.getElementById("kategori");
            const garansiDiv = document.querySelector('label[for="garansiBarang"]').parentElement;
            const periodeDiv = document.querySelector('label[for="periodeGaransi"]').parentElement;
            const garansiSelect = document.getElementById("garansiBarang");
            const periodeInput = document.getElementById("periodeGaransi");

            function toggleGaransiInputs() {
                
                if (kategoriSelect.value === "Elektronik & Gadget") {
                garansiDiv.style.display = "block";
                periodeDiv.style.display = "block";
                garansiSelect.disabled = false;
                // Make garansiBarang required
                garansiSelect.required = true;

                // Enable/disable periode based on garansiBarang value
                togglePeriodeInput();

                // Periode input required only if garansi is "Ya" (1)
                periodeInput.required = (garansiSelect.value === '1');
                } else {

                garansiDiv.style.display = "none";
                periodeDiv.style.display = "none";
                garansiSelect.disabled = true;
                garansiSelect.required = false;
                periodeInput.disabled = true;
                periodeInput.required = false;
                garansiSelect.value = "0";
                periodeInput.value = "";
                }
            }

            function togglePeriodeInput() {
                if (garansiSelect.value === '0') {
                periodeInput.disabled = true;
                periodeInput.required = false;
                periodeInput.value = "";
                } else {
                periodeInput.disabled = false;
                // periodeInput.required = true;  // Handled in toggleGaransiInputs
                }
            }

            // Initial run
            toggleGaransiInputs();

            // Event listeners
            kategoriSelect.addEventListener("change", toggleGaransiInputs);
            garansiSelect.addEventListener("change", function() {
                togglePeriodeInput();
                // Adjust required attribute on periodeInput accordingly
                periodeInput.required = (garansiSelect.value === '1');
            });
            function resetImagePreview() {
            // Clear the preview thumbnails container
                const preview = document.getElementById('preview');
                preview.innerHTML = '';

                // Clear your file tracking arrays
                filesArray = [];
                // fileNamesArray = [];

                // Reset the actual file input element
                const fileInput = document.getElementById('image');
                fileInput.value = '';

                // Show the drop area again if you hide it
                toggleDropAreaVisibility();
            }
            document.getElementById('updatestatus').addEventListener('click', function() {
    // Show a loading indicator or message
    console.log('Sending update request to API...');

    // The API endpoint
    const url = 'http://127.0.0.1:8000/api/update-barang-status'; // Adjust URL if necessary

    // Send a PUT request to update barang status
    fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
            "X-CSRF-TOKEN": csrfToken // If authorization is required
        },
    })
    .then(response => response.json())
    .then(data => {
        // Handle success (for example, show a success message)
        alert('Status updated successfully: ' + data.message); 
        fetchPenitip();
        fetchPegawai();// Show a success message
    })
    .catch(error => {
        // Handle error (for example, show an error message)
    alert('There was an error updating the status: ' + error.message);
    });
});

            // Initial fetch when the page loads
            fetchPenitip();
            fetchPegawai();
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>