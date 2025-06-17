<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reusemart</title>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (!localStorage.getItem("auth_token")) {
                window.location.href = "{{ url('/UsersLogin') }}";
            }
        });
    </script>

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
    <!-- Toastify ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">



    <style>
        body{
            background:rgb(243, 243, 243);    
        }

        #inputSearch {
            margin-bottom: 0px;
        }

        .container {
            margin-top: 0 !important;  
        }

        .card {
            margin-bottom: 10px;
        }

        .d-flex {
            gap: 10px;
        }

    </style>
</head>

<body>
    

    <!-- ////////////////////INI MODAL DELETE///////////////////////////// -->
    <div class="modal fade" id="deleteAlamat" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="exampleModalLabel">Hapus Alamat</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus alamat ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ////////////////////INI MODAL UPDATE///////////////////////////// -->
    <div class="modal fade" id="updateAlamat" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="exampleModalLabel"><strong>Update Alamat</strong></h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kategoriAlamat" class="form-label"><strong>Kategori</strong></label>
                        <input type="text" class="form-control" id="kategoriAlamat" name="kategoriAlamat" required>
                    </div>
                    <div class="mb-3">
                        <label for="namaAlamat" class="form-label"><strong>Nama</strong></label>
                        <input type="text" class="form-control" id="namaAlamat" name="namaAlamat" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label"><strong> Alamat</strong></label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmUpdate">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ////////////////////INI MODAL CREATE///////////////////////////// -->
    <div class="modal fade" id="createAlamat" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="exampleModalLabel"><strong>Buat Alamat</strong></h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kategoriAlamatCreate" class="form-label"><strong>Kategori</strong></label>
                        <select class="form-control" id="kategoriAlamatCreate" name="kategoriAlamatCreate" required>
                            <option value="" disabled selected>Pilih kategori</option>
                            <option value="Rumah">Rumah</option>
                            <option value="Kantor">Kantor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="namaCreate" class="form-label"><strong> Nama</strong></label>
                        <input class="form-control" id="namaCreate" name="namaCreate" required></input>
                    </div>
                    <div class="mb-3">
                        <label for="alamatCreate" class="form-label"><strong> Alamat</strong></label>
                        <textarea class="form-control" id="alamatCreate" name="alamatCreate" rows="3" placeholder="Nama Jalan, Gedung, Nomor Rumah, RT, RW" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="ProvinsiAlamatCreate" class="form-label"><strong>Provinsi</strong></label>
                        <select class="form-control" id="ProvinsiAlamatCreate" name="ProvinsiAlamatCreate" required disabled>
                            <option value="" disabled selected>Daerah Istimewa Yogyakarta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kategoriAlamatCreate" class="form-label"><strong>Kabupaten</strong></label>
                        <select class="form-control" id="kotaAlamatCreate" name="kotaAlamatCreate" required>
                            <option value="" disabled selected>Pilih Kota</option>
                            <option value="bantul">Kab. Bantul</option>
                            <option value="sleman">Kab. Sleman</option>
                            <option value="kulprog">Kab. Kulon Progo</option>
                            <option value="gunkid">Kab. Gunung Kidul</option>
                            <option value="yk">Kota Yogyakarta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kecamatanAlamatCreate" class="form-label"><strong>Kecamatan</strong></label>
                        <select class="form-control" id="kecamatanAlamatCreate" name="kecamatanAlamatCreate" required>
                            <option value="" disabled selected>Pilih Kecamatan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kelurahanAlamatCreate" class="form-label"><strong>Kelurahan</strong></label>
                        <select class="form-control" id="kelurahanAlamatCreate" name="kelurahanAlamatCreate" required>
                            <option value="" disabled selected>Pilih Kelurahan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="kodePosAlamatCreate" class="form-label"><strong>Kode Pos</strong></label>
                        <input class="form-control" id="kodePosAlamatCreate" name="kodePosAlamatCreate" required></input>
                    </div>
                    <div class="mb-3">
                        <input class="form-check-input" type="checkbox" id="isDefault" aria-label="Checkbox for Atur Sebagai Alamat Utama">
                        <label class="form-check-label" for="checkboxUtama">
                            Atur Sebagai Alamat Utama
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmCreate">Tambah</button>
                </div>
            </div>
        </div>
    </div>

     <!-- <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav"> -->
                <!-- Nav-bar kiri -->
                <!-- <ul class="navbar-nav me-auto">
                    <li class="nav-item d-flex align-items-center">
                        <img src="{{ asset('logoReUseMart.png') }}" alt="Logo Reusemart" style="width:50px;">
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <a class="nav-link text-black" href="{{url('/home')}}">
                            <strong>Produk</strong>
                        </a>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <a class="nav-link text-black" href="{{url('/pembeli/alamat')}}">
                            <strong>Alamat</strong>
                        </a>
                    </li>
                </ul> -->

                <!-- Nav-bar kanan -->
                <!-- <ul class="navbar-nav ms-auto mb-2 mb-lg-0 profile-menu"> 
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="profile-pic d-inline">
                                <img src="{{ asset('img/pp.png') }}" alt="Profile Picture" style="width:35px;" class="rounded-circle">
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" id="logoutLink"><i class="fas fa-sign-out-alt fa-fw" ></i> Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <hr style="margin: 0; border: 2px solid #dee2e6;"/> -->
    @include('layouts.navbar')

    
    <!-- ////////////////////INI ISI///////////////////////////// -->
    <div class="container py-4">
        <h2 class="mb-4">Daftar Alamat</h2>
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <input class="form-control me-2 flex-grow-1" name="name" id="searchInput" type="text" placeholder="Search" aria-label="Search"style="width: 200px;" >
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createAlamat" type="submit" id="tambahButton">
                <i class="bi bi-plus-square-fill me-2"></i>Tambah Alamat
            </button>
        </div>
    </div>

    <div class="container mt-2">
        <div class="row flex-column" id="alamatContainer">
            <!-- NANTI DISINI -->
        </div>
    </div>

    <footer class="bg-dark text-white text-center p-3">
        <p>&copy; 2025 Reusemart</p>
    </footer>


    <script>
        document.addEventListener("DOMContentLoaded", function(){
            const auth_token = localStorage.getItem('auth_token');
            if (!localStorage.getItem("auth_token")) {
            window.location.href = "/UsersLogin";
            }
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Get CSRF token from meta tag

            const alamatContainer = document.getElementById("alamatContainer");
            const searchInput = document.getElementById("searchInput");
            let alamatData = [];
            let idToDelete = null;
            let idToUpdate = null;
            let idToDefault = null;
            
          
            fetchAlamat();

            /* =========================
            1. KECAMATAN / KAPANEWON
            =========================*/
            const kecamatanData = {
                bantul: [
                    "Bambanglipuro", "Banguntapan", "Bantul", "Dlingo", "Imogiri", "Jetis", "Kasihan",
                    "Kretek", "Pajangan", "Pandak", "Piyungan", "Pleret", "Pundong", "Sanden",
                    "Sedayu", "Sewon", "Srandakan"
                ],

                sleman: [
                    "Berbah", "Cangkringan", "Depok", "Gamping", "Godean", "Kalasan", "Minggir", "Mlati",
                    "Moyudan", "Ngaglik", "Ngemplak", "Pakem", "Prambanan", "Seyegan", "Sleman",
                    "Tempel", "Turi"
                ],

                kulprog: [
                    "Galur", "Girimulyo", "Kalibawang", "Kokap", "Lendah", "Nanggulan", "Panjatan",
                    "Pengasih", "Samigaluh", "Sentolo", "Temon", "Wates"
                ],

                gunkid: [
                    "Gedangsari", "Girisubo", "Karangmojo", "Ngawen", "Nglipar", "Paliyan", "Panggang",
                    "Patuk", "Playen", "Ponjong", "Purwosari", "Rongkop", "Saptosari", "Semanu",
                    "Semin", "Tanjungsari", "Tepus", "Wonosari"
                ],

                yk: [
                    "Danurejan", "Gedongtengen", "Gondokusuman", "Gondomanan", "Jetis", "Kotagede",
                    "Kraton", "Mantrijeron", "Mergangsan", "Ngampilan", "Pakualaman", "Tegalrejo",
                    "Umbulharjo", "Wirobrajan"
                ]
            };


            /* =====================
            2. KELURAHAN PER KEC.
            =====================*/
            const kelurahanData = {
                bantul: {
                    Bambanglipuro: ["Mulyodadi", "Sidomulyo", "Sumbermulyo"],
                    Banguntapan: ["Banguntapan", "Baturetno", "Jagalan", "Jambidan", "Potorono", "Singosaren", "Tamanan", "Wirokerten"],
                    Bantul: ["Bantul", "Palbapang", "Ringinharjo", "Sabdodadi", "Trirenggo"],
                    Dlingo: ["Dlingo", "Jatimulyo", "Mangunan", "Muntuk", "Temuwuh", "Terong"],
                    Imogiri: ["Girirejo", "Imogiri", "Karangtalun", "Karangtengah", "Kebonagung", "Selopamioro", "Sriharjo", "Wukirsari"],
                    Jetis: ["Canden", "Patalan", "Sumberagung", "Trimulyo"],
                    Kasihan: ["Bangunjiwo", "Ngestiharjo", "Tamantirto", "Tirtonirmolo"],
                    Kretek: ["Donotirto", "Parangtritis", "Tirtohargo", "Tirtomulyo", "Tirtosari"],
                    Pajangan: ["Guwosari", "Sendangsari", "Triwidadi"],
                    Pandak: ["Caturharjo", "Gilangharjo", "Triharjo", "Wijirejo"],
                    Piyungan: ["Srimulyo", "Sitimulyo", "Srimartani"],
                    Pleret: ["Bawuran", "Pleret", "Segoroyoso", "Wonokromo", "Wonolelo"],
                    Pundong: ["Panjangrejo", "Seloharjo", "Srihardono"],
                    Sanden: ["Gadingsari", "Gadingharjo", "Murtigading", "Srigading"],
                    Sedayu: ["Argodadi", "Argorejo", "Argosari", "Argomulyo"],
                    Sewon: ["Bangunharjo", "Panggungharjo", "Pendowoharjo", "Timbulharjo"],
                    Srandakan: ["Poncosari", "Trimurti"]
                },

                sleman: {
                    Berbah: ["Jogotirto", "Kalitirto", "Sendangtirto", "Tegaltirto"],
                    Cangkringan: ["Argomulyo", "Glagaharjo", "Kepuharjo", "Wukirsari", "Umbulharjo"],
                    Depok: ["Caturtunggal", "Condongcatur", "Maguwoharjo"],
                    Gamping: ["Ambarketawang", "Balecatur", "Banyuraden", "Nogotirto", "Trihanggo"],
                    Godean: ["Sidoagung", "Sidoarum", "Sidokarto", "Sidoluhur", "Sidomoyo", "Sidomulyo", "Sidorejo"],
                    Kalasan: ["Purwomartani", "Selomartani", "Tamanmartani", "Tirtomartani"],
                    Minggir: ["Sendangagung", "Sendangarum", "Sendangmulyo", "Sendangrejo", "Sendangsari"],
                    Mlati: ["Sendangadi", "Sinduadi", "Sumberadi", "Tirtoadi", "Tlogoadi"],
                    Moyudan: ["Sumberagung", "Sumberarum", "Sumberrahayu", "Sumbersari"],
                    Ngaglik: ["Donoharjo", "Minomartani", "Sardonoharjo", "Sariharjo", "Sinduharjo", "Sukoharjo"],
                    Ngemplak: ["Bimomartani", "Sindumartani", "Umbulmartani", "Wedomartani", "Widodomartani"],
                    Pakem: ["Candibinangun", "Hargobinangun", "Harjobinangun", "Pakembinangun", "Purwobinangun"],
                    Prambanan: ["Bokoharjo", "Gayamharjo", "Madurejo", "Sambirejo", "Sumberharjo", "Wukirharjo"],
                    Seyegan: ["Margoagung", "Margodadi", "Margokaton", "Margoluwih", "Margomulyo"],
                    Sleman: ["Caturharjo", "Pandowoharjo", "Tridadi", "Triharjo", "Trimulyo"],
                    Tempel: ["Banyurejo", "Lumbungrejo", "Margorejo", "Merdikorejo", "Mororejo", "Pondokrejo", "Sumberejo", "Tambakrejo"],
                    Turi: ["Bangunkerto", "Donokerto", "Girikerto", "Wonokerto"]
                },

                kulprog: {
                    Galur: ["Banaran", "Brosot", "Karangsewu", "Kranggan", "Nomporejo", "Pandowan", "Tirtarahayu"],
                    Girimulyo: ["Giripurwo", "Jatimulyo", "Pendoworejo", "Purwosari"],
                    Kalibawang: ["Banjararum", "Banjarasri", "Banjarharjo", "Banjaroyo"],
                    Kokap: ["Hargomulyo", "Hargorejo", "Hargotirto", "Hargowilis", "Kalirejo"],
                    Lendah: ["Bumirejo", "Gulurejo", "Jatirejo", "Ngentakrejo", "Sidorejo", "Wahyuharjo"],
                    Nanggulan: ["Banyuroto", "Kembang", "Donomulyo", "Jatisarono", "Tanjungharjo", "Wijimulyo"],
                    Panjatan: ["Bojong", "Bugel", "Cerme", "Depok", "Garongan", "Gotakan", "Kanoman", "Krembangan", "Panjatan", "Pleret", "Tayuban"],
                    Pengasih: ["Karangsari", "Kedungsari", "Margosari", "Pengasih", "Sidomulyo", "Sendangsari", "Tawangsari"],
                    Samigaluh: ["Banjarsari", "Gerbosari", "Kebonharjo", "Ngargosari", "Pagerharjo", "Purwoharjo", "Sidoharjo"],
                    Sentolo: ["Banguncipto", "Demangrejo", "Kaliagung", "Salamrejo", "Sentolo", "Srikayangan", "Sukoreno", "Tuksono"],
                    Temon: ["Demen", "Glagah", "Jangkaran", "Janten", "Kalidengen", "Kaligintung", "Karangwuluh", "Kebonrejo", "Kedundang", "Kulur", "Palihan", "Plumbon", "Sindutan", "Temonkulon", "Temonwetan"],
                    Wates: ["Bendungan", "Giripeni", "Karangwuni", "Kulwaru", "Ngestiharjo", "Sogan", "Triharjo"]
                },

                gunkid: {
                    Gedangsari: ["Hargomulyo", "Mertelu", "Ngalang", "Sampang", "Serut", "Tegalrejo", "Watugajah"],
                    Girisubo: ["Balong", "Jepitu", "Karangawen", "Jerukwudel", "Pucung", "Songbanyu", "Nglindur", "Tileng"],
                    Karangmojo: ["Bendungan", "Bejiharjo", "Gedangrejo", "Jatiayu", "Karangmojo", "Kelor", "Ngawis", "Ngipak", "Wiladeg"],
                    Ngawen: ["Beji", "Jurangjero", "Kampung", "Sambirejo", "Tancep", "Watusigar"],
                    Nglipar: ["Katongan", "Kedungkeris", "Kedungpoh", "Natah", "Nglipar", "Pengkol", "Pilangrejo"],
                    Paliyan: ["Giring", "Grogol", "Karangasem", "Karangduwet", "Mulusan", "Pampang", "Sodo"],
                    Panggang: ["Giriharjo", "Girikarto", "Girimulyo", "Girisekar", "Girisuko", "Giriwungu"],
                    Patuk: ["Beji", "Bunder", "Nglanggeran", "Nglegi", "Ngoro-oro", "Patuk", "Pengkok", "Putat", "Salam", "Semoyo", "Terbah"],
                    Playen: ["Banaran", "Bandung", "Banyusoco", "Bleberan", "Dengok", "Gading", "Getas", "Logandeng", "Ngawu", "Ngleri", "Ngunut", "Playen", "Plembutan"],
                    Ponjong: ["Bedoyo", "Genjahan", "Gombang", "Karangasem", "Kenteng", "Ponjong", "Sawahan", "Sidorejo", "Sumbergiri", "Tambakromo", "Umbulrejo"],
                    Purwosari: ["Giriasih", "Giricahyo", "Girijatih", "Giripurwo", "Giritirto"],
                    Rongkop: ["Botodayaan", "Bohol", "Karangwuni", "Melikan", "Petir", "Pringombo", "Pucanganom", "Semugih"],
                    Saptosari: ["Jetis", "Kanigoro", "Kepek", "Krambilsawit", "Monggol", "Ngloro", "Planjan"],
                    Semanu: ["Candirejo", "Dadapayu", "Ngeposari", "Pacarejo", "Semanu"],
                    Semin: ["Bendung", "Bulurejo", "Candirejo", "Kalitekuk", "Karangsari", "Kemejing", "Pundungsari", "Rejosari", "Semin", "Sumberejo"],
                    Tanjungsari: ["Banjarejo", "Hargosari", "Kemadang", "Kemiri", "Ngestirejo"],
                    Tepus: ["Giripanggung", "Purwodadi", "Sidoharjo", "Sumberwungu", "Tepus"],
                    Wonosari: ["Baleharjo", "Duwet", "Gari", "Karangtengah", "Karangrejek", "Kepek", "Mulo", "Piyaman", "Pulutan", "Selang", "Siraman", "Wareng", "Wonosari", "Wunung"]
                },

                yk: {
                    Danurejan: ["Bausasran", "Tegalpanggung", "Suryatmajan"],
                    Gedongtengen: ["Pringgokusuman", "Sosromenduran"],
                    Gondokusuman: ["Baciro", "Demangan", "Klitren", "Kotabaru", "Terban"],
                    Gondomanan: ["Ngupasan", "Prawirodirjan"],
                    Jetis: ["Bumijo", "Cokrodiningratan", "Gowongan"],
                    Kotagede: ["Prenggan", "Purbayan", "Rejowinangun"],
                    Kraton: ["Panembahan", "Kadipaten", "Patehan"],
                    Mantrijeron: ["Gedongkiwo", "Suryodiningratan", "Mantrijeron"],
                    Mergangsan: ["Brontokusuman", "Keparakan", "Wirogunan"],
                    Ngampilan: ["Ngampilan", "Notoprajan"],
                    Pakualaman: ["Gunungketur", "Purwokinanti"],
                    Tegalrejo: ["Bener", "Karangwaru", "Kricak", "Tegalrejo"],
                    Umbulharjo: ["Pandeyan", "Sorosutan", "Giwangan", "Warungboto", "Mujamuju", "Semaki", "Tahunan"],
                    Wirobrajan: ["Pakuncen", "Patangpuluhan", "Wirobrajan"]
                }
            };




            const kotaSelect = document.getElementById('kotaAlamatCreate');
            const kecamatanSelect = document.getElementById('kecamatanAlamatCreate');
            const kelurahanSelect = document.getElementById('kelurahanAlamatCreate');

            // Inisialisasi dropdown dalam kondisi disabled
            kecamatanSelect.disabled = true;
            kelurahanSelect.disabled = true;

            kotaSelect.addEventListener('change', function () {
                const selectedKota = this.value;

                // Reset isi kecamatan dan kelurahan
                kecamatanSelect.innerHTML = '<option value="" disabled selected>Pilih Kecamatan</option>';
                kelurahanSelect.innerHTML = '<option value="" disabled selected>Pilih Kelurahan</option>';
                kelurahanSelect.disabled = true;

                if (selectedKota && kecamatanData[selectedKota]) {
                    kecamatanData[selectedKota].forEach(kecamatan => {
                        const option = document.createElement('option');
                        option.value = kecamatan; // gunakan original name
                        option.textContent = kecamatan;
                        kecamatanSelect.appendChild(option);
                    });
                    kecamatanSelect.disabled = false;
                } else {
                    kecamatanSelect.disabled = true;
                }
            });

            kecamatanSelect.addEventListener('change', function () {
                const selectedKota = kotaSelect.value;
                const selectedKecamatan = this.value;

                // Reset isi kelurahan
                kelurahanSelect.innerHTML = '<option value="" disabled selected>Pilih Kelurahan</option>';

                if (
                    selectedKota &&
                    selectedKecamatan &&
                    kelurahanData[selectedKota] &&
                    kelurahanData[selectedKota][selectedKecamatan]
                ) {
                    kelurahanData[selectedKota][selectedKecamatan].forEach(kelurahan => {
                        const option = document.createElement('option');
                        option.value = kelurahan;
                        option.textContent = kelurahan;
                        kelurahanSelect.appendChild(option);
                    });
                    kelurahanSelect.disabled = false;
                } else {
                    kelurahanSelect.disabled = true;
                }
            });



            ////////////////////////SHOW ALAMAT//////////////////////////////////;

            function fetchAlamat(){
                const token = localStorage.getItem('auth_token');
                if (!token) {
                    console.error("No auth token found. Please log in first.");
                    return;
                }
                fetch(`http://127.0.0.1:8000/api/pembeli/alamat`, {
                    method: "GET",
                    headers: {
                        "Authorization": `Bearer ${token}`,
                        'Accept': 'application/json',
                        "Content-Type": "application/json",
                    },
                })
                .then(response => response.json())
                .then(data => {
                    alamatData = data.data;
                    renderAlamat(data.data);
                })
                .catch(error => console.error("Error fetching alamat:", error));
            }

            ////////////////////////CARD ALAMAT///////////////////////////////////
            function renderAlamat(data){
                // console.log("Rendering data:", data);
                alamatContainer.innerHTML = "";
                data.forEach(alamat => {
                    const isDefaultLabel = alamat.isDefault == true 
                        ? `<h6 class="card-subtitle mb-0 text-success" style="font-size: 0.9rem;">Utama</h6>` 
                        : '';
                    
                    const isDisabled = alamat.isDefault == true 
                        ? 'disabled' 
                        : '';

                    const card = `
                    <div class="col-12 ">
                        <div class="card">
                            <div class="card-body position-relative">
                                <div class="position-absolute top-0 end-0 mt-2 me-3">
                                    <div class="d-flex justify-content-end mb-2"" >
                                    <a href="#" class="card-link me-2 text-decoration-none update-link" data-id="${alamat.idAlamat}"
                                        data-alamat="${alamat.alamat}" data-kategoriAlamat="${alamat.kategori}"
                                        data-namaAlamat="${alamat.nama}"
                                        data-bs-toggle="modal" data-bs-target="#updateAlamat">Ubah</a>
                                    <a href="#" class="card-link text-danger text-decoration-none delete-link" data-id="${alamat.idAlamat}"
                                        data-bs-toggle="modal" data-bs-target="#deleteAlamat">Hapus</a>
                                    </div>
                                    <button type="button" class="btn btn-outline-success btn-sm" 
                                        id="confirmDefault" ${isDisabled} data-id="${alamat.idAlamat}">
                                        Atur Sebagai Alamat Utama
                                    </button>
                                </div>
                                
                                <!-- Alamat content -->
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="card-title mt-2"><strong>${alamat.kategori}</strong></h5>
                                    ${isDefaultLabel}
                                </div>
                                <p class="card-subtitle mt-2"><strong>${alamat.nama}</strong></p>
                                <p class="card-subtitle text-body-secondary mt-2">${alamat.alamat}</p>
                            </div>
                        </div>
                    </div>
                    `;
                    alamatContainer.innerHTML += card;
                });

                document.querySelectorAll(".delete-link").forEach(link => {
                    link.addEventListener("click", function () {
                        idToDelete = this.getAttribute("data-id");
                    });
                });

                document.querySelectorAll(".update-link").forEach(link => {
                    link.addEventListener("click", function () {
                        idToUpdate = this.getAttribute("data-id");
                        const kategori = this.getAttribute("data-kategoriAlamat");
                        const alamat = this.getAttribute("data-alamat");
                        const nama = this.getAttribute("data-namaAlamat");

                        document.getElementById("kategoriAlamat").value = kategori;
                        document.getElementById("alamat").value = alamat;
                        document.getElementById("namaAlamat").value = nama;
                    });
                    
                });

                document.querySelectorAll(".btn-outline-success").forEach(button => {
                    button.addEventListener("click", function () {
                        idToDefault = this.getAttribute("data-id");
                    });
                });

            }
            
            searchInput.addEventListener("input", () => {
                const query = searchInput.value.toLowerCase();
                fetch(`http://127.0.0.1:8000/api/pembeli/alamat/search?q=${query}`, {
                    headers: { 
                        "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                        'Accept': 'application/json',
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    }
                })
                    .then(response => response.json())
                    .then(data => renderAlamat(data.data))
                    .catch(error => console.error("Error searching alamat:", error));
            });

            //////////////////////UPDATE ALAMAT///////////////////////////////////
            document.getElementById("confirmUpdate").addEventListener('click', function(event) {
                if (!idToUpdate) return;

                    const kategori = document.getElementById("kategoriAlamat").value;
                    const alamat = document.getElementById("alamat").value;
                    const nama = document.getElementById("namaAlamat").value;

                    fetch(`http://127.0.0.1:8000/api/pembeli/alamat/update/${idToUpdate}`, {
                        method: 'PUT',
                        headers: {
                            "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                            'Accept': 'application/json',
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            },
                        body: JSON.stringify({
                            alamat,
                            kategori,
                            nama,
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('updateAlamat'));
                        if (modal) modal.hide();

                        Toastify({
                            text: "Berhasil Mengubah Alamat",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#8bc34a",
                        }).showToast();
                        fetchAlamat();
                        idToUpdate = null;
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Toastify({
                            text: "Gagal Mengubah Alamat",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "rgb(221, 25, 25)",
                        }).showToast();
                    });
            });

            ////////////////////////DELETE ALAMAT///////////////////////////////////
            document.getElementById("confirmDelete").addEventListener('click', function(event) {
                if (!idToDelete) return;

                    fetch(`http://127.0.0.1:8000/api/pembeli/alamat/delete/${idToDelete}`, {
                        method: 'DELETE',
                        headers: {
                            "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                            'Accept': 'application/json',
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteAlamat'));
                        if (modal) modal.hide();

                        Toastify({
                            text: "Berhasil Menghapus Alamat",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#8bc34a",
                        }).showToast();
                        fetchAlamat();
                        idToDelete = null;
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Toastify({
                            text: "Gagal Menghapus Alamat",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "rgb(221, 25, 25)",
                        }).showToast();
                    });
            });


            ////////////////////////TAMBAH ALAMAT///////////////////////////////////
            document.getElementById("confirmCreate").addEventListener("click", () => {
                const kategori = document.getElementById("kategoriAlamatCreate").value;
                const alamatJalan = document.getElementById("alamatCreate").value;
                const provinsi = document.getElementById("ProvinsiAlamatCreate").options[document.getElementById("ProvinsiAlamatCreate").selectedIndex].text;
                const kota = document.getElementById("kotaAlamatCreate").options[document.getElementById("kotaAlamatCreate").selectedIndex].text;
                const kecamatan = document.getElementById("kecamatanAlamatCreate").options[document.getElementById("kecamatanAlamatCreate").selectedIndex].text;
                const kelurahan = document.getElementById("kelurahanAlamatCreate").options[document.getElementById("kelurahanAlamatCreate").selectedIndex].text;
                const kodePos = document.getElementById("kodePosAlamatCreate").value;
                const alamat = `${alamatJalan}, ${kelurahan}, ${kecamatan}, ${kota}, ${provinsi}, ${kodePos}`;
                const nama = document.getElementById("namaCreate").value;
                const isDefault = document.getElementById("isDefault").checked;

                if(!kategori || !alamatJalan || !nama || !kodePos || !kecamatan || !kota || !kelurahan) {
                    Toastify({
                        text: "Semua field harus diisi",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "rgb(221, 25, 25)"
                        },
                    }).showToast();
                    return;
                };

                if (!/^\d{5}$/.test(kodePos)) {
                    Toastify({
                        text: "Kode pos harus 5 digit angka",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "rgb(221, 25, 25)"
                        },
                    }).showToast();
                    return;
                }

                function resetFormCreateAlamat() {
                    document.getElementById("kategoriAlamatCreate").value = "";
                    document.getElementById("namaCreate").value = "";
                    document.getElementById("alamatCreate").value = "";
                    document.getElementById("kotaAlamatCreate").value = "";
                    document.getElementById("kecamatanAlamatCreate").innerHTML = '<option value="" disabled selected>Pilih Kecamatan</option>';
                    document.getElementById("kecamatanAlamatCreate").disabled = true;
                    document.getElementById("kelurahanAlamatCreate").innerHTML = '<option value="" disabled selected>Pilih Kelurahan</option>';
                    document.getElementById("kelurahanAlamatCreate").disabled = true;
                    document.getElementById("kodePosAlamatCreate").value = "";
                    document.getElementById("isDefault").checked = false;
                }

                const alamatLengkap = `${alamatJalan}, ${kelurahan}, ${kecamatan}, ${kota}, ${provinsi}, Kode Pos: ${kodePos}`;
                fetch("http://127.0.0.1:8000/api/pembeli/buat-alamat", {
                    method: "POST",
                    headers: {
                        "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                        'Accept': 'application/json',
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        kategori,
                        alamat: alamatLengkap,
                        isDefault,
                        nama,
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Data berhasil ditambahkan:", data);
                    const modal = bootstrap.Modal.getInstance(document.getElementById("createAlamat"));
                    if (modal){
                        modal.hide();
                        resetFormCreateAlamat();
                    } ;
    
                    Toastify({
                        text: "Berhasil Menambahkan Alamat",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#8bc34a"
                        },
                    }).showToast();
    
    
                    fetchAlamat();
                })
                .catch(error => {
                    console.error("Error:", error);
                    Toastify({
                        text: "Gagal Menambahkan Alamat",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "rgb(221, 25, 25)"
                        },
                        // backgroundColor: "rgb(221, 25, 25)",
                    }).showToast();
                });
            })

            ////////////////////////SET DEFAULT///////////////////////////////////
            document.addEventListener('click', function(event) {
                if(event.target && event.target.id === 'confirmDefault' && idToDefault){
                    fetch(`http://127.0.0.1:8000/api/pembeli/alamat/set-default/${idToDefault}`, {
                        method: 'PUT',
                        headers: {
                            "Authorization": `Bearer ${localStorage.getItem("auth_token")}`,
                            'Accept': 'application/json',
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        Toastify({
                            text: "Berhasil Mengubah Menjadi Alamat Utama",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#8bc34a",
                        }).showToast();
    
                        fetchAlamat();
                        idToDefault = null;
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Toastify({
                            text: "Gagal Mengubah Menjadi Alamat Utama",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "rgb(221, 25, 25)",
                        }).showToast();
                    });
                }
            });
        });
    </script>

</body>
</html>
