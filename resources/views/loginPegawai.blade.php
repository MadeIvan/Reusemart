<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <!-- Main Content -->
    <div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 border p-4 rounded shadow">
        <h2 class="text-center" style="color:rgb(24, 134, 4); font-weight: bold;">Login Pegawai</h2>
            <form>
                <div class="mb-3">
                    <label for="username" class="form-label"><strong>Username</strong></label>
                    <input type="text" class="form-control" id="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><strong>Password</strong></label>
                    <input type="password" class="form-control" id="password">
                </div>

                <div class="d-flex justify-content-center login-button">
                    <button type="submit" class="btn btn-success item-center" id="loginButton">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script>
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
            return; // Stop proses jika input kosong
        }

        fetch("http://127.0.0.1:8000/api/pegawai/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ username, password }),
        })
        .then(response => {
            console.log('Response Status:', response.status);
            if (!response.ok) {
                throw new Error('Invalid username or password');
            }
            return response.json();
        })
        .then(data => {
            console.log('Response Data:', data);

            if (data.data.token) {
                localStorage.setItem('token', data.data.token);

                Toastify({
                    text: "Login berhasil!",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#8bc34a",
                }).showToast();

                // Uncomment jika ingin redirect setelah login
                // setTimeout(() => {
                //     window.location.href = "/HomeSetelahLogin"; // Ubah URL sesuai kebutuhan
                // }, 2000);
            } else {
                Toastify({
                    text: "Username atau password salah.",
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
                text: "Terjadi kesalahan saat login. Silakan coba lagi.",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "rgb(221, 25, 25)",
            }).showToast();
        });
    });

    </script>

</body>
</html>
