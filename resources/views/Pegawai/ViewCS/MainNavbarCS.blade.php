<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Penitip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        #penitipTableContainer {
            max-height: 400px;
            overflow-y: auto;
        }

        #penitipTable thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 1;
        }

        .no-data-message {
            text-align: center;
            color: #6c757d;
        }

        .btn-container {
            display: flex;
            gap: 10px;
        }

        .register-form-container {
            display: none;
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 10px;
            z-index: 999;
            width: 60%;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 998;
            display: none;
        }

        .toast-container {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        .nav-item active{
            color:white
        }
    </style>
</head>
<body>

        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="{{ asset('logoReUseMart.png') }}" alt="Logo Reusemart" style="width:50px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        @foreach($Halaman as $hal => $url)
                        <li class="nav-item {{ $hal == $current ? 'bg-secondary' : '' }}">
                            <a class="nav-link {{ $hal == $current ? 'text-white' : '' }}" href="{{ $url }}">
                                {{ $hal }}
                            </a>
                        </li>
                    @endforeach
                    </ul>
                    <form class="d-flex">
                        <button class="btn btn-outline-danger" type="submit">Log Out</button>
                    </form>
                </div>
            </div>
        </nav>
    <div class="container mt-4">   
    @yield('content')
    </div>
    
    
    
    
    
    <script src="{{ asset('js/scriptCS.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
