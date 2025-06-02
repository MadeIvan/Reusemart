<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reusemart</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- Bootstrap ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (!localStorage.getItem("auth_token")) {
                window.location.href = "{{ url('/UsersLogin') }}";
            }
        });
    </script>
</head>

<body>
    @include('layouts.navbar')

    <div class="container mt-5 mb-5" style="max-width: 700px;">
        <h2 class="card-title mb-3 text-center"><strong>Pembayaran</strong></h2>
        <div class="card mt-3" id="cardPembayaran">
            <div class="card-body">
                <p>Loading...</p>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center p-3">
        <p>&copy; 2025 Reusemart</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const auth_token = localStorage.getItem('auth_token');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let noNotaUpdated = null;
            let batasBayarGlobal = null;
            let barangArray = [];

            fetch(`http://127.0.0.1:8000/api/getData`, {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${auth_token}`,
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.data) {
                    const cardBody = document.querySelector("#cardPembayaran .card-body");
                    const waktuPembelian = new Date(data.data.tanggalWaktuPembelian);
                    const batasBayar = new Date(waktuPembelian.getTime() + 15000); // 60 detik
                    // const batasBayar = new Date(waktuPembelian.getTime() + 900000);
                    batasBayarGlobal = batasBayar;
                    noNotaUpdated = data.data.noNota;

                    const batasBayarFormatted = batasBayar.toLocaleString('id-ID', {
                        day: 'numeric', month: 'long', year: 'numeric',
                        hour: '2-digit', minute: '2-digit', hour12: false
                    });

                    const totalTagihan = Number(data.data.totalHarga).toLocaleString('id-ID');

                    const pembayaranDetail = `
                        <form id="uploadForm" enctype="multipart/form-data">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-9">
                                    <h4 class="text-black mb-1"><strong>Bayar Sebelum</strong></h4>
                                    <p class="mb-0">${batasBayarFormatted}</p>
                                </div>
                                <div class="col-md-3 text-md-end text-center">
                                    <p class="text-success mb-0" id="countdown">--</p>
                                </div>
                            </div>
                            <hr>

                            <div class="row align-items-center mb-3">
                                <div class="col-md-9">
                                    <h5 class="text-black mb-2"><strong>Nomor Rekening Reusemart</strong></h5>
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="{{ asset('Logo-Bank-BCA-1.png') }}" alt="Logo BCA" class="img-fluid me-2" style="width: 50px;">
                                        <h5 class="mb-0">Bank BCA</h5>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <p id="noRek" class="text-success mb-0 me-2 user-select-all" style="font-size: 1.5rem;"><strong>061 555 2323</strong></p>
                                        <button class="btn btn-outline-primary btn-sm" type="button" onclick="copyRekening()">Salin</button>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="row align-items-center mb-3">
                                <div class="col-md-9">
                                    <h5 class="text-black mb-0"><strong>Total Tagihan</strong></h5>
                                </div>
                                <div class="col-md-3 text-md-end text-center">
                                    <h5 class="text-success mb-0">Rp ${totalTagihan}</h5>
                                </div>
                            </div>
                            <hr>

                            <div class="row align-items-center mb-3">
                                <div class="col-md-9">
                                    <h5 class="text-black mb-2"><strong>Bukti Pembayaran</strong></h5>
                                    <div class="input-group">
                                        <input type="file" class="form-control" id="inputGroupFile02" name="buktiPembayaran">
                                    </div>
                                </div>
                            </div>

                            <div class="mx-3 mb-2 text-end">
                                <button class="btn btn-success" type="button" id="pembayaran-btn">Kirim Bukti Pembayaran</button>
                            </div>
                        </form>
                    `;

                    cardBody.innerHTML = pembayaranDetail;

                    startCountdown(batasBayar);

                    document.getElementById("pembayaran-btn").addEventListener("click", function () {
                        event.preventDefault();

                        const fileInput = document.getElementById("inputGroupFile02");
                        const file = fileInput.files[0];

                        const now = new Date();
                        if (batasBayarGlobal && now > batasBayarGlobal) {
                            Toastify({
                                text: "Waktu pembayaran sudah habis!",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                style: { background: "#e74c3c" },
                            }).showToast();
                            return; 
                        }

                        if (!file) {
                            Toastify({
                                text: "Bukti pembayaran wajib diunggah!",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                style: { background: "#e74c3c" },
                            }).showToast();
                            return;
                        }

                        const formData = new FormData();
                        formData.append("tanggalWaktuPelunasan", getCurrentDateTime());
                        formData.append("buktiPembayaran", file);

                        fetch(`http://127.0.0.1:8000/api/buktiBayar/${noNotaUpdated}`, {
                            method: "POST",
                            headers: {
                                "Authorization": `Bearer ${auth_token}`,
                                "X-CSRF-TOKEN": csrfToken,
                                "Accept": "application/json"
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(res => {
                            if (res.status) {
                                Toastify({
                                    text: "Berhasil mengirim bukti pembayaran!",
                                    duration: 3000,
                                    gravity: "top",
                                    position: "right",
                                    style: { background: "#8bc34a" },
                                }).showToast();
                                window.location.href = `{{ url('/home') }}`;

                                localStorage.removeItem('alamatPengiriman');
                                localStorage.removeItem('data_checkout');
                                localStorage.removeItem('metodeAnterAmbil');
                                localStorage.removeItem('poinUser');
                                localStorage.removeItem('selectedIdAlamat');
                                localStorage.removeItem('sisaPoin');
                                localStorage.removeItem('totalSetelahPoin');

                            } else {
                                alert("Gagal mengirim bukti: " + res.message);
                            }
                        })
                        .catch(err => {
                            console.error("Error:", err);
                            Toastify({
                                text: "Terjadi kesalahan saat mengirim bukti!",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                style: { background: "#e74c3c" },
                            }).showToast();
                        });
                    });
                } else {
                    document.querySelector("#cardPembayaran .card-body").innerHTML = `<p class="text-center text-muted">Belum ada transaksi yang perlu dibayar.</p>`;
                }
            })
            .catch(error => {
                console.error("Error fetching data:", error);
            });

            function getCurrentDateTime() {
                const now = new Date();
                return now.getFullYear() + "-" +
                    String(now.getMonth() + 1).padStart(2, '0') + "-" +
                    String(now.getDate()).padStart(2, '0') + " " +
                    String(now.getHours()).padStart(2, '0') + ":" +
                    String(now.getMinutes()).padStart(2, '0') + ":" +
                    String(now.getSeconds()).padStart(2, '0');
            }

            function startCountdown(endTime) {
                const countdownEl = document.getElementById("countdown");
                const pembayaranBtn = document.getElementById("pembayaran-btn");
                
                
                function updateCountdown() {
                    const now = new Date();
                    const timeLeft = new Date(endTime) - now;
                    
                    if (timeLeft <= 0) {
                        countdownEl.innerText = "Waktu habis!";
                        pembayaranBtn.disabled = true;
                        pembayaranBtn.classList.add("btn-secondary");
                        pembayaranBtn.classList.remove("btn-success"); 
                        batalBeli();
                        clearInterval(intervalId);
                        return;
                    }
                    
                    const minutes = Math.floor((timeLeft / 1000 / 60) % 60);
                    const seconds = Math.floor((timeLeft / 1000) % 60);
                    countdownEl.innerText = `${minutes}m ${seconds}s`;
                }
                
                function batalBeli(){
                    const checkoutData = JSON.parse(localStorage.getItem('data_checkout'));
                    let barangArray = [];

                    if (checkoutData && checkoutData.barang) {
                        barangArray = checkoutData.barang.map(item => item.idBarang);
                    }

                    const poinAwal = localStorage.getItem('poinUser');
                    console.log("Barang:", barangArray);
                    console.log("Poin:", poinAwal);

                    fetch(`http://127.0.0.1:8000/api/batalkanPesanan/${noNotaUpdated}`, {
                        method: "POST",
                        headers: {
                            "Authorization": `Bearer ${auth_token}`,
                            "X-CSRF-TOKEN": csrfToken,
                            "Accept": "application/json",
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            id_barang: barangArray,
                            poinAwal: localStorage.getItem('poinUser'),
                            status: "Dibatalkan (Tidak Dibayar)",

                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log("Response:", data);
                        window.location.href = `{{ url('/home') }}`;
                        localStorage.removeItem('alamatPengiriman');
                        localStorage.removeItem('data_checkout');
                        localStorage.removeItem('metodeAnterAmbil');
                        localStorage.removeItem('poinUser');
                        localStorage.removeItem('selectedIdAlamat');
                        localStorage.removeItem('sisaPoin');
                        localStorage.removeItem('totalSetelahPoin');
                    })
                    .catch(err => {
                        console.error("Batal beli error:", err);
                    });
                }

                const intervalId = setInterval(updateCountdown, 1000);
                updateCountdown();
            }
        });

        function copyRekening() {
            const text = document.getElementById("noRek").innerText;
            navigator.clipboard.writeText(text)
                .then(() => {
                    Toastify({
                        text: "Nomor rekening disalin!",
                        duration: 2000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#8bc34a"
                    }).showToast();
                })
                .catch(err => console.error("Gagal menyalin teks:", err));
        }
    </script>
</body>
</html>
