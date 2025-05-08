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
<body>
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
        document.addEventListener("DOMContentLoaded", () => {
            const registerButton = document.querySelector('.register-button button');
            registerButton.addEventListener('click', async function (e) {
                e.preventDefault();
                try{
                    const namaOrganisasi = document.getElementById('namaOrganisasi').value.trim();
                    const alamat = document.getElementById('alamat').value.trim();
                    const email = document.getElementById('email').value.trim();
                    const username = document.getElementById('username').value.trim();
                    const password = document.getElementById('password').value.trim();

                    if (!namaOrganisasi || !alamat || !email || !username || !password) {
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
                            backgroundColor: "rgb(214, 10, 10)", // Red gradient for error
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
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "rgb(95, 211, 99)",
                    }).showToast();

                    // setTimeout(() => {
                    //     window.location.href = "{{ url('/Login') }}";
                    // }, 3000);
                }catch(error){
                    console.log(error);
                    Toastify({
                        text: "Terjadi kesalahan saat registrasi. Silakan coba lagi.",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "rgb(214, 10, 10)",
                    }).showToast();

                }
            })
        })
    </script>

</body>
</html>
