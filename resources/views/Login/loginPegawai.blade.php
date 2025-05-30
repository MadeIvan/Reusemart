<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Pegawai</title> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden; /* Prevent scrolling if video is larger than viewport */
        }

        #video-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        /* Minimal custom CSS for hover effect */
        .login-button button:hover {
            background-color: #006666; /* Your original hover color */
        }

        /* Specific text color for the heading */
        .custom-heading-color {
            color: rgb(24, 134, 4) !important; /* Force this specific green */
            font-weight: bold;
        }

        /* Style for the login card background */
        .login-form-bg {
            background-color: rgba(255, 255, 255, 0.9); /* Corrected transparency, was missing alpha value */
        }

        .toggle-password {
            cursor: pointer;
            user-select: none;
        }
    </style>
</head>
<body>
    @include('layouts.navbarVideo')

    <video autoplay muted loop id="video-bg">
        <source src="{{ asset('ReUseMartVid.mp4') }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <div class="d-flex justify-content-center align-items-center vh-100 position-relative" style="z-index: 1;">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4 login-form-bg p-4 rounded shadow">
            <h2 class="text-center custom-heading-color">Login Pegawai</h2>
            <form>
                <div class="mb-3">
                    <label for="username" class="form-label"><strong>Username</strong></label>
                    <input type="text" class="form-control" id="username" autocomplete="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><strong>Password</strong></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" autocomplete="current-password">
                        <button class="btn btn-outline-secondary toggle-password" type="button" id="togglePassword">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="d-grid gap-2 login-button">
                    <button type="button" class="btn btn-success" id="loginButton">Login</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const passwordField = document.getElementById('password');
    const togglePasswordButton = document.getElementById('togglePassword');

    if (togglePasswordButton && passwordField) {
        togglePasswordButton.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }

    document.getElementById('loginButton').addEventListener('click', function () {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        if (!username || !password) {
            Toastify({
                text: "Mohon untuk mengisi username dan password.",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "rgb(214, 10, 10)",
            }).showToast();
            return; 
        }

        fetch("http://127.0.0.1:8000/api/pegawai/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken // Remove if using API routes without CSRF
            },
            body: JSON.stringify({ username, password }),
        })
        .then(response => {
            console.log('Response Status:', response.status);
            if (!response.ok) {
                if (response.status === 403) {
                    return response.json().then(errData => {
                        throw new Error(errData.message || 'Akun dinonaktifkan.');
                    });
                } else if (response.status === 401) {
                    throw new Error('Invalid username or password.');
                } else if (response.status === 404) {
                    throw new Error('Login service not found. Please check the API endpoint.');
                } else {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            }
            return response.json();
        })
        .then(data => {
            console.log('Response Data:', data);

            if (data.data && data.data.token && data.data.pegawai) {
                localStorage.setItem('token', data.data.token);

                const jabatan = data.data.pegawai.idJabatan;
                let userRole = "Unknown";

                switch (jabatan) {
                    case "1":
                        userRole = "Owner";
                        break;
                    case "2":
                        userRole = "Admin";
                        break;
                    case "3":
                        userRole = "Gudang";
                        break;
                    case "4":
                        userRole = "Kurir";
                        break;
                    case "5":
                        userRole = "CS";
                        break;
                    case "6":
                        userRole = "Hunter";
                        break;
                }

                localStorage.setItem('user_role', userRole);
                localStorage.setItem('pegawaiData', JSON.stringify(data.data.pegawai));

                Toastify({
                    text: "Login berhasil! Role: " + userRole,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#8bc34a",
                }).showToast();

                // Redirect all users to /pegawaidata
                setTimeout(() => {
                    window.location.href = "/pegawaidata";
                }, 2000);

            } else {
                Toastify({
                    text: "Login gagal. Data tidak lengkap.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "rgb(221, 25, 25)",
                }).showToast();
            }
        })
        .catch(error => {
            console.error('Error:', error.message);
            Toastify({
                text: `Terjadi kesalahan saat login: ${error.message}`,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "rgb(221, 25, 25)",
            }).showToast();
        });
    });
});
</script>

</body>
</html>