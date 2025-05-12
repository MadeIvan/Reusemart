<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Register Organisasi</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <style>
        .register-button button:hover {
            background-color: #006666;
        }
    </style>

</head>
<body>
    
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
            
            <div class="col-md-6 border p-4 rounded shadow">
            <h2 class="text-center" style="color:rgb(24, 134, 4); font-weight: bold;">Registrasi Organisasi</h2>
                <form id="registerForm">
                    <div class="mb-3">
                        <label for="namaOrganisasi" class="form-label"><strong>Nama Organisasi</strong></label>
                        <input type="text" class="form-control" id="namaOrganisasi" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label"><strong>Alamat</strong></label>
                        <input type="text" class="form-control" id="alamat" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label"><strong>Email</strong></label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label"><strong>Username</strong></label>
                        <input type="text" class="form-control" id="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label"><strong>Password</strong></label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
                    <div class="d-flex justify-content-center register-button">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
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
                    const checkResponse = await fetch(`http://127.0.0.1:8000/api/check-email-username?email=${encodeURIComponent(email)}&username=${encodeURIComponent(username)}`, {
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
