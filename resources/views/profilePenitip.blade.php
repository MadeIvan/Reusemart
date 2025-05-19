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
    <!-- <nav class="navbar navbar-expand-lg navbar-light bg-light  shadow-sm">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav"> -->
                <!-- Nav-bar kiri -->
                <!-- <ul class="navbar-nav me-auto">
                    <li class="nav-item d-flex align-items-center">
                        <img src="{{ asset('logoReUseMart.png') }}" alt="Logo Reusemart" style="width:50px;">
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <a class="nav-link text-black" href="{{url('/penitip/dashboard')}}">
                            <strong>Home</strong>
                        </a>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <a class="nav-link text-black" href="{{url('/penitip/history')}}">
                            <strong>History Transaksi</strong>
                        </a>
                    </li>
                </ul> -->

                <!-- Nav-bar kanan -->
                <!-- <ul class="navbar-nav ms-auto mb-2 mb-lg-0 profile-menu"> 
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="profile-pic d-inline">
                                <img src="{{ asset('img/pp.png') }}" alt="Profile Picture" style="width:35px;" class="rounded-circle">
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{url('/penitip/profile')}}"><i class="fas fa-sliders-h fa-fw"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" id="logoutLink"><i class="fas fa-sign-out-alt fa-fw" ></i> Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <hr style="margin: 0; border: 2px solid #dee2e6;"/> -->
    @include('layouts.navbar')


    <!-- Main Content -->
    <h1 class="text-center mb-4 mt-4" style="color:rgb(0, 138, 57); font-family: 'Bagel Fat One', system-ui;">
        Profile Pengguna
    </h1>
    <div class="d-flex justify-content-center ms-5">
    <!-- Profil Card -->
    <div class="d-flex justify-content-center mt-4 mb-4">
        <div class="card shadow-lg" style="width: 350px; border-radius: 1rem;">
            <div class="card-body text-center">
                <img src="{{ asset('img/pp.png') }}" alt="Avatar"
                    class="img-fluid rounded-circle mb-3"
                    style="width: 150px;" />
                <h4 class="text-success">Penitip</h4>
            </div>
            <div class="row px-3 pb-3">
                <!-- Poin -->
                <div class="col-6 text-center">
                    <h6 class="text-success"><strong>Poin</strong></h6>
                    <p id="poin" class="text-muted">Loading...</p>
                </div>

                <!-- Saldo -->
                <div class="col-6 text-center">
                    <h6 class="text-success"><strong>Saldo</strong></h6>
                    <p id="saldo" class="text-muted">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Account Information Card -->
    <div class="col-md-4 mb-4 mt-4 ms-5" style="width: 940px;">
        <div class="card bg-subtle shadow-lg" style="border-radius: 0.5rem;">
            <div class="card-body p-4">
                <h3 class="text-center mb-5" style="color:rgb(0, 138, 57); font-family: 'Bagel Fat One', system-ui;">
                    Informasi Umum
                </h3>
                <div class="row">
                    <!-- Nama Lengkap -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 style="color:rgb(0, 138, 57)"><strong>Nama Lengkap</strong></h6>
                                <p id="namaPenitip" class="text-muted">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <!-- NIK -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 style="color:rgb(0, 138, 57)"><strong>NIK</strong></h6>
                                <p id="nik">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <!-- username -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 style="color:rgb(0, 138, 57)"><strong>Username</strong></h6>
                                <p id="username" class="text-muted">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 style="color:rgb(0, 138, 57)"><strong>Alamat</strong></h6>
                                <p id="alamat" class="text-muted">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag
        const auth_token = localStorage.getItem("auth_token");
        console.log("Token yang digunakan:", auth_token);
			if (!auth_token) {
				window.location.href = "{{ url('/UsersLogin') }}";
			}
        /////////////////////buat profile/////////////////////
    // const auth_token = localStorage.getItem('auth_token');

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
                        "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                        'Accept': 'application/json',
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
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
                    localStorage.removeItem('auth_token');
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
