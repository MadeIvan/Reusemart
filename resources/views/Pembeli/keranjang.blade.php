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
    <!-- ////////////////////INI MODAL DELETE///////////////////////////// -->
    <div class="modal fade" id="deleteKeranjang" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus produk ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.navbar')
    
    <!-- ////////////////////INI ISI///////////////////////////// -->
    <!-- <div class="container py-4">
        <h2 class="mb-4">Keranjang Anda</h2>
        
        <div class="container my-5" id="product-keranjang"> -->
            <!-- Product details will be dynamically loaded here -->
        <!-- </div>
    </div> -->
    <div class="container my-5" style = "margin-top: 5% !important; margin-left: 5% !important;" >
        <h2>Keranjang Belanja</h2>
        <div class="row">
            <!-- ///////////////kolom produk/////////////////// -->
            <div class="col-lg-8" id="product-keranjang">
                <!-- produk -->
            </div>

            <!-- //////////////////kolom detail//////////// -->
            <div class="col-lg-4">
                <!-- metode pengiriman -->
                <div class="card p-3 shadow-sm rounded-4 mb-3">
                    <h6 class="mb-2">Metode Pengiriman</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radioDefault" id="radioDefault1">
                        <label class="form-check-label" for="radioDefault1">
                            Kurir
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="radioDefault" id="radioDefault2" checked>
                        <label class="form-check-label" for="radioDefault2">
                            Ambil Sendiri
                        </label>
                    </div>
                </div>

                <!-- detail pembelian -->
                <div id="detail-pembelian"></div>
            </div>
        </div>
    </div>
    
    
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // sessionStorage.removeItem('pesananSelesai');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const token = localStorage.getItem('auth_token');
            let idBarang = null;
            
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
                    showDetail(barangData);
                })
                .catch(error => console.error("Error fetching alamat:", error));

                document.getElementById('radioDefault1').addEventListener('change', () => showDetail(barangData));
                document.getElementById('radioDefault2').addEventListener('change', () => showDetail(barangData));

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
                                <img src="${`http://127.0.0.1:8000/${item.image}`}" alt="${item.namaBarang}" class="rounded-3 me-3" style="width: 150px; ">

                                <div class="flex-grow-1">
                                    <h3 class="mb-1">${item.namaBarang}</h3>
                                    <p class="text-secondary small mt-1">Berat barang: ${item.beratBarang} kg</p>

                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div>
                                    <button class="btn btn-link text-danger text-decoration-none p-0 remove-btn" 
                                    data-id="${item.idBarang}" data-bs-toggle="modal" data-bs-target="#deleteKeranjang">
                                        Remove
                                    </button>
                                </div>
                                <div class="text-end">
                                    <p class="mb-0 fw-bold text-success fs-4">Rp ${formatHarga}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += itemHtml;
                });

                document.querySelectorAll('.remove-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        idBarang = this.getAttribute('data-id');
                        // removeFromCart(idBarang);
                    });
                });

            }

            document.getElementById("confirmDelete").addEventListener('click', function(event) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const token = localStorage.getItem('auth_token');

                if (!idBarang) return;
                    fetch(`http://127.0.0.1:8000/api/hapus-keranjang/${idBarang}`, {
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
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteKeranjang'));
                        if (modal) modal.hide();
                        alert("Berhasil dihapus dari keranjang")
                        // Toastify({
                        //     text: "Berhasil Menghapus Alamat",
                        //     duration: 3000,
                        //     close: true,
                        //     gravity: "top",
                        //     position: "right",
                        //     backgroundColor: "#8bc34a",
                        // }).showToast();
                        fetchBarang();
                        idBarang = null;
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Gagal Menghapus Alamat")
                        // Toastify({
                        //     text: "Gagal Menghapus Alamat",
                        //     duration: 3000,
                        //     gravity: "top",
                        //     position: "right",
                        //     backgroundColor: "rgb(221, 25, 25)",
                        // }).showToast();
                    });
            });

            // function removeFromCart(idBarang) {
            //     const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            //     const token = localStorage.getItem('auth_token');

            //     fetch(`http://127.0.0.1:8000/api/hapus-keranjang/${idBarang}`, {
            //         method: "DELETE",
            //         headers: {
            //             "Authorization": `Bearer ${token}`,
            //             'Accept': 'application/json',
            //             "Content-Type": "application/json",
            //             "X-CSRF-TOKEN": csrfToken,
            //         },
            //     })
            //     .then(res => res.json())
            //     .then(res => {
            //         if (res.status) {
            //             alert("Berhasil dihapus dari keranjang")
            //             // Toastify({
            //             //     text: "",
            //             //     duration: 3000,
            //             //     gravity: "top",
            //             //     position: "right",
            //             //     backgroundColor: "green",
            //             // }).showToast();
            //             fetchBarang(); 
            //         } else {
            //             alert(res.message);
            //         }
            //     })
            //     .catch(error => {
            //         console.error("Gagal hapus:", error);
            //     });
            // }

            function getMetodePengiriman(){
                const kurirChecked = document.getElementById('radioDefault1').checked;
                return kurirChecked ? 'Kurir' : 'Ambil Sendiri';
            }

            function showDetail(items) {
                const detailContainer = document.getElementById('detail-pembelian');
                let totalHarga = 0;
                let ongkir = 0;

                
                let detailHtml = `
                    <div class="card p-3 shadow-sm rounded-4">
                        <h5>Detail Pembelian</h5>
                        <ul class="list-group list-group-flush">
                `;
                
                items.forEach(item => {
                    const harga = parseInt(item.hargaBarang);
                    totalHarga += harga;
                    
                    detailHtml += `
                    <li class="list-group-item d-flex justify-content-between">
                        <span>${item.namaBarang}</span>
                        <span>Rp ${harga.toLocaleString('id-ID')}</span>
                    </li>
                    `;
                });

                detailHtml += `
                    <li class="list-group-item d-flex justify-content-between">
                        <span><strong>Total Barang:</strong></span>
                        <span>Rp ${totalHarga.toLocaleString('id-ID')}</span>
                    </li>
                `;
                
                const metode = getMetodePengiriman();
                if (metode === 'Kurir' && totalHarga > 1500000) {
                    ongkir = 0;
                    localStorage.setItem('metodeAnterAmbil', "Kurir");
                }else if(metode === 'Kurir' && totalHarga <= 1500000){
                    ongkir = 100000;
                    localStorage.setItem('metodeAnterAmbil', "Kurir");
                }else{
                    ongkir = 0;
                    localStorage.setItem('metodeAnterAmbil', "Ambil Sendiri");
                }

                if(ongkir > 0){
                     detailHtml += `
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Ongkir</span>
                            <span>Rp ${ongkir.toLocaleString('id-ID')}</span>
                        </li>
                    `;
                }else if(ongkir === 0 && metode === 'Kurir'){
                    detailHtml += `
                        <li class="list-group-item d-flex justify-content-between ">
                            <span>Ongkir</span>
                            <span class="text-success">Gratis</span>
                        </li>
                    `;

                }else if(metode === 'Ambil Sendiri'){
                    detailHtml += `
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Ongkir</span>
                            <span class="text-success">Gratis</span>
                        </li>
                    `;
                }

                const totalAkhir = totalHarga + ongkir;

                detailHtml += `
                        </ul>
                        <p class="mt-3 fw-bold">Total: Rp <span id="total-harga">${totalAkhir.toLocaleString('id-ID')}</span></p>
                        <button class="btn btn-success w-100 mt-3" id="checkout-btn">Checkout</button>
                    </div>
                `;

                detailContainer.innerHTML = detailHtml;

                const checkoutBtn = document.getElementById('checkout-btn');
                if (checkoutBtn) {
                    checkoutBtn.addEventListener('click', function() {
                        window.location.href = "/checkout";
                    });
                }

                const dataCheckout = {
                    barang: barangData,
                    metode_pengiriman: metode,
                    total_harga: document.getElementById("total-harga").innerText,
                    ongkir: ongkir,
                    totalhargabarang: totalHarga
                };
                localStorage.setItem("data_checkout", JSON.stringify(dataCheckout));

                 const metode_pengiriman = localStorage.getItem("metode");
            }
            fetchBarang();
        });
    </script>

</body>
</html>