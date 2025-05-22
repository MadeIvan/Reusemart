<nav class="navbar navbar-expand-lg navbar-light shadow-sm fixed-top" style="background-color: rgba(255, 255, 255, 0.0); backdrop-filter: blur(5px);">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Nav-bar kiri -->
            <ul class="navbar-nav me-auto" id="left-menu">
                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link active text-black" id="logoLink" href="{{url('/')}}" >
                        <img src="{{ asset('logoReUseMart.png') }}" alt="Logo Reusemart" style="width:50px;">
                    </a>
                </li>
                <li class="nav-item d-flex align-items-center" id="produkMenu">
                    <a class="nav-link text-white" href="{{url('/home')}}">
                        Produk
                    </a>
                </li>
            </ul>

            <!-- Nav-bar kanan -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 profile-menu" id="right-menu"> 
                <li class="nav-item d-flex align-items-center" id="loginMenu">
                    <a class="nav-link text-white" href="{{url('/UsersLogin')}}">
                        Masuk
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function () {
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
            li.className = "nav-item d-flex align-items-center";
            const link = document.createElement("a");
            link.className = "nav-link text-white";
            link.href = href;

            if (isIcon) {
                // Misal pake Font Awesome icon keranjang
                link.innerHTML = '<i class="bi bi-cart3" style="font-size: 25px;"></i>';
                link.title = text;
            } else {
                link.textContent = text;
            }

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


        // Jika user sudah login
        if (token && role) {
            // Sembunyikan menu login dan produk default
            if (loginMenu) loginMenu.remove();
            if (produkMenu) produkMenu.remove();

            // Disable logoLink jika bukan pembeli
            if (role !== "pembeli" && logoLink) logoLink.removeAttribute("href");

            // Tambah menu sesuai role
            if (role === "pembeli") {
                leftMenu.appendChild(createItem("/penitip/dashboard", "Home"));
                leftMenu.appendChild(createItem("/home", "Produk"));
                leftMenu.appendChild(createItem("/pembeli/alamat", "Alamat"));

                const cartItem = createItem("/keranjang", "Keranjang", true);
                cartItem.style.marginRight = "15px";  // kasih jarak kanan
                rightMenu.appendChild(cartItem);
                
                rightMenu.appendChild(createLogout());
            }
        }
    });
</script>
