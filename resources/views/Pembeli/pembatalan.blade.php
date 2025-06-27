<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reusemart</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Toastify CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
</head>

<style>
  body {
    background-color: #f8f9fa;
  }

  h3 {
    font-weight: bold;
  }

  .custom-table-container {
    padding-left: 3rem;
    padding-right: 3rem;
    padding-bottom: 2rem;
  }

  .table thead th {
    background-color: #e9ecef;
  }

  .bukti-img {
    width: 100px;
    height: auto;
    object-fit: contain;
  }
</style>

<body>
    <!-- ////////////////////INI MODAL GAMBAR///////////////////////////// -->
    <div class="modal fade" id="viewGambar" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Preview Gambar" class="img-fluid" style="max-height:150vh;">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="batalkan" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="exampleModalLabel">Pembatalan Pembelian</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin akan membatalkan transaksi ini, dengan total transaksi <strong> Rp <span id="totalTransaksi"></strong> dan di konversi menjadi poin reward sebanyak <strong> <span id="poinDidapat"></strong> ? Total poin anda setelah ini adalah  <strong><span id="poinAkhir"></strong>.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

  @include('layouts.navbar')

  <h3 class="text-center mb-3 mt-2 my-5 p-4">Pembatalan Transaksi Pembelian</h3>

  <div class="custom-table-container">
    <table class="table table-striped table-bordered table-hover mt-3">
      <thead class="text-center">
        <tr>
          <th scope="col">No Transaksi</th>
          <th scope="col">Tanggal Transaksi</th>
          <th scope="col">Total Transaksi</th>
          <th scope="col">Status Transaksi</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody id="verif-body" class="text-center">
        <tr><td colspan="5">Memuat data...</td></tr>
      </tbody>
    </table>
  </div>

  <footer class="bg-dark text-white text-center p-3">
    <p>&copy; 2025 Reusemart</p>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const token = localStorage.getItem("auth_token");
      if (!token) {
        window.location.href = "/UsersLogin";
        return;
      }

      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      fetch(`http://127.0.0.1:8000/api/pembeli/pembatalan`, {
        method: "GET",
        headers: {
          "Authorization": `Bearer ${token}`,
          "Accept": "application/json",
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken,
        },
      })
        .then(response => response.json())
        .then(result => {
          if (result.status && result.data.length > 0) {
            renderTable(result.data);
          } else {
            document.getElementById("verif-body").innerHTML = `<tr><td colspan="5">Tidak ada data transaksi pembelian</td></tr>`;
          }
        })
        .catch(error => {
          console.error("Error fetching data:", error);
          document.getElementById("verif-body").innerHTML = `<tr><td colspan="5">Gagal memuat data.</td></tr>`;
        });

      // Render data ke tabel
      function renderTable(data) {
        const tbody = document.getElementById("verif-body");
        tbody.innerHTML = "";

        data.forEach((item, index) => {
          const row = document.createElement("tr");

          row.innerHTML = `
            <td>${item.noNota}</td>
            <td>${item.tanggalWaktuPembelian}</td>
            <td>Rp${item.totalHarga.toLocaleString("id-ID")}</td>
            <td><span class="badge bg-warning text-dark">${item.status}</span></td>
            <td>
              <button class="btn btn-danger btn-batalkan btn-sm rejectPembelian" data-id="${item.noNota}" data-bs-toggle="modal" 
              data-bs-target="#batalkan" data-totalTransaksi="${item.totalHarga}" data-poinDidapat="${item.totalHarga}"
              data-poinAkhir="${item.totalHarga}"><i class="bi bi-x-circle"></i></button>
            </td>
          `;
          tbody.appendChild(row);
        });

        
        document.querySelectorAll(".rejectPembelian").forEach(link => {
            link.addEventListener("click", function () {
                const noNota = this.getAttribute("data-id");
                updateStatus(noNota, "Dibatalkan Pembeli");
            });
        });
    }

    document.addEventListener("click", function (e) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        if (e.target.classList.contains("btn-batalkan")) {
            const button = e.target;
            document.getElementById("totalTransaksi").textContent = button.getAttribute("data-totalTransaksi");
            document.getElementById("poinDidapat").textContent = button.getAttribute("data-poinDidapat");
            document.getElementById("poinAkhir").textContent = button.getAttribute("data-poinAkhir");

        }
    });

    function updateStatus(noNota, status) {
        fetch(`http://127.0.0.1:8000/api/pembeli/batal/${noNota}`, {
            method: "POST",
            headers: {
            "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
            "Accept": "application/json",
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
            Toastify({
                text: `Berhasil membatalkan transaksi Pembelian`,
                duration: 3000,
                gravity: "top",
                position: "right",
                style: { background: "#4caf50" }
            }).showToast();

            location.reload();
            } else {
            Toastify({
                text: data.message || "Gagal membatalkan transaksi pembelian",
                duration: 3000,
                gravity: "top",
                position: "right",
                style: { background: "#d32f2f" }
            }).showToast();
            }
        })
        .catch(error => {
            console.error("Update error:", error);
            Toastify({
            text: "Terjadi kesalahan",
            duration: 3000,
            gravity: "top",
            position: "right",
            style: { background: "#d32f2f" }
            }).showToast();
        });
    }
    });
  </script>
</body>
</html>
