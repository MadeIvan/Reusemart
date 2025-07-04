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
        <div class="card mt-3" id="cardAlamatPengiriman">
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

        <!-- Tukar Poin -->
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Poin</h5>
                <div id="poinPembeli">
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
    if (!auth_token) {
        window.location.href = "/UsersLogin";
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const alamatContainer = document.getElementById("alamatPembeli");
    const cardAlamat = document.getElementById("cardAlamatPengiriman");
    const metodePengiriman = localStorage.getItem("metodeAnterAmbil");
    
    let alamatData = [];
    let alamatList = [];
    let idBarang = null;
    let selectedId = null;
    let totalHargaAkhir = 0;
    let barangArray = [];
    let barangData = [];
    
    // Initialize based on shipping method
    if(metodePengiriman === 'Kurir'){
        fetchAlamat();
        cardAlamat.style.display = "block";
    } else {
        cardAlamat.style.display = "none";
    }

    // Initialize page
    fetchBarang(); 
    tampilkanDetailCheckout();
    tampilkanPoin();

    //////////////////////// ALAMAT FUNCTIONS ////////////////////////
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
                "X-CSRF-TOKEN": csrfToken,
            },
        })
        .then(response => response.json())
        .then(data => {
            if(data.alamatUtama){
                selectedId = data.alamatUtama.idAlamat;
                tampilkanAlamatPengiriman(data.alamatUtama);
            }
        })
        .catch(error => console.error("Error fetching alamat:", error));
    }

    // Event listener for changing address
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
                "X-CSRF-TOKEN": csrfToken,
            },
        })
        .then(response => response.json())
        .then(data => {
            const daftarAlamatModal = document.getElementById("daftarAlamatModal");
            daftarAlamatModal.innerHTML = "";

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

                // Add event listener for address selection
                document.getElementById('btn-pilih-alamat').addEventListener('click', function() {
                    const selectedRadio = document.querySelector('input[name="alamatRadio"]:checked');
                    if (selectedRadio) {
                        const selectedId = selectedRadio.value;
                        pilihAlamat(selectedId);
                        const modalEl = document.getElementById('modalAlamat');
                        let modal = bootstrap.Modal.getInstance(modalEl);
                        if (!modal) {
                            modal = new bootstrap.Modal(modalEl);
                        }
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

    document.getElementById('modalAlamat').addEventListener('hidden.bs.modal', function () {
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });
    
    function pilihAlamat(selectedId) {
        const alamatTerpilih = alamatList.find(a => a.idAlamat == selectedId);
        if (alamatTerpilih) {
            localStorage.setItem('selectedIdAlamat', selectedId);
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

        localStorage.setItem('alamatPengiriman', JSON.stringify(alamat));
    }

    //////////////////////// CART FUNCTIONS ////////////////////////
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
            barangData = response.data;
            barangArray = barangData.map(item => item.idBarang);
            renderCart(barangData);
        })
        .catch(error => console.error("Error fetching cart:", error));
    }

    function renderCart(items) {
        const container = document.querySelector('#product-keranjang');
        container.innerHTML = '';

        if (items.length === 0) {
            container.innerHTML = `<div class="text-center text-muted">Keranjang kosong</div>`;
            return;
        }

        items.forEach(item => {
            const formatHarga = parseInt(item.hargaBarang).toLocaleString('id-ID');
            const itemHtml = `
                <div class="card mb-4 shadow-sm rounded-4 p-3">
                    <div class="d-flex align-items-start">
                        <img src="${item.image}" alt="${item.namaBarang}" class="rounded-3 me-3" style="width: 150px;">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${item.namaBarang}</h6>
                            <p class="text-secondary small mt-1">Berat barang: ${item.beratBarang} kg</p>
                            <p class="mb-0 fw-bold text-success fs-4 text-end">Rp ${formatHarga}</p>
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += itemHtml;
        });
    }

    //////////////////////// DETAIL FUNCTIONS ////////////////////////
    function tampilkanDetailCheckout() {
        const data = JSON.parse(localStorage.getItem("data_checkout"));
        if (!data) {
            document.getElementById("product-detail").innerHTML = "<p>Data checkout tidak tersedia.</p>";
            return;
        }

        const detailContainer = document.getElementById("product-detail");
        let totalAwal = Number(data.total_harga.toString().replace(/\./g, ''));

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

        detailHtml += `
            <li class="list-group-item d-flex justify-content-between">
                <span><strong>Ongkos Kirim</strong></span>
                <span>Rp ${parseInt(data.ongkir).toLocaleString('id-ID')}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span><strong>Poin yang ditukarkan</strong></span>
                <span id="poinDitukarkan">- Rp 0</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span><strong>Total</strong></span>
                <span id="totalHarga">Rp ${totalAwal.toLocaleString('id-ID')}</span>
            </li>
            </ul>
            </div>
        `;
        
        detailContainer.innerHTML = detailHtml;
    }

    //////////////////////// POINTS FUNCTIONS ////////////////////////
    function tampilkanPoin() {
        const dataCheck = JSON.parse(localStorage.getItem("data_checkout"));
        const token = localStorage.getItem('auth_token');
        const container = document.getElementById("poinPembeli");
        let poin = 0;
        let bonusPoin = 0;
        let totalPoin = 0;

        fetch(`http://127.0.0.1:8000/api/pembeli/getData`, {
            method: 'GET',
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                const user = data.data;
                const totalHargaBarang = dataCheck.totalhargabarang;
                const ongkir = Number(dataCheck?.ongkir || 0);

                // Calculate points to be earned
                if(totalHargaBarang > 500000){
                    poin = Math.floor(totalHargaBarang / 10000);
                    bonusPoin = Math.floor(poin * 0.2);
                    totalPoin = poin + bonusPoin;
                } else {
                    totalPoin = Math.floor(totalHargaBarang / 10000);
                }

                container.innerHTML = `
                    <div class="card p-3 shadow-sm rounded-4">
                        <p><strong>Poin Anda:</strong> ${user.poin} poin</p>
                        <div class="form-group mt-2">
                            <label for="inputPoin">Masukkan jumlah poin yang ingin ditukar:</label>
                            <input type="number" id="inputPoin" class="form-control w-50" min="0" max="${user.poin}" value="0">
                            <p class="mt-2">Setara dengan:<span id="hasilKonversi" class="text-muted">Rp 0</span></p>
                        </div>
                        <hr class="my-1">
                        <p>Poin yang didapatkan dari transaksi pembelian barang adalah <strong>${totalPoin}</strong> poin</p>
                        <hr class="my-1">
                        <p id="sisaPoinSetelahTransaksi">Sisa poin setelah transaksi pembelian barang adalah <strong>${user.poin + totalPoin}</strong> poin</p>
                    </div>
                `;
                
                localStorage.setItem('poinUser', user.poin);
                localStorage.setItem('sisaPoin', user.poin + totalPoin);

                const inputPoin = document.getElementById("inputPoin");
                const sisaPoinText = document.getElementById("sisaPoinSetelahTransaksi");
                
                inputPoin.addEventListener("input", () => {
                    const hasilKonversi = document.getElementById("hasilKonversi");
                    const maksimalPoinDitukar = Math.floor(totalHargaBarang + ongkir);

                    let nilaiPoin = parseInt(inputPoin.value) || 0;
                    
                    if (nilaiPoin > user.poin) {
                        nilaiPoin = user.poin;
                        inputPoin.value = user.poin;
                    }

                    let rupiah = nilaiPoin * 100;

                    if(rupiah > maksimalPoinDitukar){
                        nilaiPoin = Math.floor(maksimalPoinDitukar / 100);
                        inputPoin.value = nilaiPoin;
                        rupiah = nilaiPoin * 100;
                    }

                    hasilKonversi.innerHTML = `<strong> Rp ${rupiah.toLocaleString('id-ID')}</strong>`;
                    hasilKonversi.classList.toggle("text-black", nilaiPoin > 0);
                    hasilKonversi.classList.toggle("text-muted", nilaiPoin === 0);

                    // Update points in checkout detail
                    const poinDitukarkan = document.getElementById("poinDitukarkan");
                    const totalHargaAkhir = document.getElementById("totalHarga");

                    if (poinDitukarkan && totalHargaAkhir) {
                        poinDitukarkan.textContent = `- Rp ${rupiah.toLocaleString('id-ID')}`;

                        const totalAwal = totalHargaBarang + ongkir;
                        let totalBaru = totalAwal - rupiah;
                        if (totalBaru < 0) totalBaru = 0;

                        totalHargaAkhir.textContent = `Rp ${totalBaru.toLocaleString('id-ID')}`;
                        localStorage.setItem('totalSetelahPoin', totalBaru);
                    }

                    const sisa = user.poin - nilaiPoin + totalPoin;
                    if (sisaPoinText) {
                        sisaPoinText.innerHTML = `Sisa poin setelah transaksi pembelian barang adalah <strong>${sisa}</strong> poin`;
                    }
                    localStorage.setItem('sisaPoin', sisa);
                });
            } else {
                console.error('Gagal ambil data user');
                container.innerHTML = "<p>Gagal mengambil data pengguna.</p>";
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = "<p>Terjadi kesalahan saat mengambil data.</p>";
        });
    }

    //////////////////////// UTILITY FUNCTIONS ////////////////////////
    function getCurrentDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }

    function hapusKeranjang() {
        return fetch(`http://127.0.0.1:8000/api/hapus-keranjang`, {
            method: "DELETE",
            headers: {
                "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                'Accept': 'application/json',
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
        });
    }

    //////////////////////// CHECKOUT FUNCTION ////////////////////////
    document.getElementById("checkout-btn").addEventListener("click", function() {
        const dataCheckout = JSON.parse(localStorage.getItem("data_checkout"));
        const inputPoinElement = document.getElementById('inputPoin');
        const nilaiPoin = inputPoinElement ? parseInt(inputPoinElement.value) || 0 : 0;
        const sisaPoin = localStorage.getItem('sisaPoin');
        const alamat = JSON.parse(localStorage.getItem('alamatPengiriman'));

        let idAlamat = alamat ? alamat.idAlamat : null;

        const totalHarga = localStorage.getItem('totalSetelahPoin') !== null
            ? parseFloat(localStorage.getItem('totalSetelahPoin'))
            : Number(dataCheckout.total_harga.toString().replace(/\./g, ''));

        fetch(`http://127.0.0.1:8000/api/checkout`, {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                'Accept': 'application/json',
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                idAlamat: idAlamat,
                tanggalWaktuPembelian: getCurrentDateTime(),
                totalHarga: totalHarga,
                id_barang: barangArray,
                sisaPoin: sisaPoin,
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log("Checkout response:", data);

            if (!data.status) {
                throw new Error("Gagal membuat transaksi: " + data.message);
            }

            const noNota = data.data.noNota;
            console.log("Extracted noNota:", noNota);

            if (!noNota || noNota === "0") {
                throw new Error("Invalid transaction ID (noNota) from checkout response");
            }

            // Handle point redemption if points were used
            if (nilaiPoin > 0) {
                const userData = localStorage.getItem('userData');
                const pembeli = userData ? JSON.parse(userData).pembeli : null;
                const idPembeli = pembeli?.idPembeli;

                if (idPembeli) {
                    fetch('http://127.0.0.1:8000/api/point-redemptions', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            idPembeli: idPembeli,
                            points_used: nilaiPoin,
                            transaction_id: noNota,
                        }),
                    }).catch(err => console.error("Point redemption error:", err));
                }
            }

            // Clear cart and redirect
            hapusKeranjang().then(() => {
                if (totalHarga > 0) {
                    window.location.href = `/pembayaran/${noNota}`;
                } else {
                    window.location.href = `/home`;
                }
            }).catch(err => console.error("Error clearing cart:", err));

            // Show success message
            Toastify({
                text: "Berhasil Membuat Pesanan",
                duration: 3000,
                gravity: "top",
                position: "right",
                style: { background: "#8bc34a" },
            }).showToast();

            sessionStorage.setItem('pesananSelesai', '1');
        })
        .catch(error => {
            console.error("Error:", error);
            
            // Show error message
            Toastify({
                text: error.message || "Gagal Membuat Pesanan",
                duration: 3000,
                gravity: "top",
                position: "right",
                style: { background: "rgb(221, 25, 25)" },
            }).showToast();
        });
    });
});
   </script>
</body>
</html>