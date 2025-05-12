<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <!-- Video Background -->
    <div class="position-relative" style="height: 100vh;">
        <video autoplay muted loop id="video-bg">
            <source src="{{ asset('storage/essentials/ReUseMartVid.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- Overlay for readability, with adjusted opacity -->
        <div class="position-absolute w-100 h-100 bg-dark" style="opacity: 0.4; z-index: -1;"></div>

        <!-- Login Form -->
        <div class="container d-flex justify-content-center align-items-center h-100">
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
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to handle the form submission -->
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
            const response = await fetch(`http://127.0.0.1:8000/api/pegawai/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(data)
            });

            const resData = await response.json();
            
            if (!response.ok) {
                // Jika gagal login (401, 404, dsb)
                alert(resData.message || 'Login failed');
                return;
            }
            alert('Login successful!');
            console.log('User:', resData.penitip);

            localStorage.setItem('auth_token', resData.penitip.Token);

        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred: ' + error.message);
        }
    });
});
</script>


</body>
</html>
