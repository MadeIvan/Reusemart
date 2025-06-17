<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Page</title>
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
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

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
        <div class="container d-flex justify-content-center align-items-center h-100">
            <div class="row w-100">
                <div class="col-12 col-md-6 col-lg-4 mx-auto bg-light p-4 rounded-3 shadow" style="z-index: 1;">
                    <h3 class="text-center mb-4">Registrasi Pembeli</h3>
                    <form>
                        <div class="mb-3">
                            <label for="namaPembeli" class="form-label">Nama Pembeli</label>
                            <input type="text" class="form-control" id="namaPembeli" placeholder="Enter name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter email">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" placeholder="Enter username">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Enter password">
                        </div>
                        <div class="d-flex justify-content-center register-button">
                            <button type="submit" class="btn btn-primary w-100 item-center">Registrasi</button>
                        </div>
                    </form>
                    <p class="text-center mt-3">
                    Already have an account for buyer?
                    <a href="/UsersLogin" class="text-decoration-none">Click here!</a>
                </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const registerButton = document.querySelector('.register-button button');
            registerButton.addEventListener('click', async function (e) {
                e.preventDefault();
                try{
                    const namaPembeli = document.getElementById('namaPembeli').value.trim();
                    const email = document.getElementById('email').value.trim();
                    const username = document.getElementById('username').value.trim();
                    const password = document.getElementById('password').value.trim();

                    if (!namaPembeli || !email || !username || !password) {
                        Toastify({
                            text: "Mohon untuk mengisi seluruh form.",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "rgb(214, 10, 10)",
                        }).showToast();
                        return;
                    }

                    const checkResponse = await fetch(`http://127.0.0.1:8000/api/check-email-username?email=${encodeURIComponent(email)}&username=${encodeURIComponent(username)}`, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            'X-CSRF-TOKEN': csrfToken,
                        }
                    })

                    if (!checkResponse.ok) {
                        const errorData = await checkResponse.json();
                        throw errorData;
                    }

                    const checkResult = await checkResponse.json();
                    if (checkResult.emailExists) {
                        Toastify({
                            text: "Email telah digunakan. Mohon untuk menggunakan Email lain.",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "rgb(214, 10, 10)",
                        }).showToast();
                        return;
                    }
                    if (checkResult.usernameExists) {
                        Toastify({
                            text: "Username telah digunakan. Mohon untuk menggunakan Username lain",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "rgb(214, 10, 10)",
                        }).showToast();
                        return;
                    }

                    const data = {
                        username,
                        password,
                        namaPembeli,
                        email,
                        poin: 0,
                    };

                    const registerResponse = await fetch("http://127.0.0.1:8000/api/pembeli/register", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(data),
                    });

                    if (!registerResponse.ok) {
                        const errorData = await registerResponse.json();
                        throw errorData;
                    }

                    const registerResult = await registerResponse.json();
                    Toastify({
                        text: "Registrasi berhasil! Silahkan melakukan login",
                        style: {
                            background: "rgb(95, 211, 99)"
                        }
                    }).showToast();
                    window.location.href = 'http://127.0.0.1:8000/UsersLogin';
                }catch(error){
                    console.log(error);
                    Toastify({
                        text: "Terjadi kesalahan saat registrasi. Silakan coba lagi.",
                        style: {
                            background: "rgb(214, 10, 10)"
                        }
                    }).showToast();
                }
            })
        })
    </script>

</body>
</html>
