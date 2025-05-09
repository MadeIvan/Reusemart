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

    <script>
		const token = localStorage.getItem("token");
			if (!token) {
				window.location.href = "{{ url('/pembeli/login') }}";
			}
	</script>

    <style>
        .register-button button:hover {
            background-color: #006666;
        }
    </style>

</head>
<body>
    <!-- Navbar dengan container -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color:rgb(24, 134, 4);">
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
    <div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 border p-4 rounded shadow">
        <h2 class="text-center" style="color:rgb(24, 134, 4); font-weight: bold;">Registrasi Organisasi</h2>
            <form>
                <div class="mb-3">
                    <label for="namaOrganisasi" class="form-label"><strong>Nama Organisasi</strong></label>
                    <input type="text" class="form-control" id="namaOrganisasi">
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label"><strong>Alamat</strong></label>
                    <input type="text" class="form-control" id="alamat">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label"><strong>Email</strong></label>
                    <input type="email" class="form-control" id="email">
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label"><strong>Username</strong></label>
                    <input type="text" class="form-control" id="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><strong>Password</strong></label>
                    <input type="password" class="form-control" id="password">
                </div>
                <div class="d-flex justify-content-center register-button">
                    <button type="submit" class="btn btn-success item-center">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script>
    </script>

</body>
</html>
