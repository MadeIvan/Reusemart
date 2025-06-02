<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Toastify ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
</head>

<style>
    .diskusi-tanggal-pembeli{
        display: flex;
        justify-content: left;
        gap: 10px;
    }

    .diskusi-tanggal-pegawai{
        display: flex;
        justify-content: right;
        gap: 10px;
    }
     .image-container {
    position: relative;
    display: inline-block;
    cursor: pointer;
  }

  .image-container img {
    display: block;
    max-width: 100%;
    height: auto;
    border-radius: 8px;
  }

  .star-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    color: #ffc107;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 8px;
  }

  .image-container:hover .star-overlay {
    opacity: 1;
  }

  .star-overlay span {
    font-size: 2rem;
    cursor: pointer;
    transition: color 0.2s;
  }

  .star-overlay span:hover,
  .star-overlay span.hovered,
  .star-overlay span.selected {
    color: #ffd633;
  }
    /* .diskusi-pegawai{
        display: flex;
        justify-content: left;
        gap: 10px;
    } */
</style>

<body>
    @include('layouts.navbar')

    <header class="bg-dark text-white p-4">
        <div class="container text-center">
            <h1>Product Detail</h1>
        </div>
    </header>

    <div class="container my-5" id="product-detail">
        <!-- Product details will be dynamically loaded here -->
    </div>

    <div class="container my-3" id="diskusi-container">
        <!-- Diskusi produk akan muncul di sini -->
    </div>

    <div class="container my-3 kirim-diskusi">
        <div class="col-md-8 mt-3">
            <textarea class="form-control " id="exampleFormControlTextarea1" rows="2" placeholder="Tulis Komentar"></textarea>
        </div>
        <div class="d-flex justify-content-end col-md-8 mt-3">
            <button class="btn btn-dark" id="kirimBtn">Kirim</button>
        </div>
    </div>

    

    <footer class="bg-dark text-white text-center p-3">
        <p>&copy; 2025 Reusemart</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

   <script>
document.addEventListener("DOMContentLoaded", function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag
    const token = localStorage.getItem('auth_token');
    const pathSegments = window.location.pathname.split('/');
    const productId = pathSegments[pathSegments.length - 1]; 
    // document.querySelector('.btn.btn-dark').addEventListener('click', kirimDiskusi);

    // --- START of updated code ---
    // Fetch product data and dynamically get idPenitip and average rating
    fetch(`http://127.0.0.1:8000/api/getBarang/${productId}`)
        .then(response => response.json())
        .then(product => {
            const formattedPrice = Number(product.hargaBarang).toLocaleString('id-ID');

            // Step 1: Get idPenitip from barang/simple API
            fetch(`http://127.0.0.1:8000/api/barang/simple/${product.idBarang}`)
            .then(simpleRes => simpleRes.json())
            .then(simpleData => {
                const idPenitip = simpleData.idPenitip || null;

                if (!idPenitip) {
                    console.warn("idPenitip not found, showing stars as 0");
                    renderProductWithStars(product, formattedPrice, 0);
                    return;
                }

                // Step 2: Get average rating by idPenitip
                fetch(`http://127.0.0.1:8000/api/rating/average/${idPenitip}`)
                .then(avgRes => avgRes.json())
                .then(avgData => {
                    let avgRating = 0;
                   
                        avgRating = Number(avgData.averageRating);

                    
                    console.log(avgData.averageRating);
                    console.log("fin ", avgRating);
                    renderProductWithStars(product, formattedPrice, avgRating);
                    highlightStars(avgRating);
                })
                .catch(err => {
                    console.error("Error fetching average rating:", err);
                    renderProductWithStars(product, formattedPrice, 0);
                });
            })
            .catch(err => {
                console.error("Error fetching simple barang data:", err);
                renderProductWithStars(product, formattedPrice, 0);
            });
        })
        .catch(error => {
            console.error('Error fetching product details:', error);
            document.getElementById('product-detail').innerHTML = `<p>Error loading product details.</p>`;
        });

    // Helper to render product + stars overlay and setup event listeners
    function renderProductWithStars(product, formattedPrice, avgRating) {
        const productDetailContainer = document.getElementById('product-detail');

        const productDetailHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="image-container">
                    <img src="{{ asset('${product.image}.jpg') }}" class="img-fluid" alt="${product.namaBarang}">
                    <div class="star-overlay" id="starOverlay">

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h2>${product.namaBarang}</h2>    
                <p class="text-success">Rp. ${formattedPrice}</p>

                <div class="d-flex align-items-center gap-2">
                    <!-- Small circle avatar -->
                    <img src="path_to_avatar_image.jpg" alt="Avatar" 
                        style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">

                    <!-- Add to Cart Button -->
                    <button type="button" class="btn btn-dark add-to-cart-btn" data-id="${product.idBarang}">
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
        `;


        productDetailContainer.innerHTML = productDetailHTML;
        const starOverlayDiv = document.getElementById('starOverlay'); // Replace with your actual average rating (rounded integer)
        starOverlayDiv.innerHTML = generateStars(avgRating);
        highlightStars(Math.floor(avgRating));

        const stars = productDetailContainer.querySelectorAll('.star-overlay span');
        stars.forEach(star => {
            star.addEventListener('mouseover', () => {
                highlightStars(parseInt(star.dataset.value));
            });
            star.addEventListener('mouseout', () => {
                highlightStars(Math.floor(avgRating));
            });
        });
        function generateStars(avgRating) {
    let starsHTML = '';
    const maxStars = 5;
    for (let i = 1; i <= maxStars; i++) {
        if (i <= avgRating) {
            starsHTML += `<span data-value="${i}" style="color: #ffc107;">&#9733;</span>`; // highlighted star
        } else {
            starsHTML += `<span data-value="${i}" style="color: #ccc;">&#9733;</span>`; // grey star
        }
    }
    return starsHTML;
}

        const addToCartBtn = productDetailContainer.querySelector('.add-to-cart-btn');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function(event) {
                event.preventDefault();
                const idBarang = this.getAttribute('data-id');
                addToCart(idBarang);
            });
        }
    }

    // Star highlight function (you already have this but redeclared here to ensure correct scope)
    function highlightStars(rating) {
        const stars = document.querySelectorAll('.star-overlay span');
        stars.forEach(star => {
            star.classList.toggle('selected', parseInt(star.dataset.value) <= rating);
        });
    }

    // --- END of updated code ---

    // Your existing functions below (getDiskusi, kirimDiskusi, addToCart, etc) remain untouched
    // ...
});
</script>

</body>
</html>