<!DOCTYPE html>
<html lang="en">
<head>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Penitipan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Poppins', sans-serif; }
        .card { border: none; border-radius: 1rem; background-color: #fff; transition: transform 0.3s; box-shadow: 0 6px 20px rgba(0,0,0,0.1);}
        .card:hover { transform: scale(1.01); box-shadow: 0 6px 20px rgba(0,0,0,0.1);}
        .carousel-image { width: 100%; height: 300px; object-fit: cover; border-radius: 8px; cursor: pointer; transition: transform 0.2s;}
        .carousel-image:hover { transform: scale(1.02);}
        .fullscreen-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.9); display: flex; justify-content: center; align-items: center; z-index: 9999; opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s;}
        .fullscreen-modal.active { opacity: 1; visibility: visible;}
        .fullscreen-image { max-width: 90%; max-height: 90%; object-fit: contain; border-radius: 8px; box-shadow: 0 0 30px rgba(255, 255, 255, 0.3);}
        .close-fullscreen { position: absolute; top: 20px; right: 30px; color: white; font-size: 40px; font-weight: bold; cursor: pointer; z-index: 10000; transition: color 0.2s;}
        .close-fullscreen:hover { color: #ccc;}
        .carousel-thumbnails { display: flex; justify-content: center; gap: 10px; margin-top: 15px; flex-wrap: wrap;}
        .thumbnail { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid transparent; transition: border-color 0.2s, transform 0.2s;}
        .thumbnail:hover { transform: scale(1.1); border-color: #007bff;}
        .thumbnail.active { border-color: #007bff;}
    </style>
</head>
<body>
@include('layouts.navbar')



<div id="loadingIndicator" class="container my-4">
    <div class="text-center">
        <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Memuat data...</p>
    </div>
</div>
<div class="container mb-4 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari barang, kategori, status, dll...">
        </div>
    </div>
</div>
<h3 class="text-center mb-4 mt-4" style="color:rgb(0, 138, 57); font-family: 'Bagel Fat One', system-ui;">
    Riwayat Penitipan Barang
</h3>
<div id="barangContainer" class="row g-3 px-5"></div>

<!-- Modal Detail Barang -->
<div class="modal fade" id="detailBarang" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="exampleModalLabel"><strong>Detail Barang</strong></h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="barangCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
                    <div class="carousel-inner" id="carouselImages"></div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#barangCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#barangCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
                <div id="carouselThumbnails" class="carousel-thumbnails"></div>
                <div class="mb-3"><strong>Nama Barang: </strong><span id="namaBarang"></span></div>
                <div class="mb-3"><strong>Berat Barang: </strong><span id="beratBarang"></span></div>
                <div class="mb-3"><strong>Harga Barang: </strong><span id="hargaBarang"></span></div>
                <div class="mb-3"><strong>Tanggal Penitipan Barang: </strong><span id="tanggalPenitipan"></span></div>
                <div class="mb-3"><strong>Tanggal Penitipan Selesai: </strong><span id="tanggalPenitipanSelesai"></span></div>
                <div class="mb-3"><strong>Kategori Barang: </strong><span id="kategori"></span></div>
                <div class="mb-3"><strong>Status Barang: </strong><span id="statusBarang"></span></div>
                <div class="mb-3"><strong>Tanggal Barang Terjual: </strong><span id="tanggalTerjual"></span></div>
                <div class="mb-3 text-end">
                    <button id="btnPerpanjangPenitipan" class="btn btn-success">
                        Perpanjang Penitipan +30 Hari
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Image Modal -->
<div id="fullscreenModal" class="fullscreen-modal">
    <span class="close-fullscreen">&times;</span>
    <img id="fullscreenImage" class="fullscreen-image" src="" alt="Fullscreen Image">
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

<script>

function getCookie(name) {
    const value = ; ${document.cookie};
    const parts = value.split(; ${name}=);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// Get CSRF token from cookie and decode it
function getCSRFToken() {
    const xsrfToken = getCookie('XSRF-TOKEN');
    if (xsrfToken) {
        // Decode the URL-encoded token
        return decodeURIComponent(xsrfToken);
    }
    // Fallback to meta tag
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    return metaToken ? metaToken.getAttribute('content') : null;
}

// Use the token in your fetch requests
const csrfToken = getCSRFToken();
const token = localStorage.getItem("auth_token");
if (!token) window.location.href = "{{ url('/UsersLogin') }}";

function openFullscreenPreview(src) {
    const modal = document.getElementById('fullscreenModal');
    const img = document.getElementById('fullscreenImage');
    img.src = src;
    modal.classList.add('active');
}
function closeFullscreenPreview() {
    document.getElementById('fullscreenModal').classList.remove('active');
}
document.getElementById('fullscreenModal').addEventListener('click', function(e) {
    if (e.target === this || e.target.classList.contains('close-fullscreen')) closeFullscreenPreview();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeFullscreenPreview();
});

let allBarangData = [];
let currentIdTransaksiPenitipan = null;

document.addEventListener("DOMContentLoaded", function(){
    function hideLoading() {
        document.getElementById('loadingIndicator').style.display = 'none';
    }
    function showError(message) {
        hideLoading();
        document.getElementById("barangContainer").innerHTML = <div class='col-12 text-center text-danger'>${message}</div>;
    }
    fetch('http://localhost:8000/api/penitip/profile', {
        method: 'GET',
        headers: {
            "Authorization": Bearer ${token},
            'Accept': 'application/json',
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            fetchBarang(data.data.idPenitip);
        } else {
            showError('Gagal mengambil data profil');
        }
    })
    .catch(error => {
        showError('Error mengambil data profil');
    });

    function fetchBarang(idPenitip){
        fetch(http://localhost:8000/api/transaksi-penitipan/penitip/${idPenitip}, {
            method: "GET",
            headers: {
                "Authorization": Bearer ${token},
                'Accept': 'application/json',
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.status && Array.isArray(data.data)) {
                allBarangData = data.data;
                displayBarang(allBarangData);
            } else {
                document.getElementById("barangContainer").innerHTML = "<div class='col-12 text-center text-muted'>Tidak ada data barang ditemukan.</div>";
            }
        })
        .catch(error => {
            hideLoading();
            document.getElementById("barangContainer").innerHTML = "<div class='col-12 text-center text-danger'>Gagal memuat data: " + error.message + "</div>";
        });
    }

    function displayBarang(data) {
        const barangContainer = document.getElementById("barangContainer");
        barangContainer.innerHTML = "";
        if (data.length === 0) {
            barangContainer.innerHTML = "<div class='col-12 text-center text-muted'>Belum ada barang yang dititipkan.</div>";
            return;
        }
        data.forEach(item => {
            const details = item.detail_transaksi_penitipan;
            details.forEach(detail => {
                if (!detail || !detail.barang) return;
                const barang = detail.barang;
                const images = detail.barang.imagesbarang || barang.imagesbarang || {};
                const imageList = [images.image1, images.image2, images.image3, images.image4, images.image5].filter(Boolean);
                let statusClass = "";
                switch ((barang.statusBarang ?? "").toLowerCase()) {
                    case "dikembalikan": statusClass = "text-danger"; break;
                    case "didonasikan": statusClass = "text-primary"; break;
                    case "terjual": statusClass = "text-success"; break;
                    case "tersedia": statusClass = "text-secondary"; break;
                    default: statusClass = "text-muted"; break;
                }
                const formatCurrency = (amount) => {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
                };
                const card = `
                    <div class="col-lg-3 col-md-4 col-sm-6 p-2">
                        <div class="card h-100">
                            <img src="http://127.0.0.1:8000/${imageList[0] ? imageList[0] : 'no-image.png'}"
                                class="card-img-top"
                                alt="${imageList[0] ? imageList[0] : 'No Image'}"
                                style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-2"><strong>${barang.namaBarang}</strong></h5>
                                <p class="card-text text-muted mb-2">${formatCurrency(barang.hargaBarang)}</p>
                                <p class="card-text text-muted mb-2">${barang.beratBarang} kg</p>
                                <p class="card-text ${statusClass} mb-3"><strong>${barang.statusBarang}</strong></p>
                                <div class="mt-auto">
                                    <button type="button" class="btn btn-detail btn-outline-primary w-100"
                                        data-namaBarang="${barang.namaBarang}"
                                        data-beratBarang="${barang.beratBarang} kg"
                                        data-hargaBarang="${formatCurrency(barang.hargaBarang)}"
                                        data-kategori="${barang.kategori}"
                                        data-tanggalPenitipan="${item.tanggalPenitipan}"
                                        data-tanggalPenitipanSelesai="${item.tanggalPenitipanSelesai}"
                                        data-statusBarang="${barang.statusBarang}"
                                        data-tanggalTerjual="${barang.statusBarang && barang.statusBarang.toLowerCase() === 'terjual' && barang.detail_transaksi_pembelian && barang.detail_transaksi_pembelian.length > 0 ? barang.detail_transaksi_pembelian[0].transaksi_pembelian.tanggalWaktuPembelian : '-'}"
                                        data-images='${JSON.stringify(imageList)}'
                                        data-idtransaksipenitipan="${item.idTransaksiPenitipan}"
                                        data-bs-toggle="modal" data-bs-target="#detailBarang">
                                        Lihat Detail
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                barangContainer.innerHTML += card;
            });
        });
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const keyword = this.value.trim().toLowerCase();
        if (!keyword) {
            displayBarang(allBarangData);
            return;
        }
        const filtered = allBarangData.filter(item =>
            item.detail_transaksi_penitipan.some(detail => {
                const b = detail.barang;
                return (
                    (b.namaBarang && b.namaBarang.toLowerCase().includes(keyword)) ||
                    (b.kategori && b.kategori.toLowerCase().includes(keyword)) ||
                    (b.statusBarang && b.statusBarang.toLowerCase().includes(keyword)) ||
                    (b.hargaBarang && b.hargaBarang.toString().includes(keyword)) ||
                    (b.beratBarang && b.beratBarang.toString().includes(keyword))
                );
            })
        );
        displayBarang(filtered);
    });

    // Modal detail handler with carousel and thumbnails
    document.addEventListener("click", function (e) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        if (e.target.classList.contains("btn-detail")) {
            const button = e.target;
            document.getElementById("namaBarang").textContent = button.getAttribute("data-namaBarang");
            document.getElementById("beratBarang").textContent = button.getAttribute("data-beratBarang");
            document.getElementById("hargaBarang").textContent = button.getAttribute("data-hargaBarang");
            document.getElementById("kategori").textContent = button.getAttribute("data-kategori");
            document.getElementById("tanggalPenitipan").textContent = button.getAttribute("data-tanggalPenitipan");
            document.getElementById("tanggalPenitipanSelesai").textContent = button.getAttribute("data-tanggalPenitipanSelesai");
            document.getElementById("statusBarang").textContent = button.getAttribute("data-statusBarang");
            document.getElementById("tanggalTerjual").textContent = button.getAttribute("data-tanggalTerjual");
            currentIdTransaksiPenitipan = button.getAttribute("data-idtransaksipenitipan");

            const imageList = JSON.parse(button.getAttribute("data-images") || "[]");
            const carouselImages = document.getElementById("carouselImages");
            const carouselThumbnails = document.getElementById("carouselThumbnails");
            carouselImages.innerHTML = "";
            carouselThumbnails.innerHTML = "";

            if (imageList.length === 0) {
                carouselImages.innerHTML = `
                    <div class="carousel-item active">
                        <img src="/img/no-image.png" class="d-block carousel-image" alt="No Image" onclick="openFullscreenPreview('/img/no-image.png')">
                    </div>
                `;
            } else {
                imageList.forEach((img, idx) => {
                    const imgSrc = http://127.0.0.1:8000/${img};
                    carouselImages.innerHTML += `
                        <div class="carousel-item ${idx === 0 ? 'active' : ''}">
                            <img src="${imgSrc}" class="d-block carousel-image" alt="Barang Image ${idx+1}" onclick="openFullscreenPreview('${imgSrc}')">
                        </div>
                    `;
                });
                imageList.forEach((img, idx) => {
                    const imgSrc = http://127.0.0.1:8000/${img};
                    const thumbnail = document.createElement('img');
                    thumbnail.src = imgSrc;
                    thumbnail.className = thumbnail ${idx === 0 ? 'active' : ''};
                    thumbnail.alt = Thumbnail ${idx+1};
                    thumbnail.onclick = () => {
                        const carousel = bootstrap.Carousel.getInstance(document.getElementById('barangCarousel'));
                        carousel.to(idx);
                        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
                        thumbnail.classList.add('active');
                    };
                    carouselThumbnails.appendChild(thumbnail);
                });
                const carouselElement = document.getElementById('barangCarousel');
                carouselElement.addEventListener('slide.bs.carousel', function (e) {
                    const thumbnails = document.querySelectorAll('.thumbnail');
                    thumbnails.forEach(t => t.classList.remove('active'));
                    if (thumbnails[e.to]) {
                        thumbnails[e.to].classList.add('active');
                    }
                });
            }
        }

        // Perpanjang Penitipan Handler
        if (e.target.id === "btnPerpanjangPenitipan") {
            if (!currentIdTransaksiPenitipan) {
                alert("ID Transaksi tidak ditemukan.");
                return;
            }
            if (!confirm("Perpanjang tanggal penitipan selesai 30 hari dari tanggal saat ini?")) return;
            fetch(http://localhost:8000/api/perpanjang-penitipan/${currentIdTransaksiPenitipan}, {
                method: "POST",
                headers: {
                    "Authorization": Bearer ${token},
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    alert("Tanggal penitipan selesai berhasil diperpanjang!");
                    location.reload();
                } else {
                    alert(res.message || "Gagal memperpanjang penitipan.");
                }
            })
            .catch(() => alert("Terjadi kesalahan saat memperpanjang penitipan."));
        }
    });

    // Logout handler
    document.getElementById('logoutLink')?.addEventListener('click', function (e) {
        e.preventDefault();
        if (token) {
            fetch('http://localhost:8000/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                localStorage.removeItem('auth_token');
                window.location.href = '/';
            })
            .catch(() => {
                localStorage.removeItem('auth_token');
                window.location.href = '/';
            });
        } else {
            window.location.href = '/';
        }
    });
});
</script>
</body>
</html>