<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-dark text-white p-4">
        <div class="container text-center">
            <h1>Product Detail</h1>
        </div>
    </header>

    <div class="container my-5" id="product-detail">
        <!-- Product details will be dynamically loaded here -->
    </div>

    <footer class="bg-dark text-white text-center p-3">
        <p>&copy; 2025 Reusemart</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get the product ID from the URL query parameter
            const pathSegments = window.location.pathname.split('/');
            const productId = pathSegments[pathSegments.length - 1]; 

            // Fetch the product data from the API
            fetch(`http://127.0.0.1:8000/api/getBarang/${productId}`)
                .then(response => response.json())
                .then(product => {
                    // Get the product detail container
                    const productDetailContainer = document.getElementById('product-detail');

                    // Check if the product was returned successfully
                   
                        // Format the product price
                        const formattedPrice = Number(product.hargaBarang).toLocaleString('id-ID');

                        // Create the product detail HTML
                        const productDetailHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="{{ asset('${product.image}') }}" class="img-fluid" alt="${product.namaBarang}">
                                </div>
                                <div class="col-md-6">
                                    <h2>${product.namaBarang}</h2>
                                    // <p class="text-muted">Made by ${product.maker}</p>
                                    <p class="text-success">Rp. ${formattedPrice}</p>
                                    // <p>${product.description}</p>

                                    <!-- Add to Cart Button -->
                                    <button class="btn btn-dark">Add to Cart</button>
                                </div>
                            </div>
                        `;

                        // Append the product details to the container
                        productDetailContainer.innerHTML = productDetailHTML;

                        // Handle product not found
                        // productDetailContainer.innerHTML = `<p>Product not found.</p>`;
                    
                })
                .catch(error => {
                    console.error('Error fetching product details:', error);
                    document.getElementById('product-detail').innerHTML = `<p>Error loading product details.</p>`;
                });
        });
    </script>
</body>
</html>
