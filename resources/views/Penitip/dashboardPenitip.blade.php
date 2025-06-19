<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ReUseMart</title>
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    crossorigin="anonymous"
  />
  <!-- Toastify CSS -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css"
  />
</head>
<body>

  @include('layouts.navbar')

<video autoplay muted loop id="video-bg"
  style="
    position: fixed;
    top: 0; left: 0;
    width: 100vw;
    height: 100vh;
    object-fit: cover;
    z-index: -1;
  ">
    <source src="{{ asset('ReUseMartVid.mp4') }}" type="video/mp4">
    Your browser does not support the video tag.
</video>
  <main class="container my-5 p-4 bg-white rounded shadow-sm" style = "margin-top: 10% !important; margin-left: 10% !important; max-width: 85%  ;" >
    <h1 class="text-center text-success mb-3">Selamat Datang!</h1>
    <p class="lead text-center">
      Anda masuk sebagai <strong><span>Penitip</span></strong>.
    </p>
    <hr class="mb-4" />

    <h2 class="text-center text-success mb-4">Profil Penitip</h2>

    <table class="table table-bordered w-75 mx-auto">
      <tbody>
        <tr>
          <th scope="row" class="w-25">ID Penitip</th>
          <td id="penitipId">-</td>
        </tr>
        <tr>
          <th scope="row">Nama Penitip</th>
          <td id="penitipNama">-</td>
        </tr>
      </tbody>
    </table>
  </main>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <!-- Toastify JS -->
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const token = localStorage.getItem("auth_token");
      if (!token) {
        Toastify({
          text: "Token tidak ditemukan. Silakan login ulang.",
          duration: 4000,
          close: true,
          gravity: "top",
          position: "right",
          backgroundColor: "rgb(214, 10, 10)",
        }).showToast();
        setTimeout(() => {
          window.location.href = "/UsersLogin";
        }, 2000);
        return;
      }

      fetch('http://localhost:8000/api/penitip/profile', {
        method: 'GET',
        headers: {
          "Authorization": `Bearer ${token}`,
          "Accept": "application/json",
          "Content-Type": "application/json"
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.status && data.data) {
          const user = data.data;
          document.getElementById("penitipId").textContent = user.idPenitip || "-";
          document.getElementById("penitipNama").textContent = user.namaPenitip || "-";
        } else {
          Toastify({
            text: "Gagal memuat data profil. Silakan login ulang.",
            duration: 4000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "rgb(214, 10, 10)",
          }).showToast();
          setTimeout(() => {
            window.location.href = "/UsersLogin";
          }, 2000);
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        Toastify({
          text: "Terjadi kesalahan. Silakan coba lagi.",
          duration: 4000,
          close: true,
          gravity: "top",
          position: "right",
          backgroundColor: "rgb(214, 10, 10)",
        }).showToast();
      });
    });
  </script>
</body>
</html>
