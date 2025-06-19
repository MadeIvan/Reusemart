
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Top Navigation Bar */
        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 1001;
            display: flex;
            align-items: center;
            padding: 0 20px ;
            /* margin : 0 20px */
            gap: 20px;
        }

        .logo-container {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #1a5c1a;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .logo-container img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .search-container {
            flex: 1;
            max-width: 600px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 12px 50px 12px 20px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #1a5c1a;
            box-shadow: 0 0 0 3px rgba(26, 92, 26, 0.1);
        }

        .search-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            font-size: 18px;
            cursor: pointer;
            padding: 5px;
        }

        .search-btn:hover {
            color: #1a5c1a;
        }

        .profile-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: auto;
        }

        .profile-btn {
            padding: 8px 20px;
            border: 2px solid #1a5c1a;
            background-color: transparent;
            color: #1a5c1a;
            border-radius: 20px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-btn:hover {
            background-color: #1a5c1a;
            color: white;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            height: calc(100vh - 70px);
            width: 70px;
            background-color: white;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar:hover {
            width: 250px;
        }

        .sidebar-menu {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .sidebar-menu li {
            border-bottom: 1px solid #f8f9fa;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px;
            color: #1a5c1a;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            justify-content: center;
            white-space: nowrap;
        }

        .sidebar:hover .sidebar-menu a {
            justify-content: flex-start;
            padding: 15px 20px;
        }

        .sidebar-menu a:hover {
            background-color: #f8f9fa;
            color: #0d4a0d;
        }

        .sidebar-menu a.active {
            background-color: #1a5c1a;
            color: white;
        }

        .sidebar-menu i {
            font-size: 20px;
            width: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .sidebar:hover .sidebar-menu i {
            margin-right: 12px;
            font-size: 18px;
        }

        .sidebar-menu span {
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
            position: absolute;
            left: 60px;
            pointer-events: none;
        }

        .sidebar:hover .sidebar-menu span {
            opacity: 1;
            transform: translateX(0);
            position: static;
            pointer-events: auto;
        }

        .logout-container {
            padding: 15px;
            margin-top: auto;
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .logout-btn {
            background-color: transparent;
            border: 2px solid #dc3545;
            color: #dc3545;
            padding: 12px;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-weight: 500;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar:hover .logout-btn {
            justify-content: flex-start;
        }

        .logout-btn:hover {
            background-color: #dc3545;
            color: white;
        }

        .logout-btn i {
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .logout-btn span {
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
            margin-left: 8px;
        }

        .sidebar:hover .logout-btn span {
            opacity: 1;
            transform: translateX(0);
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.6rem;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 5px;
            min-width: 16px;
            height: 16px;
            text-align: center;
            line-height: 12px;
        }

        .cart-icon-container {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .top-navbar {
                padding: 0 10px;
            }

            .search-container {
                max-width: none;
            }

            .profile-container {
                display: none;
            }

            .sidebar {
                transform: translateX(-100%);
                width: 250px;
                top: 70px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .sidebar.show .sidebar-menu span,
            .sidebar.show .logout-btn span {
                opacity: 1;
                transform: translateX(0);
                position: static;
                pointer-events: auto;
            }

            .sidebar.show .sidebar-menu a {
                justify-content: flex-start;
                padding: 15px 20px;
            }

            .sidebar.show .sidebar-menu i {
                margin-right: 12px;
                font-size: 18px;
            }

            .sidebar.show .logout-btn {
                justify-content: flex-start;
            }
            
            .sidebar-toggle {
                position: fixed;
                top: 15px;
                left: 15px;
                z-index: 1002;
                background-color: #1a5c1a;
                color: white;
                border: none;
                padding: 10px;
                border-radius: 5px;
                font-size: 18px;
            }
        }

        /* Content area adjustment */
        .main-content {
            margin-left: 70px;
            margin-top: 70px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 70px);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }

        /* Hide elements initially */
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="top-navbar">
        <!-- Mobile toggle button -->
        <button class="sidebar-toggle d-md-none" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>

        <!-- Logo -->
        <div class="logo-container">
            <a href="#" id="logoLink">
                <img src="{{ asset('logoReUseMart.png') }}" alt="Logo Reusemart">
            </a>
        </div>

        <!-- Search Bar -->
        <div class="search-container" id="searchContainer">
            <input type="text" class="search-input" placeholder="Search products..." id="searchInput">
            <button class="search-btn" onclick="performSearch()">
                <i class="bi bi-search"></i>
            </button>
        </div>

        <!-- Profile Section -->
        <div class="profile-container">
            <button class="profile-btn" id="profileBtn">Profile</button>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Menu Items -->
        <ul class="sidebar-menu" id="sidebar-menu">
            <!-- Default items for non-logged in users -->
            <li id="produkMenu">
                <a href="/home">
                    <i class="bi bi-box-seam"></i>
                    <span>Produk</span>
                </a>
            </li>
            <li id="loginMenu">
                <a href="/UsersLogin">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Masuk</span>
                </a>
            </li>
        </ul>

        <!-- Logout Button Container -->
        <div class="logout-container" id="logout-container" style="display: none;">
            <form id="logout-form">
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Area -->


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const token = localStorage.getItem("auth_token");
            const role = localStorage.getItem("user_role");
            const sidebarMenu = document.getElementById("sidebar-menu");
            const logoLink = document.getElementById("logoLink");
            const loginMenu = document.getElementById("loginMenu");
            const produkMenu = document.getElementById("produkMenu");
            const logoutContainer = document.getElementById("logout-container");
            const profileBtn = document.getElementById("profileBtn");
            const profileContainer = document.querySelector(".profile-container");
            const searchContainer = document.getElementById('searchContainer');
            
            // Show search bar only for pembeli, penitip, and organisasi roles
            if (role && ['pembeli', 'penitip', 'organisasi'].includes(role.toLowerCase())) {
                searchContainer.style.display = 'block';
            } else {
                searchContainer.style.display = 'none';
            }
            // Function to create menu item
            const createMenuItem = (href, text, iconClass, isCart = false) => {
                const li = document.createElement("li");
                const link = document.createElement("a");
                link.href = href;

                if (isCart) {
                    link.innerHTML = `
                        <div class="cart-icon-container">
                            <i class="${iconClass}"></i>
                            <span class="cart-badge" id="cart-count-badge">0</span>
                        </div>
                        <span>${text}</span>
                    `;
                } else {
                    link.innerHTML = `
                        <i class="${iconClass}"></i>
                        <span>${text}</span>
                    `;
                }

                li.appendChild(link);
                return li;
            };

            // Setup logout functionality
            const setupLogout = () => {
                const logoutForm = document.getElementById("logout-form");
                if (logoutForm) {
                    logoutForm.addEventListener("submit", function (e) {
                        e.preventDefault();
                        localStorage.clear();
                        window.location.href = "/";
                    });
                }
            };

            // Search functionality
            window.performSearch = function() {
                const searchTerm = document.getElementById('searchInput').value;
                if (searchTerm.trim()) {
                    // Here you can implement your search logic
                    console.log('Searching for:', searchTerm);
                    
                    // Example: redirect to search results page
                    // window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;
                    
                    // Or display results in current page
                    const resultsDiv = document.getElementById('search-results');
                    resultsDiv.innerHTML = `<div class="alert alert-info">Searching for: "${searchTerm}"</div>`;
                }
            };

            // Enter key search
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            if (token && role) {
                // Remove default items and show logout
                if (loginMenu) loginMenu.remove();
                if (produkMenu) produkMenu.remove();
                logoutContainer.style.display = "block";

                // Update profile button based on role
                profileBtn.textContent = "Profile";
                profileBtn.onclick = function() {
                    // Redirect to appropriate profile page based on role
                    if (role === "pembeli") {
                        window.location.href = "/pembeli/MyProfile";
                    } else if (role === "penitip") {
                        window.location.href = "/penitip/profile";
                    } else {
                        window.location.href = "/pegawaidata";
                    }
                };

                // Clear existing menu
                sidebarMenu.innerHTML = "";

                // Default logo: disable link if not pembeli
                if (role !== "pembeli") logoLink.removeAttribute("href");

                // Add menu items based on role
                if (role === "pembeli") {
                    logoLink.href = "/";
                    sidebarMenu.appendChild(createMenuItem("/pembeli/dashboard", "Home", "bi bi-house-door"));
                    sidebarMenu.appendChild(createMenuItem("/home", "Produk", "bi bi-box-seam"));
                    sidebarMenu.appendChild(createMenuItem("/pembeli/alamat", "Alamat", "bi bi-geo-alt"));
                    sidebarMenu.appendChild(createMenuItem("/pembeli/HistoryPembeli", "History Pembelian", "bi bi-clock-history"));
                    sidebarMenu.appendChild(createMenuItem("/keranjang", "Keranjang", "bi bi-cart3", true));

                } else if (role === "penitip") {
                    sidebarMenu.appendChild(createMenuItem("/penitip/dashboard", "Home", "bi bi-house-door"));
                    sidebarMenu.appendChild(createMenuItem("/penitip/history", "History Penitip", "bi bi-clock-history"));
                    sidebarMenu.appendChild(createMenuItem("/penitip/historyPenjualan", "History Penjualan", "bi bi-clock-history"));

                } else if (role === "organisasi") {
                    sidebarMenu.appendChild(createMenuItem("/OrganisasiMain", "Request Donasi", "bi bi-heart"));

                } else if (role === "admin") {
                    sidebarMenu.appendChild(createMenuItem("/organisasi", "Organisasi", "bi bi-building"));
                    sidebarMenu.appendChild(createMenuItem("/pegawaiView", "Pegawai", "bi bi-people"));

                } else if (role === "cs") {
                    sidebarMenu.appendChild(createMenuItem("/pegawai/PenitipData", "Data Penitip", "bi bi-database"));
                    sidebarMenu.appendChild(createMenuItem("/verifikasi", "Verifikasi Pembayaran", "bi bi-check-circle"));
                    sidebarMenu.appendChild(createMenuItem("/merchandise", "Merchandise", "bi bi-bag"));
                    sidebarMenu.appendChild(createMenuItem("/Pegawai/TopSeller", "Top Seller", "bi bi-trophy"));

                } else if (role === "gudang") {
                    sidebarMenu.appendChild(createMenuItem("/pegawai/gudangview", "View Gudang", "bi bi-house"));
                    sidebarMenu.appendChild(createMenuItem("/pegawai/penjadwalan", "Penjadwalan Barang", "bi bi-calendar-event"));
                    sidebarMenu.appendChild(createMenuItem("/pegawai/ViewNota", "Nota Pembelian", "bi bi-receipt"));

                } else if (role === "owner") {
                    sidebarMenu.appendChild(createMenuItem("/pegawai/penjualankategori", "Laporan Penjualan", "bi bi-bar-chart"));
                    sidebarMenu.appendChild(createMenuItem("/pegawai/penitipanhabis", "Penitipan Expired", "bi bi-exclamation-triangle"));
                    sidebarMenu.appendChild(createMenuItem("/requestDonasi", "Request Donasi", "bi bi-heart"));
                    sidebarMenu.appendChild(createMenuItem("/donasi", "Donasi", "bi bi-gift"));
                    sidebarMenu.appendChild(createMenuItem("/laporanPenitip", "Penitip", "bi bi-person-lines-fill"));
                    sidebarMenu.appendChild(createMenuItem("/laporanPenjualan", "Penjualan", "bi bi-graph-up"));
                    sidebarMenu.appendChild(createMenuItem("/laporanKomisi", "Komisi", "bi bi-currency-dollar"));
                    sidebarMenu.appendChild(createMenuItem("/laporanStok", "Gudang", "bi bi-boxes"));
                }

                setupLogout();
            } else {
                // Hide profile button for non-logged in users
                profileContainer.style.display = "none";
            }
        });

        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }

        // Close sidebar when clicking outside (mobile)
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.sidebar-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !toggleBtn.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>
</html>