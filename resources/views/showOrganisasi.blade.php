<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reusemart</title>


	<!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

	<script>
		document.addEventListener("DOMContentLoaded", function() {
            const token = localStorage.getItem("token");
            if (!token) {
                window.location.href = "{{ url('/pegawai/login') }}";
            }
        });
	</script>

    <style>
        body{
            background:rgb(243, 243, 243);    
        }
        .main-box.no-header {
            padding-top: 20px;
        }
        .table thead tr th {
            text-transform: uppercase;
            font-size: 0.875em;
            /* text-align: center; */
        }
        .btn-edit {
            background-color:rgb(216, 216, 216);
            color: #333;
        }
        .btn-edit:hover {
            background-color:rgb(173, 173, 173); 
        }
        .btn-delete {
            background-color:rgb(221, 58, 58);
            color: #333;
        }
        .btn-delete:hover {
            background-color:rgb(166, 42, 42);
        }
        .table td{
            font-size: 0.85rem;
        }
        
    </style>
</head>

<body>
    <!-- ////////////////////INI MODAL DELETE///////////////////////////// -->
    <div class="modal fade" id="deleteOrganisasi" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="exampleModalLabel">Hapus Organisasi</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data organisasi ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ////////////////////INI MODAL UPDATE///////////////////////////// -->
    <div class="modal fade" id="updateOrganisasi" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="exampleModalLabel"><strong>Update Organisasi</strong></h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="namaOrganisasi" class="form-label"><strong>Nama Organisasi</strong></label>
                        <input type="text" class="form-control" id="namaOrganisasi" name="namaOrganisasi" required>
                    </div>
                    <div class="mb-3">
                        <label for="usernameOrganisasi" class="form-label"><strong>Username Organisasi</strong></label>
                        <input type="text" class="form-control" id="usernameOrganisasi" name="usernameOrganisasi" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamatOrganisasi" class="form-label"><strong>Alamat Organisasi</strong></label>
                        <input type="text" class="form-control" id="alamatOrganisasi" name="alamatOrganisasi" required >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmUpdate">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ////////////////////INI ISI///////////////////////////// -->
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="mb-3">Organisasi Management</h2>
            <input class="form-control me-2" name="name" type="text" placeholder="Search" aria-label="Search" id="inputSearch" style="width: 300px;" >
        </div>
        <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box ">
                    <div class="main-box-body">
                        <div class="table-responsive">
                            <table class="table organisasi-list">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Organisasi</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Alamat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="listOrganisasi">
                                    <!-- Load data -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- //////////////////////////INI SCRIPT SHOW ALL DAN SEARCH/////////////////////////// -->
<script>
    document.addEventListener("DOMContentLoaded", function(){
        const organisasiList = document.getElementById("listOrganisasi");
        const inputSearch = document.getElementById("inputSearch");
        let OrganisasiData = [];
        let idToDelete = null;
        let idToUpdate = null;
        let idToDefault = null;
        
        fetchOrganisasi();

        ////////////////////////SHOW ALAMAT///////////////////////////////////
        function fetchOrganisasi(){
            fetch("http://127.0.0.1:8000/api/organisasi", {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${localStorage.getItem("token")}`,
                    "Content-Type": "application/json",
                },
            })
            .then(response => response.json())
            .then(data => {
                OrganisasiData = data.data;
                renderOrganisasi(data.data);
            })
            .catch(error => console.error("Error fetching organisasi:", error));
        }

        ////////////////////////Organisasi///////////////////////////////////
        function renderOrganisasi(data){
            // console.log("Rendering data:", data);
            organisasiList.innerHTML = ""; 

            data.forEach(organisasi => {
                const row = `
                    <tr>
                        <td>${organisasi.idOrganisasi}</td>
                        <td>${organisasi.namaOrganisasi}</td>
                        <td>${organisasi.username}</td>
                        <td>${organisasi.email}</td>
                        <td>${organisasi.alamat}</td>
                        <td >
                            <button type="button" class="btn btn-edit" data-id="${organisasi.idOrganisasi}" 
                                data-nama="${organisasi.namaOrganisasi}" data-username="${organisasi.username}" data-alamat="${organisasi.alamat}"
                                data-bs-toggle="modal" data-bs-target="#updateOrganisasi">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <button type="button" class="btn btn-delete" data-id="${organisasi.idOrganisasi}" 
                                data-bs-toggle="modal" data-bs-target="#deleteOrganisasi">
                                <i class="fas fa-trash" style="color: white"></i>
                            </button>
                        </td>
                    </tr>
                `;
                organisasiList.innerHTML += row;
            });

            document.querySelectorAll(".btn-delete").forEach(button => {
                button.addEventListener("click", () => {
                    idToDelete = button.getAttribute("data-id");
                });
            });

            document.querySelectorAll(".btn-edit").forEach(button => {
                button.addEventListener("click", () => {
                    idToUpdate = button.getAttribute("data-id");
                    const nama = button.getAttribute("data-nama");
                    const username = button.getAttribute("data-username");
                    const alamat = button.getAttribute("data-alamat");
        
                    document.getElementById("namaOrganisasi").value = nama;
                    document.getElementById("usernameOrganisasi").value = username;
                    document.getElementById("alamatOrganisasi").value = alamat;
                });
            });

            document.querySelectorAll(".btn-outline-success").forEach(button => {
                button.addEventListener("click", function () {
                    idToDefault = this.getAttribute("data-id");
                });
            });
        }

        inputSearch.addEventListener("input", () => {
            const query = inputSearch.value.toLowerCase();
            fetch(`http://127.0.0.1:8000/api/organisasi/search?q=${query}`, {
                headers: { 
                    "Authorization": `Bearer ${localStorage.getItem('token')}` },
            })
                .then(response => response.json())
                .then(data => renderOrganisasi(data.data))
                .catch(error => console.error("Error searching organisasi:", error));
        });


        //////////////////////UPDATE ORGANISASI///////////////////////////////////
        document.getElementById("confirmUpdate").addEventListener('click', function(event) {
            if (!idToUpdate) return;

                const namaOrganisasi = document.getElementById("namaOrganisasi").value;
                const username = document.getElementById("usernameOrganisasi").value;
                const alamat = document.getElementById("alamatOrganisasi").value;

                fetch(`http://127.0.0.1:8000/api/organisasi/update/${idToUpdate}`, {
                    method: 'PUT',
                    headers: {
                        "Authorization": `Bearer ${localStorage.getItem('token')}`,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        username,
                        namaOrganisasi,
                        alamat,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('updateOrganisasi'));
                    if (modal) modal.hide();

                    Toastify({
                        text: "Berhasil Mengubah Organisasi",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#8bc34a",
                    }).showToast();
                    fetchOrganisasi();
                    idToUpdate = null;
                })
                .catch(error => {
                    console.error("Error:", error);
                    Toastify({
                        text: "Gagal Mengubah Organisasi",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "rgb(221, 25, 25)",
                    }).showToast();
                });
        });
        

        ////////////////////////DELETE Organisasi///////////////////////////////////
        document.getElementById("confirmDelete").addEventListener('click', function(event) {
            if (!idToDelete) return;

                fetch(`http://127.0.0.1:8000/api/organisasi/delete/${idToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        "Authorization": `Bearer ${localStorage.getItem('token')}`,
                        "Content-Type": "application/json"
                    },
                })
                .then(response => response.json())
                .then(data => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteOrganisasi'));
                    if (modal) modal.hide();

                    Toastify({
                        text: "Berhasil Menghapus Organisasi",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#8bc34a",
                    }).showToast();
                    fetchOrganisasi();
                    idToDelete = null;
                })
                .catch(error => {
                    console.error("Error:", error);
                    Toastify({
                        text: "Gagal Menghapus Organisasi",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "rgb(221, 25, 25)",
                    }).showToast();
                });
            });
    });
</script>

</body>
</html>
