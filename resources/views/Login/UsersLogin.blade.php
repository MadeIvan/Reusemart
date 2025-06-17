<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" 
          crossorigin="anonymous">

    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" 
            integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" 
            crossorigin="anonymous"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" 
            crossorigin="anonymous"></script>

    <style>
        /* Ensuring the video covers the full background */
        #video-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
    </style>


</head>
<body>
    <!-- //////////////////ini background video///////////////////// -->
    <div class="position-relative" style="height: 100vh; overflow: hidden;">
        <video autoplay muted loop id="video-bg">
            <source src="{{ asset('ReUseMartVid.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- /////////////////////////////////////ini navbar////////////////////////// -->
        <nav class="navbar navbar-expand-lg navbar-light shadow-sm fixed-top" style="background-color: rgba(255, 255, 255, 0.0); backdrop-filter: blur(5px);">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- Nav-bar kiri -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link active text-black" href="{{url('/')}}" >
                                <!-- <strong>Home</strong> -->
                                <img src="{{ asset('logoReUseMart.png') }}" alt="Logo Reusemart" style="width:50px;">
                            </a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link text-white" href="{{url('/home')}}">
                                Produk
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Overlay for readability, with adjusted opacity -->
        <div class="position-absolute w-100 h-100 bg-dark" style="opacity: 0.4; z-index: -1;"></div>

        <!-- Login Form -->
        <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh; padding-top: 100px;">
            <div class="row w-100">
                <div class="col-12 col-md-6 col-lg-4 mx-auto bg-light p-4 rounded-3 shadow" style="z-index: 1;">
                    <h3 class="text-center mb-4">Login to Your Account</h3>
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" placeholder="Enter username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Enter password" required>
                        </div>

                        <!-- Status Dropdown -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" required>
                                <option value="" disabled selected>Select Status</option>
                                <option value="penitip">Penitip</option>
                                <option value="pembeli">Pembeli</option>
                                <option value="organisasi">Organisasi</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <p class="text-center mt-3">
                        Don't have an account for organization?
                        <a href="/organisasi/register" class="text-decoration-none">Click here!</a>
                    </p>
                     <p class="text-center mt-3">
                        Don't have an account for buyer?
                        <a href="/pembeli/register" class="text-decoration-none">Click here!</a>
                    </p>
                    <p class="text-center mt-2">
                        <a href="/resetPassword" id="forgotPasswordLink" class="text-decoration-none">Lupa password?</a>
                    </p>

                </div>
            </div>          
        </div>


    <script>


document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('#loginForm').addEventListener('submit', async function(event) {
        event.preventDefault(); // Prevent the default form submission

        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();
        const status = document.getElementById('status').value;

        const data = { username, password };
        const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
        try {
            const response = await fetch(`http://127.0.0.1:8000/api/${status}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(data)
            });
            const resData = await response.json();
            const userStorage=resData.data
            ////////////////nambah ini sekalian buat simpen data user di localStorage///////////////////////////////////
            localStorage.setItem('userData', JSON.stringify(userStorage));
            console.log(resData);
            if (!response.ok) {
                alert(resData.message || 'Login failed');
                return;
            }
        alert('Login successful! Login as ' + status);
            

            if (status == 'penitip'  && resData.penitip) {
                localStorage.setItem('auth_token', resData.penitip.token);
                localStorage.setItem('user_role', 'penitip');
                window.location.href = 'http://127.0.0.1:8000/penitip/dashboard';
            } else if (status == 'pembeli') {
                localStorage.setItem('auth_token', resData.data.token);
                localStorage.setItem('user_role', 'pembeli');
                window.location.href = 'http://127.0.0.1:8000/pembeli/dashboard';
            } else if (status == 'organisasi') {
                localStorage.setItem('auth_token', resData.data.token);
                localStorage.setItem('user_role', 'organisasi');
                window.location.href = 'http://127.0.0.1:8000/OrganisasiMain'

            }
        });
    });
</script>
</body>
</html>