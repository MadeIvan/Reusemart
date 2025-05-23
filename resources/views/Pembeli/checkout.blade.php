<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reusemart</title>

    <script>
        // Cek token saat halaman dimuat
        document.addEventListener("DOMContentLoaded", function() {
            if (!localStorage.getItem("auth_token")) {
                // Jika token tidak ada, redirect ke halaman login
                window.location.href = "{{ url('/UsersLogin') }}";
            }
        });
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

</head>

<body>
    <!-- ////////////////////MODAL ALAMAT///////////////////// -->
    <div class="modal fade" id="modalAlamat" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="exampleModalLabel">Alamat Saya</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="daftarAlamatModal">
                    <!-- nanti muncul disini -->
                </div>
            </div>
        </div>
    </div>





    @include('layouts.navbar')

    <div class="container mt-5 mb-5">
        <h3 class="card-title mb-3">Checkout</h3>

            <!--Alamat Pengiriman -->
            
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Alamat Pengiriman</h5>
                <div id="alamatPembeli">
                    <!-- Daftar ALamat -->
                </div>
            </div>
        </div>

        <!-- Produk yang dipesan -->
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Produk yang dipesan</h5>
                <div id="product-keranjang">
                    <!-- Daftar produk -->
                </div>
            </div>
        </div>

        <!-- Detail (dipindah ke bawah produk) -->
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Detail</h5>
                <div id="product-detail">
                    <!-- Detail -->
                </div>
            </div>
            <div class="mx-3 mb-2 text-end">
                <button class="btn btn-success" id="checkout-btn">Buat Pesanan</button>
            </div>
        </div>
    </div>


    <footer class="bg-dark text-white text-center p-3">
        <p>&copy; 2025 Reusemart</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function(){
            const auth_token = localStorage.getItem('auth_token');
            if (!localStorage.getItem("auth_token")) {
                window.location.href = "/UsersLogin";
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag
            const alamatContainer = document.getElementById("alamatPembeli");
            let alamatData = [];
            let alamatList = [];
            let idBarang = null;
            
          
            fetchAlamat();
            fetchBarang(); 
            tampilkanDetailCheckout();
            // showDetail(barangData);

            ////////////////////////SHOW ALAMAT//////////////////////////////////;

            function fetchAlamat(){
                const token = localStorage.getItem('auth_token');
                if (!token) {
                    console.error("No auth token found. Please log in first.");
                    return;
                }
                fetch(`http://127.0.0.1:8000/api/alamatUtama`, {
                    method: "GET",
                    headers: {
                        "Authorization": `Bearer ${token}`,
                        'Accept': 'application/json',
                        "Content-Type": "application/json",

                    },
                })
                .then(response => response.json())
                .then(data => {
                    // alamatData = data.alamatUtama;
                    // renderAlamat(data.alamatUtama);
                    if(data.alamatUtama){
                        tampilkanAlamatPengiriman(data.alamatUtama);
                    }
                })
                .catch(error => console.error("Error fetching alamat:", error));
            }

            
            // function renderAlamat(data) {
            //     const isDefaultLabel = data.isDefault == true 
            //         ? `<span class="text-success" style="font-size: 0.9rem;">Utama</span>` 
            //         : '';

            //     alamatContainer.innerHTML = `
            //         <div class="row align-items-center">
            //             <div class="col-md-9">
            //                 <h6 class="card-subtitle mt-2 text-black d-flex align-items-center gap-2">
            //                     <strong>${data.nama}</strong>
            //                     ${isDefaultLabel}    
            //                 </h6>
            //                 <p class="card-text">${data.alamat}</p>
            //             </div>
            //             <div class="col-md-3 d-flex justify-content-center align-items-center" style="height: 100%;">
            //                 <a class="text-success" href="#" data-bs-toggle="modal" data-bs-target="#modalAlamat" id="ubahAlamat">Ubah Alamat</a>
            //             </div> 
            //         </div>
            //     `;
            // }

            document.addEventListener("click", function(e) {
                if (e.target && e.target.id === "ubahAlamat") {
                    tampilkanModalAlamat();
                }
            });

            function tampilkanModalAlamat() {
                const token = localStorage.getItem('auth_token');
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
                    const daftarAlamatModal = document.getElementById("daftarAlamatModal");
                    daftarAlamatModal.innerHTML = ""; // Kosongkan isi sebelumnya

                    if (data.data && data.data.length > 0) {
                        alamatList = data.data;
                        let radioList = '<form id="form-alamat">';
                        data.data.forEach((item) => {
                            const isDefault = item.isDefault == 1 ? 'checked' : '';
                            radioList += `
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="alamatRadio" id="alamat-${item.idAlamat}" value="${item.idAlamat}" ${isDefault}>
                                    <label class="form-check-label" for="alamat-${item.idAlamat}">
                                        <strong>${item.nama}</strong> ${item.isDefault == 1 ? '<span class="badge bg-success">Utama</span>' : ''}
                                        <br>
                                        <small>${item.alamat}</small>
                                    </label>
                                </div>
                                <hr>
                            `;
                        });
                        radioList += `
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-success" id="btn-pilih-alamat">Pilih Alamat</button>
                        </form>`;

                        daftarAlamatModal.innerHTML = radioList;

                        // Tambahkan event listener untuk tombol pilih alamat
                        document.getElementById('btn-pilih-alamat').addEventListener('click', function() {
                            const selectedRadio = document.querySelector('input[name="alamatRadio"]:checked');
                            if (selectedRadio) {
                                const selectedId = selectedRadio.value;
                                // Panggil fungsi untuk simpan/refresh alamat utama berdasarkan id yg dipilih
                                pilihAlamat(selectedId);
                                // Tutup modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById('modalAlamat'));
                                modal.hide();
                            } else {
                                alert('Silakan pilih alamat terlebih dahulu');
                            }
                        });

                    } else {
                        daftarAlamatModal.innerHTML = `<p>Tidak ada alamat yang tersedia.</p>`;
                    }
                })
                .catch(error => console.error("Gagal mengambil daftar alamat:", error));
            }

            function pilihAlamat(selectedId) {
                // Cari alamat lengkap berdasarkan id yang dipilih
                const alamatTerpilih = alamatList.find(a => a.idAlamat == selectedId);
                if (alamatTerpilih) {
                    // Tampilkan alamat pengiriman di halaman utama checkout
                    tampilkanAlamatPengiriman(alamatTerpilih);
                }
            }

           function tampilkanAlamatPengiriman(alamat) {
                const alamatContainer = document.getElementById('alamatPembeli');
                const isDefaultLabel = alamat.isDefault == 1 ? `<span class="text-success" style="font-size: 0.9rem;">Utama</span>` : '';
                alamatContainer.innerHTML = `
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <h6 class="card-subtitle mt-2 text-black d-flex align-items-center gap-2">
                                <strong>${alamat.nama}</strong>
                                ${isDefaultLabel}
                            </h6>
                            <p class="card-text">${alamat.alamat}</p>
                        </div>
                        <div class="col-md-3 d-flex justify-content-center align-items-center" style="height: 100%;">
                            <a class="text-success" href="#" data-bs-toggle="modal" data-bs-target="#modalAlamat" id="ubahAlamat">Ubah Alamat</a>
                        </div> 
                    </div>
                `;

                // Simpan alamat pengiriman yang dipilih untuk proses checkout nanti
                localStorage.setItem('alamatPengiriman', JSON.stringify(alamat));
            }


            ////////////////////////ini barangggggg///////////////////////
            function fetchBarang(){
                const token = localStorage.getItem('auth_token');
                if (!token) {
                    console.error("No auth token found. Please log in first.");
                    return;
                }
                fetch(`http://127.0.0.1:8000/api/keranjang`, {
                    method: "GET",
                    headers: {
                        "Authorization": `Bearer ${token}`,
                        'Accept': 'application/json',
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                })
                .then(response => response.json())
                .then(response => {
                    if (!response.status) {
                        console.error("Failed fetching cart:", response.message);
                        return;
                    }
                    // simpan data
                    barangData = response.data;
                    // render ke halaman
                    renderCart(barangData);
                    // showDetail(barangData);
                })
                .catch(error => console.error("Error fetching alamat:", error));
            }

            function renderCart(items) {
                const container = document.querySelector('#product-keranjang');
                container.innerHTML = ''; // Reset isi

                if (items.length === 0) {
                    container.innerHTML = `<div class="text-center text-muted">Keranjang kosong</div>`;
                    return;
                }

                items.forEach(item => {
                    const formatHarga = parseInt(item.hargaBarang).toLocaleString('id-ID');

                    const itemHtml = `
                        <div class="card mb-4 shadow-sm rounded-4 p-3">
                            <div class="d-flex align-items-start">
                                <img src="${item.image}" alt="${item.namaBarang}" class="rounded-3 me-3" style="width: 150px; ">

                                <div class="flex-grow-1">
                                    <h6 class="mb-1">${item.namaBarang}</h6>
                                    <p class="text-secondary small mt-1">Berat barang: ${item.beratBarang} kg</p>
                                    <p class="mb-0 fw-bold text-success fs-4 text-end">Rp ${formatHarga}</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="text-end">
                                    
                                </div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += itemHtml;
                });
            }


            ////////////////////detail//////////
            function tampilkanDetailCheckout() {
            const data = JSON.parse(localStorage.getItem("data_checkout"));
            if (!data) {
                document.getElementById("product-detail").innerHTML = "<p>Data checkout tidak tersedia.</p>";
                return;
            }

            const detailContainer = document.getElementById("product-detail");
            let detailHtml = `
                <div class="card p-3 shadow-sm rounded-4">
                    <ul class="list-group list-group-flush">
            `;

            data.barang.forEach(item => {
                detailHtml += `
                    <li class="list-group-item d-flex justify-content-between">
                        <span>${item.namaBarang}</span>
                        <span>Rp ${parseInt(item.hargaBarang).toLocaleString('id-ID')}</span>
                    </li>
                `;
            });
            let totalHargaFormatted = data.total_harga.toString().replace(/\./g, '');
    totalHargaFormatted = parseInt(totalHargaFormatted).toLocaleString('id-ID');

            detailHtml += `
                <li class="list-group-item d-flex justify-content-between">
                    <span><strong>Metode Pengiriman</strong></span>
                    <span>${data.metode_pengiriman}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span><strong>Total</strong></span>
                    <span>Rp ${totalHargaFormatted}</span>
                </li>
                </ul>
            </div>
            `;

            detailContainer.innerHTML = detailHtml;
        }

        document.addEventListener("DOMContentLoaded", tampilkanDetailCheckout);



            
        });
    </script>
</body>
</html>
