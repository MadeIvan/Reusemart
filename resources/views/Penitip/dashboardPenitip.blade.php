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
		const token = localStorage.getItem("auth_token");
			if (!token) {
				window.location.href = "{{ url('/UsersLogin') }}";
			}
	</script>

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Poppins', sans-serif;
        }
    </style>

</head>
<body>
    @include('layouts.navbar')
    <!-- Navbar dengan container -->
     <!-- <nav class="navbar navbar-expand-lg navbar-light bg-lightshadow-sm">
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
    </nav> -->

    <hr style="margin: 0; border: 2px solid #dee2e6;"/>
    <!-- Main Content -->
    <div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 border p-4 rounded shadow">
        
        </div>
    </div>
</div>

    <script>
        /////////////////////buat logout///////////////////////
        document.getElementById('logoutLink').addEventListener('click', function (e) {
            e.preventDefault();

            const token = localStorage.getItem('auth_token');

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
