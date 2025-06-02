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

    // Modal detail handler
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("btn-detail")) {
            const transaksi = JSON.parse(e.target.getAttribute("data-transaksi"));
            document.getElementById("modalNoNota").textContent = transaksi.noNota ?? '-';
            document.getElementById("modalTanggal").textContent = transaksi.tanggalWaktuPembelian ?? '-';
            document.getElementById("modalStatus").textContent = transaksi.status ?? '-';
            document.getElementById("modalTotal").textContent = "Rp" + formatRupiah(transaksi.totalHarga);
            document.getElementById("modalAlamat").textContent = transaksi.pembeli?.alamat?.[0]?.alamat ?? '-';

            // Barang list
            const barangList = transaksi.detail_transaksi_pembelian || [];
            const modalBarangList = document.getElementById("modalBarangList");
            modalBarangList.innerHTML = "";
            barangList.forEach(b => {
                modalBarangList.innerHTML += `<li class="list-group-item">${b.barang?.namaBarang ?? '-'} - Rp${formatRupiah(b.barang?.hargaBarang)}</li>`;
            });
        }
    });
});
</script>
</body>
</html>