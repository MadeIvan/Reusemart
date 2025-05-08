<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reusemart</title>

	<script>
		const token = localStorage.getItem("token");
			if (!token) {
				window.location.href = "{{ url('/pembeli/login') }}";
			}
	</script>

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
            margin-bottom: 0px;  /* Mengurangi jarak bawah pada input search */
        }

        .container {
            margin-top: 0 !important;  
            /* padding: 0 !important;  */
        }

        .card {
            margin-bottom: 10px;  /* Mengurangi jarak antar card */
        }

        .d-flex {
            gap: 10px;  /* Menyusun input dan button secara lebih rapat */
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
    <div class="container py-4">
        <h2 class="mb-4">Daftar Alamat</h2>
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <input class="form-control me-2 flex-grow-1" name="name" type="text" placeholder="Search" aria-label="Search" id="inputSearch" style="width: 200px;" >
            <button class="btn btn-success" type="submit" id="searchButton">
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
            const alamatContainer = document.getElementById("alamatContainer");
            const searchInput = document.getElementById("inputSearch");
            let alamatData = [];

            fetchAlamat();

            function fetchAlamat(){
                fetch("http://127.0.0.1:8000/api/pembeli/alamat", {
                    method: "GET",
                    headers: {
                        "Authorization": `Bearer ${localStorage.getItem("token")}`,
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

            function renderAlamat(data){
                alamatContainer.innerHTML = "";
                data.forEach(alamat => {
                    const isDefaultLabel = alamat.isDefault == 1 
                        ? `<h6 class="card-subtitle mb-0 text-success" style="font-size: 0.9rem;">Utama</h6>` 
                        : '';
                    
                    const isDisabled = alamat.isDefault == 1 
                        ? 'disabled' 
                        : '';

                    const card = `
                    <div class="col-12 ">
                        <div class="card">
                            <div class="card-body position-relative">
                                <div class="position-absolute top-0 end-0 mt-2 me-3">
                                    <div class="d-flex justify-content-end mb-2"" >
                                    <a href="#" class="card-link me-2 text-decoration-none">Ubah</a>
                                    <a href="#" class="card-link text-danger text-decoration-none" data-id="${alamat.idAlamat}"
                                        data-bs-toggle="modal" data-bs-target="#deleteAlamat">Hapus</a>
                                    </div>
                                    <button type="button" class="btn btn-outline-success btn-sm" 
                                        id="confirmDefault" ${isDisabled} data-id="${alamat.idAlamat}">
                                        Atur Sebagai Utama
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
            }

            alamatContainer.addEventListener('click', function(event) {
                const target = event.target;
                
                if (target.matches('.card-link.text-danger[data-id]')) {
                    event.preventDefault();
                    const idAlamat = target.getAttribute('data-id');

                    fetch(`http://127.0.0.1:8000/api/pembeli/alamat/delete/${idAlamat}`, {
                        method: 'DELETE',
                        headers: {
                            "Authorization": `Bearer ${localStorage.getItem('token')}`,
                            "Content-Type": "application/json"
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
                        fetchAlamat(); // Reload data tanpa reload halaman
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
                }
            });


            // document.querySelectorAll(".card-link[data-id]").addEventListener("click", () => {
            //     const deleteAlamat = document.getElementById("nisn").value;

            //     fetch(`http://127.0.0.1:8000/api/beasiswa/delete/${nisn}`, {
            //         method: "DELETE",
            //         headers: { "Authorization": `Bearer ${localStorage.getItem('authToken')}` },
            //     })
            //         .then(response => response.json())
            //         .then(data => {
            //             alert(data.message);
            //             fetchBeasiswa();
            //         })
            //         .catch(error => console.error("Error deleting beasiswa:", error));
            // });
            
            //     const deleteAlamat = document.querySelectorAll(".card-link[data-id]");
            //     deleteAlamat.forEach(link => {
            //         link.addEventListener("click", function(event) {
            //             event.preventDefault();
            //             const id = this.getAttribute("data-id");
            //             handleDelete(id);
            //             const id = button.getAttribute("data-id");
            //             handleDelete(id);
            //         });
            //     });

                // document.querySelectorAll(".btn-edit").forEach(button => {
                // button.addEventListener("click", () => {
                //     const id = button.getAttribute("data-id");
                //     const nama = button.getAttribute("data-nama");
                //     const username = button.getAttribute("data-username");
                //     const alamat = button.getAttribute("data-alamat");

                //     document.getElementById("namaOrganisasi").value = nama;
                //     document.getElementById("usernameOrganisasi").value = username;
                //     document.getElementById("alamatOrganisasi").value = alamat;

                //     handleUpdate(id);
                // });
            // });
            // const searchInput = document.getElementById("inputSearch");
            // searchInput.addEventListener("input", () => {
            //     const query = searchInput.value.toLowerCase();
            //     fetch(`http://127.0.0.1:8000/api/organisasi/search?q=${query}`, {
            //         headers: { 
            //             "Authorization": `Bearer ${localStorage.getItem('token')}` },
            //     })
            //         .then(response => response.json())
            //         .then(data => renderTable(data.data))
            //         .catch(error => console.error("Error searching organisasi:", error));
            // });
            // })
        // })
        });
    </script>

</body>
</html>
