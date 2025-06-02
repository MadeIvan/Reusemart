
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Pembeli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Poppins', sans-serif; }
        .card { border: none; border-radius: 1rem; background-color: #fff; transition: transform 0.3s; }
        .card:hover { transform: scale(1.01); box-shadow: 0 6px 20px rgba(0,0,0,0.1);}
        .img-circle { border: 4px solid #4caf50; padding: 2px; }
        h6, h4 { font-weight: 600; margin-bottom: 0.3rem; }
    </style>
</head>
<body>
@include('layouts.navbar')

<h1 class="text-center mb-4 mt-4" style="color:rgb(0, 138, 57); font-family: 'Bagel Fat One', system-ui;">
    Profile Pembeli
</h1>
<div class="d-flex justify-content-center ms-5">
    <div class="d-flex justify-content-center mt-4 mb-4">
        <div class="card shadow-lg" style="width: 350px; border-radius: 1rem;">
            <div class="card-body text-center">
                <img src="{{ asset('img/pp.png') }}" alt="Avatar"
                    class="img-fluid rounded-circle mb-3"
                    style="width: 150px;" />
                <h4 class="text-success">Pembeli</h4>
            </div>
            <div class="row px-3 pb-3">
                <div class="col-12 text-center">
                    <h6 class="text-success"><strong>Poin</strong></h6>
                    <p id="poin" class="text-muted">Loading...</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4 mt-4 ms-5" style="width: 940px;">
        <div class="card bg-subtle shadow-lg" style="border-radius: 0.5rem;">
            <div class="card-body p-4">
                <h3 class="text-center mb-5" style="color:rgb(0, 138, 57); font-family: 'Bagel Fat One', system-ui;">
                    Informasi Umum
                </h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 style="color:rgb(0, 138, 57)"><strong>Nama Lengkap</strong></h6>
                        <p id="namaPembeli" class="text-muted">Loading...</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 style="color:rgb(0, 138, 57)"><strong>Username</strong></h6>
                        <p id="username" class="text-muted">Loading...</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 style="color:rgb(0, 138, 57)"><strong>Email</strong></h6>
                        <p id="email" class="text-muted">Loading...</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 style="color:rgb(0, 138, 57)"><strong>Alamat</strong></h6>
                        <p id="alamat" class="text-muted">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const auth_token = localStorage.getItem("auth_token");
if (!auth_token) window.location.href = "{{ url('/UsersLogin') }}";

fetch('http://127.0.0.1:8000/api/pembeli/poin', {
    method: 'GET',
    headers: {
        "Authorization": `Bearer ${auth_token}`,
        'Accept': 'application/json',
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken,
    }
})
.then(response => response.json())
.then(data => {
    if (data.status) {
        const user = data.data;
        document.getElementById('namaPembeli').textContent = user.namaPembeli ?? '-';
        document.getElementById('username').textContent = user.username ?? '-';
        document.getElementById('email').textContent = user.email ?? '-';
        document.getElementById('alamat').textContent = user.alamat?.[0]?.alamat ?? '-';
        document.getElementById('poin').textContent = user.poin ? `${user.poin} Poin` : '0 Poin';
        document.getElementById('namaPembeli').classList.remove('text-muted');
        document.getElementById('username').classList.remove('text-muted');
        document.getElementById('email').classList.remove('text-muted');
        document.getElementById('alamat').classList.remove('text-muted');
        document.getElementById('poin').classList.remove('text-muted');
    } else {
        document.getElementById('poin').textContent = 'Gagal mengambil data';
    }
})
.catch(error => {
    document.getElementById('poin').textContent = 'Error';
    console.error('Error:', error);
});
</script>
</body>
</html>