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
  <main class="container my-5 p-4 bg-white rounded shadow-sm" style = "margin-top: 10% !important; margin-left: 10% !important; max-width:85%;" >
    <h1 class="text-center text-success mb-3">Selamat Datang!</h1>
    <p class="lead text-center">
      Anda masuk sebagai <strong><span id="loggedInAs">Memuat...</span></strong>.
    </p>
    <hr class="mb-4" />

    <h2 class="text-center text-success mb-4">Profil Pembeli</h2>

    <table class="table table-bordered w-75 mx-auto">
      <tbody>
        <tr>
          <th scope="row" class="w-25">ID Pembeli</th>
          <td id="idPembeli">-</td>
        </tr>
        <tr>
          <th scope="row">Nama Pembeli</th>
          <td id="namaPembeli">-</td>
        </tr>
        <tr>
          <th scope="row">Username</th>
          <td id="usernamePembeli">-</td>
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
      const dataUserString = localStorage.getItem("userData");
      if (!dataUserString) {
        Toastify({
          text: "Anda belum login atau sesi telah berakhir. Mohon login.",
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

      try {
        const user = JSON.parse(dataUserString);
        document.getElementById("loggedInAs").textContent = localStorage.getItem("user_role") || "Pengguna";
        document.getElementById("idPembeli").textContent = user.pembeli.idPembeli || "-";
        document.getElementById("namaPembeli").textContent = user.pembeli.namaPembeli || "-";
        document.getElementById("usernamePembeli").textContent = user.pembeli.username || "-";
    } catch (e) {
        console.error("Error parsing user data:", e);
        Toastify({
        text: "Gagal menampilkan data pengguna",
        duration: 4000,
        close: true,
        gravity: "top",
        position: "right",
        backgroundColor: "orange",
        }).showToast();
    }
    });
  </script>
</body>
</html>
