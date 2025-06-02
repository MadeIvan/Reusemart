<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Profil Pegawai - ReUseMart</title>
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
  <main class="container my-5 p-4 bg-white rounded shadow-sm" style="max-width: 700px;">
    <h1 class="text-center text-success mb-3">Selamat Datang!</h1>
    <p class="lead text-center">
      Anda masuk sebagai <strong><span id="loggedInAs">Memuat...</span></strong>.
    </p>
    <hr class="mb-4" />

    <h2 class="text-center text-success mb-4">Profil Pegawai</h2>

    <table class="table table-bordered w-75 mx-auto">
      <tbody>
        <tr>
          <th scope="row" class="w-25">ID Pegawai</th>
          <td id="pegawaiId">-</td>
        </tr>
        <tr>
          <th scope="row">Nama Pegawai</th>
          <td id="pegawaiNama">-</td>
        </tr>
        <tr>
          <th scope="row">Tanggal Lahir</th>
          <td id="pegawaiTanggalLahir">-</td>
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
      const pegawaiDataString = localStorage.getItem("pegawaiData");
      if (!pegawaiDataString) {
        Toastify({
          text: "Anda belum login atau sesi telah berakhir. Mohon login.",
          duration: 4000,
          close: true,
          gravity: "top",
          position: "right",
          backgroundColor: "rgb(214, 10, 10)",
        }).showToast();
        setTimeout(() => {
          window.location.href = "/PegawaiLogin";
        }, 2000);
        return;
      }

      try {
        const pegawai = JSON.parse(pegawaiDataString);
        let  loggedInAs = "Pegawai";
        if(pegawai.idJabatan==="1"){
          loggedInAs="Owner";
        }else if (pegawai.idJabatan==="2"){
          loggedInAs="Admin";
        }else if (pegawai.idJabatan==="3"){
          loggedInAs="Pegawaai Gudang";
        }else if (pegawai.idJabatan==="4"){
          loggedInAs="Kuiri";
        }else if (pegawai.idJabatan==="5"){
          loggedInAs="Customer Service";
        }else if (pegawai.idJabatan==="6"){
          loggedInAs="Hunter";
        }
        document.getElementById("loggedInAs").textContent =
          loggedInAs || "Pegawai";
        document.getElementById("pegawaiId").textContent =
          pegawai.idPegawai || "-";
        document.getElementById("pegawaiNama").textContent =
          pegawai.namaPegawai || "-";

        if (pegawai.tanggalLahir) {
          const date = new Date(pegawai.tanggalLahir);
          const options = { day: "2-digit", month: "long", year: "numeric" };
          document.getElementById(
            "pegawaiTanggalLahir"
          ).textContent = date.toLocaleDateString("id-ID", options);
        } else {
          document.getElementById("pegawaiTanggalLahir").textContent = "-";
        }
      } catch (e) {
        console.error("Error parsing pegawai data:", e);
        localStorage.removeItem("pegawaiData");
        Toastify({
          text: "Data profil tidak valid. Mohon login ulang.",
          duration: 4000,
          close: true,
          gravity: "top",
          position: "right",
          backgroundColor: "rgb(221, 25, 25)",
        }).showToast();
        // setTimeout(() => {
        //   window.location.href = "/PegawaiLogin";
        // }, 2000);
      }
    });
  </script>
</body>
</html>
