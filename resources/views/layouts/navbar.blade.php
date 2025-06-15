<style>
    /* .navbar-nav .nav-item .nav-link:hover {
        background-color: #6c757d; 
        color: white !important;  
    } */

    .badge-cart {
        font-size: 0.7rem;
        position: absolute;
        top: 25px !important;
        right: 100px !important;
    }
</style>


<nav class="navbar navbar-expand-lg navbar-light bg-light  shadow-sm">
    <div class="container-fluid">

        <!-- Logo -->
            <a class="navbar-brand" id="logoLink" href="{{ url('/') }}">
                <img src="{{ asset('logoReUseMart.png') }}" alt="Logo Reusemart" style="width:50px;">
            </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Bagian kiri navbar -->
            <ul class="navbar-nav me-auto" id="left-menu">
                <li class="nav-item" id="produkMenu">
                    <a class="nav-link text-black" href="{{ url('/home') }}">Produk</a>
                </li>
            </ul>
            <!-- Bagian kanan navbar -->
            <ul class="navbar-nav ms-auto" id="right-menu">
                <li class="nav-item" id="loginMenu">
                    <a class="nav-link text-black" href="{{ url('/UsersLogin') }}">Masuk</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // updateCartBadge()

        const token = localStorage.getItem("auth_token");
        const role = localStorage.getItem("user_role");
        const leftMenu = document.getElementById("left-menu");
        const rightMenu = document.getElementById("right-menu");
        const logoLink = document.getElementById("logoLink");
        const loginMenu = document.getElementById("loginMenu");
        const produkMenu = document.getElementById("produkMenu");


        // Fungsi buat item menu
        const createItem = (href, text, isIcon = false) => {
            const li = document.createElement("li");
            li.className = "nav-item";
            const link = document.createElement("a");
            link.className = "nav-link text-black";
            link.href = href;

            if (isIcon) {
                // const cartCount = localStorage.getItem("cart_count") || 0;
                link.innerHTML = `
                    <i class="bi bi-cart3" style="font-size: 25px;"></i>
                    `;
                    link.title = text;
                } else {
                    link.textContent = text;
                }
                
                // <span class="position-absolute translate-middle badge rounded-pill bg-danger badge-cart" id="cart-count-badge">
                //     ${cartCount}
                // </span>
            li.appendChild(link);
            return li;
        };

        // Fungsi Logout
        const createLogout = () => {
            const li = document.createElement("li");
            li.className = "nav-item d-flex align-items-center";

            const form = document.createElement("form");
            form.className = "d-flex";

            const button = document.createElement("button");
            button.className = "btn btn-outline-danger";
            button.type = "submit";
            button.textContent = "Log Out";

            // Tambahkan event handler logout
            form.addEventListener("submit", function (e) {
                e.preventDefault();
                localStorage.clear();
                window.location.href = "/";
            });

            form.appendChild(button);
            li.appendChild(form);
            return li;
        };

        // function updateCartBadge() {
        //     const token = localStorage.getItem('auth_token');
        //     if (!token) {
        //         // Kalau tidak login, badge di-reset
        //         localStorage.setItem('cart_count', 0);
        //         const badge = document.getElementById("cart-count-badge");
        //         if (badge) badge.style.display = 'none';
        //         return;
        //     }

        //     fetch('http://127.0.0.1:8000/api/keranjang', {
        //         method: 'GET',
        //         headers: {
        //             'Authorization': `Bearer ${token}`,
        //             'Accept': 'application/json'
        //         }
        //     })
        //     .then(res => res.json())
        //     .then(data => {
        //         const count = data.count || 0;
        //         localStorage.setItem('cart_count', count);

        //         const badge = document.getElementById("cart-count-badge");
        //          if (!badge) return;

        //         if (count > 0) {
        //             badge.textContent = count;
        //             badge.style.display = 'inline';  // tampilkan badge
        //         } else {
        //             badge.style.display = 'none';   // sembunyikan badge
        //         }
        //     })
        //     .catch(err => {
        //         console.error('Gagal update cart badge:', err);
        //     });
        // };

        if (token && role) {
            // if (loginMenu){
                loginMenu.remove(); // sembunyikan tombol masuk
                produkMenu.remove();
            // }     

            // Default logo: disable link jika bukan pembeli
            if (role !== "pembeli") logoLink.removeAttribute("href");

            // Isi menu berdasarkan role
            if (role === "pembeli") {
                leftMenu.appendChild(createItem("/pembeli/dashboard", "Home"));
                leftMenu.appendChild(createItem("/home", "Produk"));
                leftMenu.appendChild(createItem("/pembeli/alamat", "Alamat"));

                const cartItem = createItem("/keranjang", "Keranjang", true);
                cartItem.style.marginRight = "15px";  // jarak kanan
                rightMenu.appendChild(cartItem);

                rightMenu.appendChild(createLogout());          
            }else if (role === "penitip") {
                leftMenu.appendChild(createItem("/penitip/dashboard", "Home"));
                leftMenu.appendChild(createItem("/penitip/history", "History Penitip"));
                leftMenu.appendChild(createItem("/penitip/profile", "Profile"));
                rightMenu.appendChild(createLogout());            
            }else if (role === "organisasi") {
                leftMenu.appendChild(createItem("/OrganisasiMain", "Request Donasi"));
                leftMenu.appendChild(createItem("/organisasi/history-request", "History Request Donasi"));
                rightMenu.appendChild(createLogout());
            }else if(role === "admin"){
                leftMenu.appendChild(createItem("/pegawaidata", "Profile"));
                leftMenu.appendChild(createItem("/organisasi", "Organisasi"));
                leftMenu.appendChild(createItem("/pegawaiView", "Pegawai"));
                rightMenu.appendChild(createLogout());
            }else if(role==="cs"){
                leftMenu.appendChild(createItem("/home", "Produk"));
                leftMenu.appendChild(createItem("/pegawaidata", "Profile"));
                leftMenu.appendChild(createItem("/pegawai/PenitipData", "Data Penitip"));
                leftMenu.appendChild(createItem("/verifikasi", "Verifikasi Pembayaran"));
                rightMenu.appendChild(createLogout());            

            }else if (role==="gudang"){
                leftMenu.appendChild(createItem("/pegawaidata", "Profile"));
                leftMenu.appendChild(createItem("/pegawai/gudangview", "View Gudang"));
                leftMenu.appendChild(createItem("/pegawai/penjadwalan", "Penjadwalan Barang"));
                rightMenu.appendChild(createLogout());   
            }else if (role==="owner"){
                leftMenu.appendChild(createItem("/pegawaidata", "Profile"));
                leftMenu.appendChild(createItem("/requestDonasi", "Request Donasi"));
                leftMenu.appendChild(createItem("/donasi", "Donasi"));
                leftMenu.appendChild(createItem("/laporanPenitip", "Penitip"));
                rightMenu.appendChild(createLogout());   
            }
        }
    });
</script>
