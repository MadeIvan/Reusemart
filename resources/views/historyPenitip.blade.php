<!DOCTYPE html>
<html lang="en">
<head>
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

    <!-- <script>
		const token = localStorage.getItem("token");
			if (!token) {
				window.location.href = "{{ url('/pembeli/login') }}";
			}
	</script> -->

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
    <!-- Navbar dengan container -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color:rgb(255, 255, 255);">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Nav-bar kiri -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active text-black" href="{{url('/penitip/dashboard')}}" >
                            <strong>Home</strong>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-black" href="{{url('/penitip/history')}}">
                            <strong>History Transaksi</strong>
                        </a>
                    </li>
                </ul>

                <!-- Nav-bar kanan -->
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 profile-menu"> 
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="profile-pic d-inline">
                                <img src="{{ asset('img/pp.png') }}" alt="Profile Picture" style="width:35px;" class="rounded-circle">
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{url('/penitip/profile')}}"><i class="fas fa-sliders-h fa-fw"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item"  id="logoutLink"><i class="fas fa-sign-out-alt fa-fw"></i> Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!--//////////////////////////////////////////// Main Content//////////////////////////////////// -->
    <h3 class="text-center mb-4 mt-4" style="color:rgb(0, 138, 57); font-family: 'Bagel Fat One', system-ui;">
        Riwayat Penitipan Barang
    </h3>

    <div id="barangContainer" class="row g-3 px-5"></div>

    <script>
        document.addEventListener("DOMContentLoaded", function(){
            const barangContainer = document.getElementById("barangContainer");            
            fetchBarang();

            ////////////////////////SHOW barang///////////////////////////////////
            function fetchBarang(){
                fetch("http://127.0.0.1:8000/api/penitip/history", {
                    method: "GET",
                    headers: {
                        "Authorization": `Bearer ${localStorage.getItem("token")}`,
                        "Content-Type": "application/json",
                    },
                })
                .then(response => response.json())
                .then(data => {
                    barangData = data.data;
                    renderBarang(data.data);
                })
                .catch(error => console.error("Error fetching barang:", error));
            }

            ////////////////////////CARD barang///////////////////////////////////
            function renderBarang(data){
                barangContainer.innerHTML = "";
                data.forEach(item => {
                    item.transaksi_penitipan.forEach(transaksi => {
                        const barang = transaksi.barang;
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
                        <div class="card ">
                            <img src="/img/${barang.image}" class="card-img-top" alt="Foto Produk"">
                            <div class="card-body position-relative">
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="card-title mb-2 text-justify"><strong>${barang.namaBarang}</strong></h5>
                                </div>
                                <p class="card-subtitle ${statusClass} mt-2 ">${barang.statusBarang}</p>
                            </div>
                        </div>
                    </div>
                        `;
                        barangContainer.innerHTML += card;
                    });
                });
            }
            
            // searchInput.addEventListener("input", () => {
            //     const query = searchInput.value.toLowerCase();
            //     fetch(`http://127.0.0.1:8000/api/pembeli/alamat/search?q=${query}`, {
            //         headers: { 
            //             "Authorization": `Bearer ${localStorage.getItem('token')}` },
            //     })
            //         .then(response => response.json())
            //         .then(data => renderAlamat(data.data))
            //         .catch(error => console.error("Error searching alamat:", error));
            // });
        });

        /////////////////////buat profile/////////////////////
        const token = localStorage.getItem('token');

    fetch('http://localhost:8000/api/penitip/profile', {
        method: 'GET',
        headers: {
            "Authorization": `Bearer ${localStorage.getItem("token")}`,
            "Accept": 'application/json',
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

            const token = localStorage.getItem('token');

            if (token) {
                fetch('http://localhost:8000/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
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
