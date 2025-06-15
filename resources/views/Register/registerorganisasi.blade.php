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
                            <a class="nav-link active text-black" >
                                <!-- <strong>Home</strong> -->
                                <img src="{{ asset('logoReUseMart.png') }}" alt="Logo Reusemart" style="width:50px;">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>



        <!-- Overlay for readability, with adjusted opacity -->
        <!-- Main Content -->
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <!-- Toast notification -->
        <div class="toast-container">
            <div class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true" id="successToast">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage">
                        Action completed successfully!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div> 
        <div class="row w-100 justify-content-center">
            
            <div class="col-12 col-md-6 col-lg-4 mx-auto bg-light p-4 rounded-3 shadow" style="z-index: 1;">
            <h3 class="text-center mb-4">Registrasi Organisasi</h3>
                <form id="registerForm">
                    <div class="mb-3">
                        <label for="namaOrganisasi" class="form-label">Nama Organisasi</label>
                        <input type="text" class="form-control" id="namaOrganisasi" placeholder="Enter name" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="alamat" placeholder="Enter address" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter email" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" placeholder="Enter username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" placeholder="Enter password" required>
                    </div>
                    <div class="d-flex justify-content-center register-button">
                        <button type="submit" class="btn btn-primary w-100">Submit</button>
                    </div>
                </form>
                <p class="text-center mt-3">
                    Already have an account for organization?
                    <a href="/UsersLogin" class="text-decoration-none">Click here!</a>
                </p>
            </div>
        </div>
    </div>
    
<script>
    document.addEventListener("DOMContentLoaded", () => {
        function showToast(message, bgColor = 'bg-primary') {
            const toast = document.getElementById('successToast');
            const toastMessage = document.getElementById('toastMessage');
            
            // Set message
            toastMessage.textContent = message;
            
            // Update background color
            toast.className = `toast align-items-center text-white border-0 ${bgColor}`;
            
            // Show toast using Bootstrap's toast API
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }

        const registerForm = document.getElementById('registerForm');
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();  // Prevent the default form submission

            const namaOrganisasi = document.getElementById('namaOrganisasi').value.trim();
            const alamat = document.getElementById('alamat').value.trim();
            const email = document.getElementById('email').value.trim();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            // Check if all fields are filled
            if (!namaOrganisasi || !alamat || !email || !username || !password) {
                showToast("Mohon untuk mengisi seluruh form", "bg-danger");
                return;
            }

            // Check if email and username already exist
            try {
                const checkResponse = await fetch(`http://127.0.0.1:8000/api/organisasi/check-email-username?email=${encodeURIComponent(email)}&username=${encodeURIComponent(username)}`, {
                    method: "GET",
                });

                if (!checkResponse.ok) {
                    const errorData = await checkResponse.json();
                    console.error('Error in checking email/username:', errorData);
                    throw errorData;  // Error handling if check fails
                }

                const checkResult = await checkResponse.json();

                // Display toast if email or username already exists
                if (checkResult.emailExists) {
                    showToast("Email telah digunakan. Mohon untuk menggunakan Email lain.", "bg-danger");
                    return;
                }

                if (checkResult.usernameExists) {
                    showToast("Username telah digunakan. Mohon untuk menggunakan username lain.", "bg-danger");
                    return;
                }

                // Send the registration data
                const data = {
                    namaOrganisasi,
                    alamat,
                    email,
                    username,
                    password,
                };

                const registerResponse = await fetch("http://127.0.0.1:8000/api/organisasi/register", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify(data),
                });

                if (!registerResponse.ok) {
                    const errorData = await registerResponse.json();
                    console.error('Error in registration:', errorData);
                    throw errorData;
                }

                const registerResult = await registerResponse.json();
                console.log('Registration successful:', registerResult);
                showToast("Organisasi Berhasil teregister!", "bg-success");

                setTimeout(() => {
                    window.location.href = "{{ url('/UsersLogin') }}"; // Redirect to login page after 3 seconds
                }, 3000);

            } catch (error) {
                console.error('Failed to register:', error);
                showToast(`Gagal mendaftar: ${error.message}`, "bg-danger");  // Show error toast
            }
        });
    });
</script>
</body>
</html>