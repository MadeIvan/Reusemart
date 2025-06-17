<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@include('layouts.navbar')

<div class="container py-4">
    <h3 class="text-center mb-4 text-success">Riwayat Pembelian</h3>
    <div id="pembelianContainer" class="row g-3"></div>
</div>

<!-- Star Rating Modal -->
<div class="modal fade" id="starRatingModal" tabindex="-1" aria-labelledby="starRatingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="starRatingForm">
        <div class="modal-header">
          <h5 class="modal-title" id="starRatingModalLabel">Rate Barang</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <div id="starContainer" style="font-size: 2rem; color: #ccc; cursor: pointer;">
            <!-- Stars will be inserted here by JS -->
          </div>
          <input type="hidden" id="starRatingValue" name="starRating" value="0" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit Rating</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal Detail -->
<div class="modal fade" id="detailPembelianModal" tabindex="-1" aria-labelledby="detailPembelianLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailPembelianLabel">Detail Transaksi Pembelian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group mb-3">
            <li class="list-group-item"><strong>No Nota:</strong> <span id="modalNoNota"></span></li>
            <li class="list-group-item"><strong>Tanggal Pembelian:</strong> <span id="modalTanggal"></span></li>
            <li class="list-group-item"><strong>Status:</strong> <span id="modalStatus"></span></li>
            <li class="list-group-item"><strong>Total Harga:</strong> <span id="modalTotal"></span></li>
            <li class="list-group-item"><strong>Alamat:</strong> <span id="modalAlamat"></span></li>
        </ul>
        <h6>Daftar Barang:</h6>
        <ul class="list-group" id="modalBarangList"></ul>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script>
let globalIdPembeli = null;
const pembelianContainer = document.getElementById('pembelianContainer');
const token = localStorage.getItem("auth_token");
if (!token) window.location.href = "{{ url('/UsersLogin') }}";

document.addEventListener("DOMContentLoaded", function() {
    fetchPembelian();

    function fetchPembelian() {
        fetch("http://127.0.0.1:8000/api/showAllTransaksi", {
            method: "GET",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status && Array.isArray(data.data)) {
                renderPembelian(data.data);
                console.log("data cari nama wak: ",data.data[0].pembeli.idPembeli);
                globalIdPembeli =data.data[0].pembeli.idPembeli;
            } else {
                pembelianContainer.innerHTML = "<div class='col-12 text-center text-muted'>Tidak ada riwayat pembelian.</div>";
            }
        })
        .catch(error => {
            pembelianContainer.innerHTML = "<div class='col-12 text-center text-danger'>Gagal memuat data.</div>";
        });
    }

    function renderPembelian(data) {
        pembelianContainer.innerHTML = "";
        data.forEach(item => {
            const barangList = item.detail_transaksi_pembelian.map(b => b.barang?.namaBarang).join(', ');
            const card = `
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-2">${item.noNota}</h5>
                            <p class="mb-1"><strong>Tanggal:</strong> ${item.tanggalWaktuPembelian ?? '-'}</p>
                            <p class="mb-1"><strong>Status:</strong> <span class="badge bg-${getStatusColor(item.status)}">${item.status}</span></p>
                            <p class="mb-1"><strong>Total:</strong> Rp${formatRupiah(item.totalHarga)}</p>
                            <p class="mb-2"><strong>Barang:</strong> ${barangList}</p>
                            <button class="btn btn-outline-primary btn-sm btn-detail" 
                                data-transaksi='${JSON.stringify(item)}'
                                data-bs-toggle="modal" data-bs-target="#detailPembelianModal">
                                Lihat Detail
                            </button>
                            <button class="btn btn-outline-warning btn-sm btn-detail" 
                                data-transaksi='${JSON.stringify(item)}'
                                data-bs-toggle="modal" data-bs-target="#starRatingModal">
                                Beri Rating
                            </button>
                        </div>
                    </div>
                </div>
            `;
            pembelianContainer.innerHTML += card;
        });
    }

    function getStatusColor(status) {
        if (!status) return "secondary";
        status = status.toLowerCase();
        if (status.includes("lunas")) return "success";
        if (status.includes("batal")) return "danger";
        if (status.includes("belum")) return "warning";
        return "secondary";
    }

    function formatRupiah(angka) {
        return (angka ?? 0).toLocaleString('id-ID');
    }

    const starContainer = document.getElementById("starContainer");
    const starRatingValue = document.getElementById("starRatingValue");
    let currentRating = 0;

    let currentIdPenitip = null;
    let currentIdBarang = null;
    let currentIdRater = null;

    // Create 5 stars
    for (let i = 1; i <= 5; i++) {
      const star = document.createElement("span");
      star.classList.add("star");
      star.innerHTML = "&#9733;"; // star character
      star.dataset.value = i;

      star.addEventListener("mouseover", () => {
        highlightStars(i);
      });

      star.addEventListener("mouseout", () => {
        highlightStars(currentRating);
      });

      star.addEventListener("click", () => {
        currentRating = i;
        starRatingValue.value = currentRating;
        highlightStars(currentRating);
      });

      starContainer.appendChild(star);
    }

    function highlightStars(rating) {
      const stars = starContainer.querySelectorAll(".star");
      stars.forEach(star => {
        if (parseInt(star.dataset.value) <= rating) {
          star.style.color = "#ffc107"; // gold color
        } else {
          star.style.color = "#ccc"; // gray color
        }
      });
    }

    // Bootstrap modal show event without jQuery
    document.getElementById('starRatingModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const item = JSON.parse(button.getAttribute('data-transaksi'));
        currentIdPenitip = item.transaksiPenitipan?.idPenitip || null;
        currentIdBarang = item.idBarang || null;
        currentIdRater = localStorage.getItem('user_id'); // or your auth user ID
        currentRating = 0;
        starRatingValue.value = 0;
        highlightStars(0);
    });

    // Optional: handle form submission
    document.getElementById("starRatingForm").addEventListener("submit", async function (e) {
      e.preventDefault();
      if (currentRating === 0) {
        alert("Please select a rating before submitting.");
        return;
      }

      const confirmed = confirm("Kirim Rating ke Penitip?");
    if (!confirmed) {
        // User clicked Cancel, stop submission
        return;
    }
      const payload = {
        idTarget: globalIdPenitip,
        idBarang: globalIdBarang,
        idRater: globalIdPembeli,
        value: currentRating
      };
      console.log("Payload rating : ",payload);


      try {
        const response = await fetch("http://127.0.0.1:8000/api/rating", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (response.ok && result.status) {
          alert("Rating submitted successfully!");
          const modal = bootstrap.Modal.getInstance(document.getElementById("starRatingModal"));
          modal.hide();
        } else {
          alert("Failed to submit rating: " + (result.message || "Unknown error"));
        }
      } catch (error) {
        console.error("Error submitting rating:", error);
        alert("An error occurred while submitting rating.");
      }
    });

    // Modal detail handler
// Declare global variables outside event listener
let globalIdPenitip = null;
let globalIdBarang = null;

document.addEventListener("click", function(e) {
    if (e.target.classList.contains("btn-detail")) {
        const transaksi = JSON.parse(e.target.getAttribute("data-transaksi"));
        const firstBarangId = transaksi.detail_transaksi_pembelian?.[0]?.idBarang
            ?? transaksi.detail_transaksi_pembelian?.[0]?.barang?.idBarang
            ?? null;

        console.log("Clicked idBarang:", firstBarangId);

        if (!firstBarangId) {
            console.error("No idBarang found.");
            return;
        }

        // Fetch idPenitip and idBarang from API
        fetch(`http://127.0.0.1:8000/api/barang/simple/${firstBarangId}`, {
            method: 'GET',
            headers: {
                "Authorization": `Bearer ${localStorage.getItem('auth_token')}`,
                "Accept": "application/json"
            }
        })
        .then(response => {
            if (!response.ok) throw new Error("Failed to fetch barang simple data");
            return response.json();
        })
        .then(data => {
            // Assuming your API returns JSON like: { idPenitip: "...", idBarang: "..." }
            console.log("API returned data:", data);

            // Store in global variables
            globalIdPenitip = data.idPenitip || null;
            globalIdBarang = data.idBarang || null;

            console.log("Stored globalIdPenitip:", globalIdPenitip);
            console.log("Stored globalIdBarang:", globalIdBarang);

            // Now you can use these globals elsewhere in your app
        })
        .catch(error => {
            console.error("Error fetching barang simple data:", error);
        });
    }
});


});

</script>

</body>
</html>