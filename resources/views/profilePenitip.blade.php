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
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color:rgb(90, 170, 75);">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Nav-bar kiri -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{url('/penitip/dashboard')}}">
                            <strong>Home</strong>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('/VerifBeasiswa')}}">
                            <strong>History Transaksi</strong>
                        </a>
                    </li>
    
                </ul>

                <!-- Nav-bar kanan -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item active">
                        <a class="nav-link d-flex align-items-center" href="{{url('/penitip/profile')}}">
                            <i class="fas fa-tools"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('/HomeSebelumLogin')}}"><strong>Logout</strong></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Main Content -->
    <h1 class="text-center mb-4 mt-4" style="color:rgb(0, 138, 57); font-family: 'Bagel Fat One', system-ui;">
        Profile Pengguna
    </h1>
    <div class="d-flex justify-content-center ms-5">
    <!-- Profil Card -->
    <div class="col-md-4 mb-4 mt-4" style="width: 350px;">
        <div class="card mb-3 bg-subtle card shadow-lg" style="border-radius: 1rem;" >
            <div class="card-body text-center">
                <img src="{{ asset('img/pp.png') }}" alt="Avatar" class="img-fluid img-circle my-2 rounded-circle mb-3" style="width: 150px;" />
                <h4 style="color:rgb(0, 138, 57)">Penitip</h4>
            </div>
            <div class="row d-flex justify-content-around">
                <!-- Poin -->
                <div class="col-md-5 mb-3 d-flex flex-column align-items-center">
                    <h6 style="color:rgb(0, 138, 57);"><strong>Poin</strong></h6>
                    <p id="poin" class="text-muted">Loading...</p>
                </div>

                <!-- Saldo -->
                <div class="col-md-5 mb-3 d-flex flex-column align-items-center">
                    <h6 style="color:rgb(0, 138, 57)"><strong>Saldo</strong></h6>
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
    </script>

</body>
</html>
