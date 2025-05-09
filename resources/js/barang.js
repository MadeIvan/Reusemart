document.addEventListener("DOMContentLoaded", function() {
    // Fetch products from the API with the correct URL
    fetch('http://127.0.0.1:8000/api/getBarang')  // Add '/api' to match your Postman route
        .then(response => response.json())
        .then(data => {
            // Get the product list container
            const productList = document.getElementById('product-list');

            // Loop through each product and create a card
            data.forEach(product => {
                const productCard = `
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="${product.image}" class="card-img-top" alt="${product.namaBarang}" />
                            <div class="card-body text-center">
                                <h5 class="card-title">${product.namaBarang}</h5>
                                <p class="card-text text-warning">${product.hargaBarang}</p>
                                <a href="#" class="btn btn-dark">View More</a>
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
