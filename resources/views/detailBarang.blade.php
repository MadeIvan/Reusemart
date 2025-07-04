<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Toastify ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
</head>

<style>
    .diskusi-tanggal-pembeli{
        display: flex;
        justify-content: left;
        gap: 10px;
    }

    .diskusi-tanggal-pegawai{
        display: flex;
        justify-content: right;
        gap: 10px;
    }

    /* .diskusi-pegawai{
        display: flex;
        justify-content: left;
        gap: 10px;
    } */
</style>

<body>
    @include('layouts.navbar')

    <header class="bg-dark text-white p-4">
        <div class="container text-center">
            <h1>Product Detail</h1>
        </div>
    </header>

    <div class="container my-5" id="product-detail">
        <!-- Product details will be dynamically loaded here -->
    </div>

    <div class="container my-3" id="diskusi-container">
        <!-- Diskusi produk akan muncul di sini -->
    </div>

    <div class="container my-3 kirim-diskusi" style="display: none;">
        <div class="col-md-8 mt-3">
            <textarea class="form-control " id="exampleFormControlTextarea1" rows="2" placeholder="Tulis Komentar"></textarea>
        </div>
        <div class="d-flex justify-content-end col-md-8 mt-3">
            <button class="btn btn-dark" id="kirimBtn">Kirim</button>
        </div>
    </div>

    <footer class="bg-dark text-white text-center p-3">
        <p>&copy; 2025 Reusemart</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag
            const token = localStorage.getItem('auth_token');
            const pathSegments = window.location.pathname.split('/');
            const productId = pathSegments[pathSegments.length - 1]; 
            
            role = localStorage.getItem("user_role");
            
            
            // Fetch the product data from the API
            fetch(`http://127.0.0.1:8000/api/getBarang/${productId}`)
                .then(response => response.json())
                .then(product => {
                    // Get the product detail container
                    const productDetailContainer = document.getElementById('product-detail');

                    // Check if the product was returned successfully
                   
                        // Format the product price
                        const formattedPrice = Number(product.hargaBarang).toLocaleString('id-ID');

                        // Create the product detail HTML
                        const productDetailHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="{{ asset('${product.image}') }}" class="img-fluid" alt="${product.namaBarang}">
                                </div>
                                <div class="col-md-6">
                                    <h2>${product.namaBarang}</h2>    
                                    <p class="text-success">Rp. ${formattedPrice}</p>

                                    <!-- Add to Cart Button -->
                                    <button type="button" class="btn btn-dark add-to-cart-btn" data-id="${product.idBarang}">Add to Cart</button>
                                </div>
                            </div>
                            
                        `;

                        if(role === "cs") {
                            productDetailContainer.innerHTML = productDetailHTML;
                           const addToCartBtn = productDetailContainer.querySelector('.add-to-cart-btn');
                            if (addToCartBtn) {
                                addToCartBtn.disabled = true;
                            }
                        } else if (role === "pembeli") {
                            document.querySelector('.kirim-diskusi').style.display = 'block';
                            document.querySelector('.btn.btn-dark').addEventListener('click', kirimDiskusi);
                            productDetailContainer.innerHTML = productDetailHTML;
                            const addToCartBtn = productDetailContainer.querySelector('.add-to-cart-btn');
                            if (addToCartBtn) {
                                addToCartBtn.addEventListener('click', function(event) {
                                    event.preventDefault(); // <- tambahkan ini
                                    const idBarang = this.getAttribute('data-id');
                                    addToCart(idBarang);
                                });
                            }
                        }
                      

                        getDiskusi(productId);

                        // Handle product not found
                        // productDetailContainer.innerHTML = `<p>Product not found.</p>`;
                    
                })
                .catch(error => {
                    console.error('Error fetching product details:', error);
                    document.getElementById('product-detail').innerHTML = `<p>Error loading product details.</p>`;
                });
        });

        function getDiskusi(idBarang) {
            fetch(`http://127.0.0.1:8000/api/diskusi/${idBarang}`)
            .then(response => response.json())
            .then(response => {
                let diskusiItems = '';
                const data = response.data;

                if (data && data.length > 0) {
                    data.forEach(item => {
                        let namaPengirim = '';
                        if(item.idPembeli){
                            namaPengirim = item.pembeli ? item.pembeli.username : 'Pembeli tidak tersedia';
                        }else if (item.idPegawai) {
                            namaPengirim = item.pegawai ? item.pegawai.username : 'Pegawai tidak tersedia';
                        }

                        if (item.idPembeli) {
                                diskusiItems += `
                                <div class="diskusi-tanggal-pembeli">
                                    <p class="text-success"><strong>${namaPengirim} | </strong></p>
                                    <p><strong>${item.tanggalDiskusi}</strong></p>
                                    <p><strong>${item.waktuMengirimDiskusi}</strong></p>
                                </div>
                                    <p > ${item.pesandiskusi}</p>
                                `;
                        } else if (item.idPegawai) {
                            diskusiItems += `
                            <div class="diskusi-tanggal-pegawai">
                                    <p class="text-success"><strong>${namaPengirim}  | </strong></p>
                                    <p><strong>${item.tanggalDiskusi}</strong></p>
                                    <p><strong>${item.waktuMengirimDiskusi}</strong></p>
                                </div>
                            <p class="diskusi-pegawai"> ${item.pesandiskusi}</p>
                            `;
                        }
                        diskusiItems += `<hr/>`;
                    });
                }

                const diskusiHTML = `
                    <div class="col-md-8 mt-5">
                        <h2><strong>Diskusi Produk</strong></h2>
                        <ul>${diskusiItems}</ul>
                    </div>
                `;

                document.getElementById('diskusi-container').innerHTML = diskusiHTML;
            })
            .catch(error => {
                console.error("Error fetching diskusi data:", error);
                document.getElementById('diskusi-container').innerHTML = `<p>Error loading diskusi.</p>`;
            });

        }

        function kirimDiskusi() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag
            const token = localStorage.getItem('auth_token');
            const komentar = document.getElementById('exampleFormControlTextarea1').value.trim();
            const idBarang = window.location.pathname.split('/').pop();
            const btn = document.getElementById('kirimBtn');
            const role = localStorage.getItem("user_role");

            const endpoint = role === "cs" 
                ? `http://127.0.0.1:8000/api/pegawai/buat-diskusi/${idBarang}` 
                : `http://127.0.0.1:8000/api/pembeli/buat-diskusi/${idBarang}`;
            
            console.log('Token:', token); // Periksa token
            console.log('ID Barang:', idBarang); // Periksa ID Barang
            console.log('Komentar:', komentar); // Periksa komentar
            
            if (!token) {
                alert("Silakan login terlebih dahulu untuk mengirim komentar.");
                return;
            }

            if (!komentar) {
                alert("Komentar tidak boleh kosong.");
                return;
            }

            btn.disabled = true;
            btn.textContent = "Mengirim...";

            fetch(endpoint,{
                method: 'POST',
                headers: {
                    "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                    'Accept': 'application/json',
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    },
                body: JSON.stringify({
                    pesandiskusi: komentar
                })
            })
            .then(response => {
                console.log('Response Status:', response.status);
                return response.json();
            })
            .then(response => {
                console.log('Response Data:', response);
                alert("Komentar berhasil dikirim!");
                document.getElementById('exampleFormControlTextarea1').value = ""; 
                getDiskusi(idBarang);
            })
            .catch(error => {
                console.error("Error mengirim diskusi:", error);
                alert("Terjadi kesalahan saat mengirim komentar.");
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = "Kirim";
            });
        }


        function addToCart(idBarang){
            const token = localStorage.getItem('auth_token');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag
            if (!token) {
                alert("Silakan login terlebih dahulu untuk menambahkan ke keranjang.");
                return;
            }

            fetch(`http://127.0.0.1:8000/api/tambah-keranjang/${idBarang}`,{
                method: 'POST',
                headers: {
                    "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                    'Accept': 'application/json',
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
            })
            .then(async (response) => {
                const status = response.status;
                const data = await response.json();

                if (status === 200 && data.status) {
                    alert("Produk berhasil ditambahkan ke keranjang!");
                    let currentCount = parseInt(localStorage.getItem("cart_count") || 0);
                    localStorage.setItem("cart_count", currentCount + 1);

                    // Update badge
                    const badge = document.getElementById("cart-count-badge");
                    if (badge) {
                        badge.textContent = currentCount + 1;
                    }
                } else if (status === 409) {
                    alert("Barang sudah ada di keranjang.");
                } else {
                    alert("Terjadi kesalahan saat menambahkan ke keranjang.");
                }
            })
            .catch(error => {
                console.error("Error menambahkan ke keranjang:", error);
                alert("Terjadi kesalahan saat menambahkan ke keranjang.");
            });
        }
            
    </script>
</body>
</html>