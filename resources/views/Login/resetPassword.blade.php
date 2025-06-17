<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reusemart</title>
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

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh; padding-top: 100px;">
        <div class="row w-100">
            <div class="col-12 col-md-6 col-lg-4 mx-auto bg-light p-4 rounded-3 shadow" style="z-index: 1;">
                <h3 class="text-center ">Reset Password</h3>
                <p> Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one. </p>
                <form id="forgotPasswordForm">                    
                     <div class="mb-3">
                            <input type="email" class="form-control" id="email" placeholder="Masukkan Email" required>
                    </div>

                    <div class="mb-3">
                            <select class="form-select" id="role" required>
                                <option value="" disabled selected>Select Status</option>
                                <option value="penitip">Penitip</option>
                                <option value="pembeli">Pembeli</option>
                                <option value="organisasi">Organisasi</option>
                            </select>
                        </div>
                    <button type="submit"class="btn btn-primary w-100">Kirim Link Reset</button>
                </form>
            </div>
        </div>          
    </div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const role = document.getElementById('role').value;

        const response = await fetch('http://127.0.0.1:8000/api/forgot-password', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json' ,
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({ email, role })
        });

        const result = await response.json();
        alert(result.message);
    });
});

</script>





</body>
</html>