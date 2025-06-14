<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reusemart</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Poppins', sans-serif;
        }

        .card {
            border: none;
            border-radius: 1rem;
            background-color: #ffffff;
            transition: transform 0.3s ease;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: scale(1.01);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .img-circle {
            border: 4px solid #4caf50;
            padding: 2px;
        }

        h6, h4 {
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

    </style>

</head>
<body>
    <div class="modal fade" id="detailBarang" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="exampleModalLabel"><strong>Detail Barang</strong></h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Nama Barang: </strong><span id="namaBarang"></span>
                </div>
                <div class="mb-3">
                    <strong>Berat Barang: </strong><span id="beratBarang"></span>
                </div>
                <div class="mb-3">
                    <strong>Harga Barang: </strong><span id="hargaBarang"></span>
                </div>
                <div class="mb-3">
                    <strong>Tanggal Penitipan Barang: </strong><span id="tanggalPenitipan"></span>
                </div>
                <div class="mb-3">
                    <strong>Tanggal Penitipan Selesai: </strong><span id="tanggalPenitipanSelesai"></span>
                </div>
                <div class="mb-3">
                    <strong>Kategori Barang: </strong><span id="kategori"></span>
                </div>
                <div class="mb-3">
                    <strong>Status Barang: </strong><span id="statusBarang"></span>
                </div>
                <div class="mb-3">
                    <strong>Tanggal Barang Terjual: </strong><span id="tanggalTerjual"></span>
                </div>
            </div>
        </div>
    </div>
</div>

    @include('layouts.navbar')

    <!--//////////////////////////////////////////// Main Content//////////////////////////////////// -->
    <h3 class="text-center mb-4 mt-4" style="color:rgb(0, 138, 57); font-family: 'Bagel Fat One', system-ui;">
        Riwayat Penitipan Barang
    </h3>

    <div id="barangContainer" class="row g-3 px-5"></div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag
        const token = localStorage.getItem("auth_token");
        console.log("Token yang digunakan:", token);
			if (!token) {
				window.location.href = "{{ url('/UsersLogin') }}";
			}

        document.addEventListener("DOMContentLoaded", function(){
            const barangContainer = document.getElementById("barangContainer");   
            let idDetail = null;         
            fetchBarang();

            ////////////////////////SHOW barang///////////////////////////////////
            function fetchBarang(){
                fetch("http://127.0.0.1:8000/api/penitip/history", {
                    method: "GET",
                    headers: {
                        "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                        'Accept': 'application/json',
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status && Array.isArray(data.data)) {
                        renderBarang(data.data);
                    } else {
                        console.error("Respon tidak valid atau kosong:", data);
                    }
                })
                .catch(error => console.error("Error fetching barang:", error));
            }

            ////////////////////////CARD barang///////////////////////////////////
            function renderBarang(data) {
                barangContainer.innerHTML = "";
                data.forEach(item => {
                    item.detail_transaksi_penitipan.forEach(transaksi => {
                        const barang = transaksi.barang;

                        // Cek apakah ada transaksi pembelian jika status barang adalah "terjual"
                        let tanggalTerjual = "-";
                        if (barang.statusBarang.toLowerCase() === "terjual" && barang.detail_transaksi_pembelian.length > 0) {
                            // Akses tanggalWaktuPembelian jika ada transaksi pembelian
                            const pembelian = barang.detail_transaksi_pembelian[0].transaksi_pembelian;
                            tanggalTerjual = pembelian.tanggalWaktuPembelian; // Ambil tanggalWaktuPembelian
                        }

                        const tanggalPenitipan = transaksi.tanggalPenitipan || item.tanggalPenitipan;
                        const tanggalPenitipanSelesai = transaksi.tanggalPenitipanSelesai || item.tanggalPenitipanSelesai;
                        let statusClass = "";
                        switch (barang.statusBarang.toLowerCase()) {
                            case "dikembalikan":
                                statusClass = "text-danger"; // merah
                                break;
                            case "didonasikan":
                                statusClass = "text-primary"; // biru
                                break;
                            case "terjual":
                                statusClass = "text-success"; // hijau
                                break;
                            case "tersedia":
                                statusClass = "text-secondary"; // default/abu
                        }
                        const card = `
                            <div class="col-md-3 p-2">
                                <div class="card">
                                    <img src="/img/${barang.image}" class="card-img-top" alt="Foto Produk">
                                    <div class="card-body position-relative">
                                        <div class="d-flex align-items-center gap-2">
                                            <h5 class="card-title mb-2 text-justify"><strong>${barang.namaBarang}</strong></h5>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <p class="card-subtitle ${statusClass} mt-2 ">${barang.statusBarang}</p>
                                            <button type="button" class="btn btn-detail btn-outline-primary mt-3" 
                                                data-id="${barang.idBarang}" 
                                                data-namaBarang="${barang.namaBarang}"
                                                data-beratBarang="${barang.beratBarang}"
                                                data-hargaBarang="${barang.hargaBarang}"
                                                data-kategori="${barang.kategori}"
                                                data-tanggalPenitipan="${tanggalPenitipan}"
                                                data-tanggalPenitipanSelesai="${tanggalPenitipanSelesai}"
                                                data-statusBarang="${barang.statusBarang}"
                                                data-tanggalPembelian="${tanggalTerjual}" 
                                                data-bs-toggle="modal" data-bs-target="#detailBarang">
                                                Lihat Detail
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        barangContainer.innerHTML += card;
                    });
                });
            }


            
        });

        // document.querySelectorAll(".btn-detail").forEach(button => {
            document.addEventListener("click", function (e) {
                if (e.target.classList.contains("btn-detail")) {
                    const button = e.target;
                    const namaBarang = button.getAttribute("data-namaBarang");
                    const beratBarang = button.getAttribute("data-beratBarang");
                    const hargaBarang = button.getAttribute("data-hargaBarang");
                    const kategori = button.getAttribute("data-kategori");
                    const tanggalPenitipan = button.getAttribute("data-tanggalPenitipan");
                    const tanggalPenitipanSelesai = button.getAttribute("data-tanggalPenitipanSelesai");
                    const statusBarang = button.getAttribute("data-statusBarang");
                    const tanggalTerjual = button.getAttribute("data-tanggalPembelian");

                    function formatTanggalJam(tanggalString) {
                        if (!tanggalString) return "-";
                        const date = new Date(tanggalString);
                        return date.toLocaleString("id-ID", {
                            day: "numeric",
                            month: "long",
                            year: "numeric",
                            hour: "2-digit",
                            minute: "2-digit",
                        });
                    }

                    function formatTanggalSaja(tanggalString) {
                        if (!tanggalString) return "-";
                        const date = new Date(tanggalString);
                        return date.toLocaleDateString("id-ID", {
                            day: "numeric",
                            month: "long",
                            year: "numeric",
                        });
                    }

                    // Isi modal
                    document.getElementById("namaBarang").textContent = namaBarang;
                    document.getElementById("beratBarang").textContent = beratBarang;
                    document.getElementById("hargaBarang").textContent = hargaBarang;
                    document.getElementById("kategori").textContent = kategori;
                    document.getElementById("tanggalPenitipan").textContent = formatTanggalJam(tanggalPenitipan);
                    document.getElementById("tanggalPenitipanSelesai").textContent = formatTanggalSaja(tanggalPenitipanSelesai);
                    document.getElementById("statusBarang").textContent = statusBarang;
                    document.getElementById("tanggalTerjual").textContent = formatTanggalJam(tanggalTerjual);
                }
    });

        /////////////////////buat profile/////////////////////

    fetch('http://localhost:8000/api/penitip/profile', {
        method: 'GET',
        headers: {
            "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
            'Accept': 'application/json',
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            const user = data.data;
            function ubahFormat(angka) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
            }

            // Update data dan hilangkan "Loading..."
            document.getElementById('namaPenitip').textContent = user.namaPenitip ?? '-';
            document.getElementById('nik').textContent = user.nik ?? '-';
            document.getElementById('username').textContent = user.username ?? '-';
            document.getElementById('alamat').textContent = user.alamat ?? '-';
            document.getElementById('poin').textContent = user.poin ? `${user.poin} Poin` : '0 Poin';
            document.getElementById('saldo').textContent = user.dompet && user.dompet.saldo != null ? ubahFormat(user.dompet.saldo) : '-';
            
            // Sembunyikan teks "Loading..." yang ada sebelumnya
            document.getElementById('namaPenitip').classList.remove('text-muted');
            document.getElementById('nik').classList.remove('text-muted');
            document.getElementById('username').classList.remove('text-muted');
            document.getElementById('alamat').classList.remove('text-muted');
            document.getElementById('poin').classList.remove('text-muted');
            document.getElementById('saldo').classList.remove('text-muted');
        } else {
            console.error('Gagal ambil data user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });

    /////////////////////buat logout///////////////////////
        document.getElementById('logoutLink').addEventListener('click', function (e) {
            e.preventDefault();

            const auth_token = localStorage.getItem('auth_token');

            if (auth_token) {
                fetch('http://localhost:8000/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + auth_token,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data.message); // opsional: tampilkan pesan sukses logout
                })
                .catch(error => {
                    console.error('Logout error:', error);
                })
                .finally(() => {
                    // Bersihkan token & redirect ke halaman awal
                    localStorage.removeItem('token');
                    window.location.href = '/';
                });
            } else {
                // Jika token tidak ada, langsung redirect
                window.location.href = '/';
            }
        });
    </script>

</body>
</html>
