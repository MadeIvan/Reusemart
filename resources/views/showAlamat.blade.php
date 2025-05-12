<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reusemart</title>

    <!-- <script>
// Cek token saat halaman dimuat
document.addEventListener("DOMContentLoaded", function() {
    if (!localStorage.getItem("token")) {
        // Jika token tidak ada, redirect ke halaman login
        window.location.href = "{{ url('/pembeli/login') }}";
    }
});
</script> -->

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
    <!-- Toastify ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">



    <style>
        body{
            background:rgb(243, 243, 243);    
        }

        #inputSearch {
            margin-bottom: 0px;
        }

        .container {
            margin-top: 0 !important;  
        }

        .card {
            margin-bottom: 10px;
        }

        .d-flex {
            gap: 10px;
        }

    </style>
</head>

<body>




    <!-- ////////////////////INI MODAL DELETE///////////////////////////// -->
    <div class="modal fade" id="deleteAlamat" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="exampleModalLabel">Hapus Alamat</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus alamat ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ////////////////////INI MODAL UPDATE///////////////////////////// -->
    <div class="modal fade" id="updateAlamat" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="exampleModalLabel"><strong>Update Alamat</strong></h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kategoriAlamat" class="form-label"><strong>Kategori</strong></label>
                        <input type="text" class="form-control" id="kategoriAlamat" name="kategoriAlamat" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label"><strong> Alamat</strong></label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmUpdate">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ////////////////////INI MODAL CREATE///////////////////////////// -->
    <div class="modal fade" id="createAlamat" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="exampleModalLabel"><strong>Buat Alamat</strong></h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kategoriAlamatCreate" class="form-label"><strong>Kategori</strong></label>
                        <input type="text" class="form-control" id="kategoriAlamatCreate" name="kategoriAlamatCreate" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamatCreate" class="form-label"><strong> Alamat</strong></label>
                        <textarea class="form-control" id="alamatCreate" name="alamatCreate" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <input class="form-check-input" type="checkbox" id="isDefault" aria-label="Checkbox for Atur Sebagai Alamat Utama">
                        <label class="form-check-label" for="checkboxUtama">
                            Atur Sebagai Alamat Utama
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmCreate">Tambah</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ////////////////////INI ISI///////////////////////////// -->
    <div class="container py-4">
        <h2 class="mb-4">Daftar Alamat</h2>
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <input class="form-control me-2 flex-grow-1" name="name" id="searchInput" type="text" placeholder="Search" aria-label="Search"style="width: 200px;" >
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createAlamat" type="submit" id="tambahButton">
                <i class="bi bi-plus-square-fill me-2"></i>Tambah Alamat
            </button>
        </div>
    </div>

    <div class="container mt-2">
        <div class="row flex-column" id="alamatContainer">
            <!-- NANTI DISINI -->
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function(){
            const auth_token = localStorage.getItem('auth_token');
            if (!localStorage.getItem("auth_token")) {
            window.location.href = "/UsersLogin";
            }
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag
            const alamatContainer = document.getElementById("alamatContainer");
            const searchInput = document.getElementById("searchInput");
            let alamatData = [];
            let idToDelete = null;
            let idToUpdate = null;
            let idToDefault = null;
            
          
            fetchAlamat();

            ////////////////////////SHOW ALAMAT//////////////////////////////////;

            function fetchAlamat(){
                const token = localStorage.getItem('auth_token');
                if (!token) {
                    console.error("No auth token found. Please log in first.");
                    return;
                }
                fetch(`http://127.0.0.1:8000/api/pembeli/alamat`, {
                    method: "GET",
                    headers: {
                        "Authorization": `Bearer ${token}`,
                        'Accept': 'application/json',
                        "Content-Type": "application/json",
                    },
                })
                .then(response => response.json())
                .then(data => {
                    alamatData = data.data;
                    renderAlamat(data.data);
                })
                .catch(error => console.error("Error fetching alamat:", error));
            }

            ////////////////////////CARD ALAMAT///////////////////////////////////
            function renderAlamat(data){
                // console.log("Rendering data:", data);
                alamatContainer.innerHTML = "";
                data.forEach(alamat => {
                    const isDefaultLabel = alamat.isDefault == true 
                        ? `<h6 class="card-subtitle mb-0 text-success" style="font-size: 0.9rem;">Utama</h6>` 
                        : '';
                    
                    const isDisabled = alamat.isDefault == true 
                        ? 'disabled' 
                        : '';

                    const card = `
                    <div class="col-12 ">
                        <div class="card">
                            <div class="card-body position-relative">
                                <div class="position-absolute top-0 end-0 mt-2 me-3">
                                    <div class="d-flex justify-content-end mb-2"" >
                                    <a href="#" class="card-link me-2 text-decoration-none update-link" data-id="${alamat.idAlamat}"
                                        data-alamat="${alamat.alamat}" data-kategoriAlamat="${alamat.kategori}"
                                        data-bs-toggle="modal" data-bs-target="#updateAlamat">Ubah</a>
                                    <a href="#" class="card-link text-danger text-decoration-none delete-link" data-id="${alamat.idAlamat}"
                                        data-bs-toggle="modal" data-bs-target="#deleteAlamat">Hapus</a>
                                    </div>
                                    <button type="button" class="btn btn-outline-success btn-sm" 
                                        id="confirmDefault" ${isDisabled} data-id="${alamat.idAlamat}">
                                        Atur Sebagai Alamat Utama
                                    </button>
                                </div>
                                
                                <!-- Alamat content -->
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="card-title mb-2">${alamat.kategori}</h5>
                                    ${isDefaultLabel}
                                </div>
                                <p class="card-subtitle text-body-secondary mt-2">${alamat.alamat}</p>
                            </div>
                        </div>
                    </div>
                    `;
                    alamatContainer.innerHTML += card;
                });

                document.querySelectorAll(".delete-link").forEach(link => {
                    link.addEventListener("click", function () {
                        idToDelete = this.getAttribute("data-id");
                    });
                });

                document.querySelectorAll(".update-link").forEach(link => {
                    link.addEventListener("click", function () {
                        idToUpdate = this.getAttribute("data-id");
                        const kategori = this.getAttribute("data-kategoriAlamat");
                        const alamat = this.getAttribute("data-alamat");

                        document.getElementById("kategoriAlamat").value = kategori;
                        document.getElementById("alamat").value = alamat;
                    });
                });

                document.querySelectorAll(".btn-outline-success").forEach(button => {
                    button.addEventListener("click", function () {
                        idToDefault = this.getAttribute("data-id");
                    });
                });

            }
            
            searchInput.addEventListener("input", () => {
                const query = searchInput.value.toLowerCase();
                fetch(`http://127.0.0.1:8000/api/pembeli/alamat/search?q=${query}`, {
                    headers: { 
                        "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                        'Accept': 'application/json',
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    }
                })
                    .then(response => response.json())
                    .then(data => renderAlamat(data.data))
                    .catch(error => console.error("Error searching alamat:", error));
            });

            //////////////////////UPDATE ALAMAT///////////////////////////////////
            document.getElementById("confirmUpdate").addEventListener('click', function(event) {
                if (!idToUpdate) return;

                    const kategori = document.getElementById("kategoriAlamat").value;
                    const alamat = document.getElementById("alamat").value;

                    fetch(`http://127.0.0.1:8000/api/pembeli/alamat/update/${idToUpdate}`, {
                        method: 'PUT',
                        headers: {
                            "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                            'Accept': 'application/json',
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            },
                        body: JSON.stringify({
                            alamat,
                            kategori,
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('updateAlamat'));
                        if (modal) modal.hide();

                        Toastify({
                            text: "Berhasil Mengubah Alamat",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#8bc34a",
                        }).showToast();
                        fetchAlamat();
                        idToUpdate = null;
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Toastify({
                            text: "Gagal Mengubah Alamat",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "rgb(221, 25, 25)",
                        }).showToast();
                    });
            });

            ////////////////////////DELETE ALAMAT///////////////////////////////////
            document.getElementById("confirmDelete").addEventListener('click', function(event) {
                if (!idToDelete) return;

                    fetch(`http://127.0.0.1:8000/api/pembeli/alamat/delete/${idToDelete}`, {
                        method: 'DELETE',
                        headers: {
                            "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                            'Accept': 'application/json',
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteAlamat'));
                        if (modal) modal.hide();

                        Toastify({
                            text: "Berhasil Menghapus Alamat",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#8bc34a",
                        }).showToast();
                        fetchAlamat();
                        idToDelete = null;
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Toastify({
                            text: "Gagal Menghapus Alamat",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "rgb(221, 25, 25)",
                        }).showToast();
                    });
            });


            ////////////////////////TAMBAH ALAMAT///////////////////////////////////
            document.getElementById("confirmCreate").addEventListener("click", () => {
                const kategori = document.getElementById("kategoriAlamatCreate").value;
                const alamat = document.getElementById("alamatCreate").value;
                const isDefault = document.getElementById("isDefault").checked;


                fetch("http://127.0.0.1:8000/api/pembeli/buat-alamat", {
                    method: "POST",
                    headers: {
                        "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                        'Accept': 'application/json',
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        kategori,
                        alamat,
                        isDefault
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Data berhasil ditambahkan:", data);
                    const modal = bootstrap.Modal.getInstance(document.getElementById("createAlamat"));
                    if (modal) modal.hide();
    
                    Toastify({
                        text: "Berhasil Menambahkan Alamat",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#8bc34a"
                        },
                    }).showToast();
    
    
                    fetchAlamat();
                })
                .catch(error => {
                    console.error("Error:", error);
                    Toastify({
                        text: "Gagal Menambahkan Alamat",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "rgb(221, 25, 25)"
                        },
                        // backgroundColor: "rgb(221, 25, 25)",
                    }).showToast();
                });
            })

            ////////////////////////SET DEFAULT///////////////////////////////////
            document.addEventListener('click', function(event) {
                if(event.target && event.target.id === 'confirmDefault' && idToDefault){
                    fetch(`http://127.0.0.1:8000/api/pembeli/alamat/set-default/${idToDefault}`, {
                        method: 'PUT',
                        headers: {
                            "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                            'Accept': 'application/json',
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        Toastify({
                            text: "Berhasil Mengubah Menjadi Alamat Utama",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#8bc34a",
                        }).showToast();
    
                        fetchAlamat();
                        idToDefault = null;
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Toastify({
                            text: "Gagal Mengubah Menjadi Alamat Utama",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "rgb(221, 25, 25)",
                        }).showToast();
                    });
                }
            });
        });
    </script>

</body>
</html>
