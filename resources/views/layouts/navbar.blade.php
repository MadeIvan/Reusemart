<nav class="navbar navbar-expand-lg navbar-light bg-light  shadow-sm">
    <div class="container-fluid">

        <!-- Logo -->
        @if (Auth::guard('pegawai')->check() || Auth::guard('organisasi')->check() || Auth::guard('penitip')->check() || Auth::guard('pembeli')->check())
            <!-- Logo tanpa link untuk pegawai -->
            <img src="{{ asset('logoReUseMart.png') }}" alt="Logo Reusemart" style="width:50px;">
        @else
            <!-- Logo dengan link untuk selain pegawai -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('logoReUseMart.png') }}" alt="Logo Reusemart" style="width:50px;">
            </a>
        @endif

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Bagian kiri navbar -->
            <ul class="navbar-nav me-auto">
                
                @if (!Auth::guard('penitip')->check() && !Auth::guard('organisasi')->check() && !Auth::guard('pegawai')->check())
                    <li class="nav-item d-flex align-items-center">
                        <a class="nav-link text-black" href="{{ url('/home') }}">
                            <strong>Produk</strong>
                        </a>
                    </li>
                @endif
            </ul>

            <!-- Bagian kanan navbar -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 profile-menu">

                <!-- Belum login -->
                @if (!Auth::guard('pegawai')->check() && !Auth::guard('organisasi')->check() && !Auth::guard('penitip')->check() && !Auth::guard('pembeli')->check())
                    <li class="nav-item">
                        <a class="nav-link text-black" href="{{ url('/UsersLogin') }}"><strong>Masuk</strong></a>
                    </li>

                <!-- Login pegawai -->
                @elseif (Auth::guard('pegawai')->check())
                    @php
                        $pegawai = Auth::guard('pegawai')->user();
                    @endphp

                    @if ($pegawai->idJabatan == 1) {{-- Admin --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/dashboard') }}">Dashboard Admin</a>
                        </li>
                    @elseif ($pegawai->idJabatan == 2) {{-- CS --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/cs/tickets') }}">Customer Service</a>
                        </li>
                    @elseif ($pegawai->idJabatan == 3) {{-- Owner --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/owner/reports') }}">Laporan</a>
                        </li>
                    @elseif ($pegawai->idJabatan == 4) {{-- Pegawai Gudang --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/gudang/inventory') }}">Inventori</a>
                        </li>
                    @elseif ($pegawai->idJabatan == 5) {{-- Kurir --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/kurir/deliveries') }}">Pengiriman</a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/pegawai/logout') }}">Logout</a>
                    </li>

                <!-- Login organisasi -->
                @elseif (Auth::guard('organisasi')->check())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/organisasi/dashboard') }}">Dashboard Organisasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/organisasi/logout') }}">Logout</a>
                    </li>

                <!-- Login penitip -->
                @elseif (Auth::guard('penitip')->check())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/penitip/dashboard') }}">Dashboard Penitip</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/penitip/logout') }}">Logout</a>
                    </li>

                <!-- Login pembeli -->
                @elseif (Auth::guard('pembeli')->check())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/pembeli/dashboard') }}">Dashboard Pembeli</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/pembeli/logout') }}">Logout</a>
                    </li>
                @endif

            </ul>
        </div>
    </div>
</nav>
