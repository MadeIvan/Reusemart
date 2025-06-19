<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Barang Penjadwalan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
      .modal-backdrop-custom {
        position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1040; display: none;
      }
      .modal-custom.show { display: block; }
      .border-dashed { border-style: dashed !important; }
      .modal-lg-custom { max-width: 700px }
      .modal-body legend { font-size: 1.25rem; }
      .form-section { border: 2px dashed #198754; padding: 1.2rem; border-radius: 12px; margin-bottom: 1.5rem; }
      .form-section-info { border-color: #0dcaf0; }
    </style>
</head>
<body class="bg-dark">
    @include('layouts.navbar')

    <div class="container mt-4 mb-5" style="max-width: 80%; margin-top: 10% !important; margin-left: 10 % !important;">
        <div class="bg-light rounded-4 p-4 shadow">
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <svg width="24" height="24" fill="none" stroke="gray" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input id="searchInput" type="text" class="form-control border-start-0" placeholder="Search..." autocomplete="off">
                </div>
            </div>
            <div id="barangList" class="d-flex flex-column gap-4"></div>
        </div>
    </div>

    <!-- Modal & Backdrop -->
    <div class="modal-backdrop-custom" id="modalBackdrop"></div>
    <div class="modal-custom modal fade" id="detailModal" tabindex="-1" aria-hidden="true" style="display:none; z-index:1050;">
      <div class="modal-dialog modal-dialog-centered modal-lg-custom">
        <div class="modal-content">
          <form id="jadwalForm" autocomplete="off">
            <div class="modal-header pb-0 border-0">
              <h3 class="modal-title w-100 fw-bold text-success">Informasi Transaksi</h3>
              <button type="button" class="btn-close" id="modalCloseBtn"></button>
            </div>
            <div class="modal-body pt-2">
              <!-- TRANSAKSI SECTION -->
              <fieldset class="form-section">
                <div class="row g-2 align-items-center">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">No Nota</label>
                    <input id="modalNoNota" type="text" class="form-control" disabled>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Waktu Pembelian</label>
                    <input id="modalWaktuPembelian" type="text" class="form-control" disabled>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Customer Services</label>
                    <input id="modalCustomerService" type="text" class="form-control" disabled>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Waktu Pelunasan</label>
                    <input id="modalWaktuPelunasan" type="text" class="form-control" disabled>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Pegawai Gudang</label>
                    <input id="modalPegawaiGudang" type="text" class="form-control" disabled>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <input id="modalStatus" type="text" class="form-control" disabled>
                  </div>
                  <div class="col-md-6 d-kurir">
                    <label class="form-label fw-semibold">Kurir</label>
                    <select id="selectKurir" name="selectKurir" class="form-select">
                      <option value="">Belum terpilih</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Kirim/Ambil</label>
                    <input id="modalTanggalKirim" name="tanggalKirim" type="date" class="form-control">
                  </div>
                </div>
              </fieldset>
              <!-- PEMBELI & BARANG SECTION -->
              <fieldset class="form-section form-section-info">
                <legend class="fw-bold text-info px-2 mb-2">Pembeli &amp; Barang</legend>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Pembeli</label>
                    <input id="modalNamaPembeli" type="text" class="form-control" disabled>
                  </div>
                  <div class="col-md-6 text-center" rowspan="5">
                    <img id="modalBarangImg" src="https://via.placeholder.com/160x120?text=No+Image" alt="gambar barang" class="rounded-2 border" style="width: 180px; height: 140px; object-fit:cover; margin-top:12px;">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Alamat</label>
                    <input id="modalAlamat" type="text" class="form-control" disabled>
                  </div>
                  <div class="col-md-6"></div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Barang</label>
                    <input id="modalNamaBarang" type="text" class="form-control" disabled>
                  </div>
                  <div class="col-md-6"></div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">ID Barang</label>
                    <input id="modalIDBarang" type="text" class="form-control" disabled>
                  </div>
                  <div class="col-md-6"></div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Berat Barang</label>
                    <input id="modalBeratBarang" type="text" class="form-control" disabled>
                  </div>
                </div>
              </fieldset>
              <div class="alert alert-warning small mt-2" id="infoTimeAlert" style="display:none;">
                Pengiriman untuk pembelian di atas jam 4 sore tidak bisa dijadwalkan di hari yang sama.
              </div>
            </div>
            <div class="modal-footer justify-content-end">
              <button type="button" class="btn btn-light" id="cancelBtn">Cancel</button>
              <button type="submit" class="btn btn-success ms-2">Simpan Penjadwalan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 2000">
      <div id="toastNotif" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body" id="toastNotifBody"></div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
    </div>

    <script>
      const apiUrl = "/api/barang-penjadwalan";
      const kurirUrl = "/api/pegawai-showkurir";
      let allData = [], kurirList = [], selectedItem = null;

      const barangList = document.getElementById('barangList');
      const searchInput = document.getElementById('searchInput');
      const modalBackdrop = document.getElementById('modalBackdrop');
      const detailModal = document.getElementById('detailModal');
      const jadwalForm = document.getElementById('jadwalForm');
      const toastNotif = document.getElementById('toastNotif');
      const toastNotifBody = document.getElementById('toastNotifBody');
      const modalCloseBtn = document.getElementById('modalCloseBtn');
      const cancelBtn = document.getElementById('cancelBtn');

      // Toast function
      function showToast(message, color="primary") {
        toastNotif.className = `toast align-items-center text-bg-${color} border-0`;
        toastNotifBody.textContent = message;
        const toast = new bootstrap.Toast(toastNotif);
        toast.show();
      }

      // Modal helpers
      function openModal() {
        modalBackdrop.style.display = "block";
        detailModal.classList.add('show');
        detailModal.style.display = "block";
        document.body.classList.add('modal-open');
      }
      function closeModal() {
        modalBackdrop.style.display = "none";
        detailModal.classList.remove('show');
        detailModal.style.display = "none";
        document.body.classList.remove('modal-open');
        selectedItem = null;
      }
      modalCloseBtn.onclick = closeModal;
      modalBackdrop.onclick = closeModal;
      cancelBtn.onclick = closeModal;

      // Utility to create badge (Bootstrap)
      function badge(text, color) {
        return `<span class="badge rounded-pill bg-${color} me-1 mb-1">${text}</span>`;
      }

      function renderBarangList(data) {
        barangList.innerHTML = '';
        if (data.length === 0) {
          barangList.innerHTML = '<div class="text-center text-muted fs-5">No items found.</div>';
          return;
        }
        data.forEach(item => {
          item.detail_transaksi_pembelian.forEach(detail => {
            const barang = detail.barang;
            if (!barang) return;

            let badgeGaransi = barang.garansiBarang == 1 ? badge('Garansi on', 'primary') : '';
            let badgeAvailable = (barang.statusBarang === 'Tersedia') ? badge('Available', 'success') : '';
            let badgeHunter = barang.haveHunter == 1 ? badge('Have hunter', 'warning') : '';

            const imgSrc = barang.image
              ? `/storage/images/${barang.image}`
              : 'https://via.placeholder.com/80x80?text=No+Image';

            const tgl = new Date(item.tanggalWaktuPembelian);
            const tglStr = `${tgl.getDate().toString().padStart(2, '0')} ${tgl.toLocaleString('default', { month: 'short' })} ${tgl.getFullYear()} ${tgl.getHours().toString().padStart(2, '0')}:${tgl.getMinutes().toString().padStart(2, '0')}:${tgl.getSeconds().toString().padStart(2, '0')}`;

            barangList.innerHTML += `
              <div class="card shadow-sm rounded-4 flex-row align-items-center p-3 barang-card" style="cursor:pointer;" data-nonota="${item.noNota}">
                <div class="flex-shrink-0">
                  <img src="${imgSrc}" alt="gambar ${barang.namaBarang}" class="rounded-3" style="width:80px;height:80px;object-fit:cover;">
                </div>
                <div class="flex-grow-1 ms-4">
                  <div class="d-flex align-items-center mb-2">
                    <h4 class="mb-0 fw-bold me-2">${barang.idBarang} | ${barang.namaBarang}</h4>
                    <span class="fs-6 text-muted">| ${tglStr}</span>
                  </div>
                  <div class="mb-1 text-secondary">Berat : <b>${barang.beratBarang} Kg</b></div>
                  <div class="mb-1 text-secondary">kategori : <b>${barang.kategori}</b></div>
                  <div class="mt-2">
                    ${badgeAvailable}${badgeHunter}${badgeGaransi}
                  </div>
                </div>
              </div>
            `;
          });
        });

        // Add card click handler
        Array.from(document.getElementsByClassName('barang-card')).forEach(card => {
          card.onclick = () => {
            const noNota = card.getAttribute('data-nonota');
            selectedItem = data.find(x => x.noNota === noNota);
            fillModalFields(selectedItem);
            openModal();
          };
        });
      }

      // Kurir fetch
      async function loadKurirList() {
        try {
          const res = await fetch(kurirUrl);
          const kurir = await res.json();
          kurirList = kurir.data || [];
        } catch (err) {
          kurirList = [];
        }
      }

      // Fill modal with selected data
      function fillModalFields(item) {
        const barang = item.detail_transaksi_pembelian[0].barang;
        const pembeli = item.pembeli || {};
        const alamat = item.idAlamat && pembeli.alamat
          ? pembeli.alamat.find(a => a.idAlamat === item.idAlamat)
          : null;

        // Transaksi section
        document.getElementById('modalNoNota').value = item.noNota || '';
        document.getElementById('modalWaktuPembelian').value = item.tanggalWaktuPembelian || '';
        document.getElementById('modalCustomerService').value = item.pegawai ? item.pegawai.namaPegawai : '';
        document.getElementById('modalWaktuPelunasan').value = item.tanggalWaktuPelunasan || '';
        document.getElementById('modalPegawaiGudang').value = item.pegawai2 ? item.pegawai2.namaPegawai : '';
        document.getElementById('modalStatus').value = item.status || '';
        document.getElementById('modalTanggalKirim').value = '';

        // Kurir
        const kurirDiv = document.querySelector('.d-kurir');
        const kurirSelect = document.getElementById('selectKurir');
        if (!alamat) {
          kurirDiv.style.display = "none";
        } else {
          kurirDiv.style.display = "";
          kurirSelect.innerHTML = `<option value="">Belum terpilih</option>` +
            kurirList.map(k => `<option value="${k.idPegawai}">${k.namaPegawai}</option>`).join('');
        }

        // Pembeli & barang section
        document.getElementById('modalNamaPembeli').value = pembeli.namaPembeli || '';
        document.getElementById('modalAlamat').value = alamat ? `${alamat.alamat} (${alamat.kategori})` : '';
        document.getElementById('modalNamaBarang').value = barang.namaBarang || '';
        document.getElementById('modalIDBarang').value = barang.idBarang || '';
        document.getElementById('modalBeratBarang').value = (barang.beratBarang ? barang.beratBarang + " Kg" : '');
        document.getElementById('modalBarangImg').src = barang.image
          ? `/storage/images/${barang.image}`
          : 'https://via.placeholder.com/160x120?text=No+Image';

        // Jam > 16 warning
        const tgl = new Date(item.tanggalWaktuPembelian);
        document.getElementById('infoTimeAlert').style.display = (tgl.getHours() >= 16 ? '' : 'none');
      }

jadwalForm.onsubmit = async function(e) {
  e.preventDefault();
  if (!selectedItem) return;

  const pembeli = selectedItem.pembeli || {};
  const alamat = selectedItem.idAlamat && pembeli.alamat
    ? pembeli.alamat.find(a => a.idAlamat === selectedItem.idAlamat)
    : null;

  // Get today's date at midnight for comparison
  const today = new Date();
  today.setHours(0,0,0,0);

  // Get the chosen schedule date from input
  const tanggalKirimStr = document.getElementById('modalTanggalKirim').value;
  if (!tanggalKirimStr) {
    showToast('Pilih tanggal kirim/ambil terlebih dahulu!', 'danger');
    return;
  }
  const tanggalKirim = new Date(tanggalKirimStr);
  tanggalKirim.setHours(0,0,0,0);

  // Purchase date and just the date part for comparison
  const purchaseDate = new Date(selectedItem.tanggalWaktuPembelian);
  const purchaseDateOnly = new Date(purchaseDate);
  purchaseDateOnly.setHours(0,0,0,0);

  // Disallow scheduling before today
  if (tanggalKirim < today) {
    showToast('Tanggal kirim/ambil tidak boleh sebelum hari ini!', 'danger');
    return;
  }

  // Disallow same-day scheduling if purchase time is after 4 PM
  if (
    tanggalKirim.getTime() === purchaseDateOnly.getTime() &&
    purchaseDate.getHours() >= 16
  ) {
    showToast('Pengiriman di atas jam 4 sore tidak bisa dijadwalkan di hari yang sama', 'danger');
    return;
  }

  // Prepare payload
  let statusBaru = '';
  let kurirNama = null;
  let idKurir = null;
  if (!alamat) {
    statusBaru = "Lunas Siap Diambil";
  } else {
    statusBaru = "Lunas Siap Diantarkan";
    const selectKurir = document.getElementById('selectKurir');
    if (!selectKurir.value) {
      showToast('Pilih kurir terlebih dahulu!', 'danger');
      return;
    }
    idKurir = selectKurir.value;
    const selectedKurir = kurirList.find(k => k.idPegawai === idKurir);
    kurirNama = selectedKurir ? selectedKurir.namaPegawai : null;
  }

  try {
    const res = await fetch(`/api/barang-penjadwalan/${selectedItem.noNota}/jadwal`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        status: statusBaru,
        idKurir: idKurir || null,
        kurirNama: kurirNama || null,
        tanggalKirim: tanggalKirimStr
      })
    });
    if (!res.ok) throw new Error("Gagal menyimpan jadwal");
    showToast("Berhasil menjadwalkan barang!", "success");
    closeModal();
    loadData();
  } catch (err) {
    showToast("Gagal menjadwalkan barang", "danger");
  }
};



      // Fetch JSON data from API
      async function loadData() {
        try {
          const res = await fetch(apiUrl);
          allData = await res.json();
          renderBarangList(allData);
        } catch (err) {
          barangList.innerHTML = '<div class="text-danger text-center">Failed to load data.</div>';
        }
      }

      // Load kurir list first, then load data
      async function initAll() {
        await loadKurirList();
        await loadData();
      }

      // Search/filter
      // Enhanced search/filter
searchInput.addEventListener('input', () => {
  const keyword = searchInput.value.trim().toLowerCase();

  const filtered = allData.filter(item => {
    // Collect all relevant data from transaksi (item) and detail barang
    let fields = [
      item.noNota,
      item.status,
      item.tanggalWaktuPembelian,
      item.tanggalWaktuPelunasan,
      item.pegawai && item.pegawai.namaPegawai,
      item.pegawai2 && item.pegawai2.namaPegawai,
      item.pembeli && item.pembeli.namaPembeli,
      item.pembeli && item.pembeli.email,
    ];

    // Add alamat
    if (item.pembeli && Array.isArray(item.pembeli.alamat)) {
      item.pembeli.alamat.forEach(a => {
        fields.push(a.alamat);
        fields.push(a.kategori);
      });
    }

    // Add detail transaksi pembelian fields
    if (Array.isArray(item.detail_transaksi_pembelian)) {
      item.detail_transaksi_pembelian.forEach(detail => {
        const barang = detail.barang || {};
        fields.push(barang.idBarang, barang.namaBarang, barang.kategori, barang.statusBarang);
      });
    }

    // Combine all fields to a single string, lowercase, for easier searching
    const fullText = fields.filter(Boolean).join(' ').toLowerCase();
    return fullText.includes(keyword);
  });

  renderBarangList(filtered);
});


      // Initial load
      initAll();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
