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
        <h2 class="text-center" style="color:rgb(24, 134, 4); font-weight: bold;">Registrasi Penitip</h2>
            <form>
                <div class="mb-3">
                    <label for="namaPenitip" class="form-label"><strong>Nama Penitip</strong></label>
                    <input type="text" class="form-control" id="namaPenitip">
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label"><strong>Username</strong></label>
                    <input type="text" class="form-control" id="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><strong>Password</strong></label>
                    <input type="password" class="form-control" id="password">
                </div>
                <div class="mb-3">
                    <label for="nik" class="form-label"><strong>NIK</strong></label>
                    <input type="text" class="form-control" id="nik">
                </div>
                <div class="d-flex justify-content-center register-button">
                    <button type="submit" class="btn btn-success item-center">Registrasi</button>
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
                    const namaPenitip = document.getElementById('namaPenitip').value.trim();
                    const username = document.getElementById('username').value.trim();
                    const password = document.getElementById('password').value.trim();
                    const nik = document.getElementById('nik').value.trim();

                    if (!namaPenitip || !username || !password ||!nik) {
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

                    const checkResponse = await fetch(`http://127.0.0.1:8000/api/check-nik?nik=${encodeURIComponent(nik)}`, {
                        method: "GET",
                    })

                    if (!checkResponse.ok) {
                        const errorData = await checkResponse.json();
                        throw errorData;
                    }

                    const checkResult = await checkResponse.json();
                    if (checkResult.nikExists) {
                        Toastify({
                            text: "nik telah digunakan. Mohon untuk menggunakan nik lain.",
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
                        namaPenitip,
                        nik,
                    };

                    const registerResponse = await fetch("http://127.0.0.1:8000/api/penitip/register", {
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

                    setTimeout(() => {
                        window.location.href = "{{ url('/penitip/login') }}";
                    }, 3000);
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
