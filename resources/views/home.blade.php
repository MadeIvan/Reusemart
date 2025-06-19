<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reusemart</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <!-- Toastify ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body">
        
    @include('layouts.navbar')
    <div class="container-fluid" style = "margin-top:7% !important; margin-left: 5% !important;" >
 <!-- Header Section -->

    <!-- Product List Section -->
    <!-- <section class="container my-5" id="product-list"> -->
        <div class="row" id="product-list">
            <!-- Products will be dynamically loaded here -->
        </div>
    <!-- </section> -->

    </div>
   
    <!-- Footer Section -->
    <footer class="bg-dark text-white text-center p-3">
        <p>&copy; 2025 Reusemart</p>
    </footer>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybK5Zt9seR4Dd4VuK9ckb9F9c7B66tL8fQ1Qu4u6E9f4W/p7jm" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>    
    
    <!-- Custom JavaScript to fetch and display products -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Fetch products from the API
            fetch('http://127.0.0.1:8000/api/getBarang')  // Add '/api' to match your Postman route
                .then(response => response.json())
                .then(data => {
                    // Get the product list container
                    const productList = document.getElementById('product-list');

                    // Loop through each product and create a card
                    data.forEach(product => {
                        const formattedPrice = Number(product.hargaBarang).toLocaleString('id-ID');
                        const productCard = `
                            <div class="col-md-3 mb-4"> <!-- 4 cards per row on medium screens -->
                                <div class="card">
                                    <img src="{{ asset('${product.image}.jpg') }}" class="card-img-top" alt="${product.namaBarang}" />
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div>
                                            <h5 class="card-title">${product.namaBarang}</h5>
                                            <p class="card-text text-success">Rp. ${formattedPrice}</p>
                                        </div>
                                        <a href="/getBarang/${product.idBarang}" class="btn btn-dark align-self-end">Lihat Barang</a>

                                    </div>
                                </div>
                            </div>
                        `;
                        productList.innerHTML += productCard;
                    });
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                });
        });
    </script>
</body>
</html>
