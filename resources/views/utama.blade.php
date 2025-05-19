<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reusemart</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- Toastify ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<style>
    video{
        position: absolute;
        top: 0;
        left: 0;
        min-width: 100%;
        min-height: 100%;
        object-fit: cover;
        z-index: -1;
    }

    .content-wrapper {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center; /* Tengah vertikal */
        align-items: center;     /* Tengah horizontal */
        text-align: center;
        padding: 30px;
        text-shadow: 1px 1px 4px rgba(0,0,0,0.8);
    }

</style>

<body>
    @include('layouts.navbarVideo')
    <!-- //////////////////ini background video///////////////////// -->
    <div class="position-relative" style="height: 100vh; overflow: hidden;">
        <video autoplay muted loop id="video-bg">
            <source src="{{ asset('ReUseMartVid.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        
        <div class="content-wrapper text-center">
            <h1>Selamat Datang di Reusemart</h1>
            <p>
               nanti isinya ttg reusemart (?)
            </p>
            </div>
        </div>
    </div>

    <!-- ////////////////////////////ini isinya/////////////// -->

    

    <!-- Footer Section -->
    <footer class="bg-dark text-white text-center p-3">
        <p>&copy; 2025 Reusemart</p>
    </footer>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybK5Zt9seR4Dd4VuK9ckb9F9c7B66tL8fQ1Qu4u6E9f4W/p7jm" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>    
    
    <!-- Custom JavaScript to fetch and display products -->
    <script>
     
    </script>
</body>
</html>
