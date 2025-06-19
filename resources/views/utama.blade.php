<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reusemart</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.css" rel="stylesheet" />
    <!-- Toastify ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<style>
    
    video{
        position: absolute;
        top: 0;
        left: 0;
        min-width: 100%;
        min-height: 100%;
        object-fit: cover;
        z-index: -1;
    }

    .content-wrapper {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center; /* Tengah vertikal */
        align-items: center;     /* Tengah horizontal */
        text-align: center;
        padding: 30px;
        text-shadow: 1px 1px 4px rgba(0,0,0,0.8);
    }
    header.masthead h1, header.masthead .h1 {
        font-size: 2.25rem;
        }
        @media (min-width: 992px) {
        header.masthead {
            height: 100vh;
            min-height: 40rem;
            padding-top: 4.5rem;
            padding-bottom: 0;
        }
        header.masthead p {
            font-size: 1.15rem;
        }
        header.masthead h1, header.masthead .h1 {
            font-size: 3rem;
        }
        }
        @media (min-width: 1200px) {
        header.masthead h1, header.masthead .h1 {
            font-size: 3.5rem;
        }
    }
    .text-white {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-white-rgb), var(--bs-text-opacity)) !important;
    }
    .text-white-75 {
        color: rgba(255, 255, 255, 0.75) !important;
    }
    hr.divider {
        height: 0.2rem;
        max-width: 13.25rem;
        margin: 1.5rem auto;
        background-color:rgb(123, 198, 35);
        opacity: 1;
    }   
    .page-section {
        padding: 8rem 0;
    }
    #portfolio .container-fluid, #portfolio .container-sm, #portfolio .container-md, #portfolio .container-lg, #portfolio .container-xl, #portfolio .container-xxl {
        max-width: 1920px;
    }
    #portfolio .container-fluid .portfolio-box, #portfolio .container-sm .portfolio-box, #portfolio .container-md .portfolio-box, #portfolio .container-lg .portfolio-box, #portfolio .container-xl .portfolio-box, #portfolio .container-xxl .portfolio-box {
        position: relative;
        display: block;
    }
    #portfolio .container-fluid .portfolio-box .portfolio-box-caption, #portfolio .container-sm .portfolio-box .portfolio-box-caption, #portfolio .container-md .portfolio-box .portfolio-box-caption, #portfolio .container-lg .portfolio-box .portfolio-box-caption, #portfolio .container-xl .portfolio-box .portfolio-box-caption, #portfolio .container-xxl .portfolio-box .portfolio-box-caption {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        width: 100%;
        height: 100%;
        position: absolute;
        bottom: 0;
        text-align: center;
        opacity: 0;
        color: #fff;
        background: rgb(123, 198, 35);
        transition: opacity 0.25s ease;
        text-align: center;
    }
    #portfolio .container-fluid .portfolio-box .portfolio-box-caption .project-category, #portfolio .container-sm .portfolio-box .portfolio-box-caption .project-category, #portfolio .container-md .portfolio-box .portfolio-box-caption .project-category, #portfolio .container-lg .portfolio-box .portfolio-box-caption .project-category, #portfolio .container-xl .portfolio-box .portfolio-box-caption .project-category, #portfolio .container-xxl .portfolio-box .portfolio-box-caption .project-category {
        font-family: "Merriweather Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    #portfolio .container-fluid .portfolio-box .portfolio-box-caption .project-name, #portfolio .container-sm .portfolio-box .portfolio-box-caption .project-name, #portfolio .container-md .portfolio-box .portfolio-box-caption .project-name, #portfolio .container-lg .portfolio-box .portfolio-box-caption .project-name, #portfolio .container-xl .portfolio-box .portfolio-box-caption .project-name, #portfolio .container-xxl .portfolio-box .portfolio-box-caption .project-name {
        font-size: 1.2rem;
    }
    #portfolio .container-fluid .portfolio-box:hover .portfolio-box-caption, #portfolio .container-sm .portfolio-box:hover .portfolio-box-caption, #portfolio .container-md .portfolio-box:hover .portfolio-box-caption, #portfolio .container-lg .portfolio-box:hover .portfolio-box-caption, #portfolio .container-xl .portfolio-box:hover .portfolio-box-caption, #portfolio .container-xxl .portfolio-box:hover .portfolio-box-caption {
        opacity: 0.95;
    }
.showcase .showcase-text {
padding: 3rem;
}

.showcase .showcase-img {
    min-height: 30rem;
    background-size: cover;
}

@media (min-width: 768px) {
    .showcase .showcase-text {
        padding: 7rem;
    }
}
.simple-lightbox .sl-caption {
    display: none !important;  /* Hide caption */
}
#hidden{
    opacity: 0;
    transform: translateY(60px); /* Start from below */
    transition: opacity 0.5s ease, transform 0.5s ease;
}
#hidden.fade-in {
    opacity: 1;
    filter: blur(0);
    transform: translateY(0); /* End at normal position */
}
.col-lg-3 col-md-6:nth-child(1){
    transition-delay: 200ms;
}
.col-lg-3 col-md-6:nth-child(2){
    transition-delay: 400ms;
}
.col-lg-3 col-md-6:nth-child(3){
    transition-delay: 600ms;
}
.col-lg-3 col-md-6:nth-child(4){
    transition-delay: 800ms;
}
</style>

<body>
    @include('layouts.navbarVideo')
    <!-- //////////////////ini background video///////////////////// -->
    <div class="position-relative" style="height: 100vh; overflow: hidden;">
        <video autoplay muted loop id="video-bg">
            <source src="{{ asset('ReUseMartVid.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        
        <!-- <div class="content-wrapper text-center">
            <h1>Selamat Datang di Reusemart</h1>
            <p>
               nanti isinya ttg reusemart (?)
            </p>
            </div>
        </div> -->
         <header class="masthead" id="hidden">
            <div class="container px-4 px-lg-5 h-100">
                <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-8 align-self-end">
                        <h1 class="text-white font-weight-bold">Reusemart</h1>
                        <hr class="divider" />
                    </div>
                    <div class="col-lg-8 align-self-baseline">
                        <p class="text-white-75 mb-5">Temukan cerita dari barang bekas. Hadirkan kesempatan baru dari barang lama</p>
                        <a class="btn btn-outline-succes" href="userLogin" style="background-color: rgb(123, 198, 35); color: white;">Masuk</a>
                    </div>
                </div>
            </div>
        </header>
    </div>
    <section class="page-section bg-succes" style="background-color: rgb(123, 198, 35);"id="hidden">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5 justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="text-white mt-0">Temukan Barang Berkualitas</h2>
                    <hr class="divider divider-light" />
                    <p class="text-white-75 mb-5">Jelajahi dan dapatkan barang dengan harga miring di sekitar</p>
                    <a class="btn btn-outline-light" href="/produk">Jelajah Produk</a>
                </div>
            </div>
        </div>
    </section>
    <section class="page-section" id="hidden">
            <div class="container px-4 px-lg-5">
                <h2 class="text-center mt-0">Keuntungan Reusemart</h2>
                <hr class="divider" />
                <div class="row gx-4 gx-lg-5">
                    <div class="col-lg-3 col-md-6 text-center">
                        <div class="mt-5">
                            <div class="mb-2"><i class="bi-gem fs-1 text-success"></i></div>
                            <h3 class="h4 mb-2"style="color:green;">Kualitas Terjamin</h3>
                            <p class="text-muted mb-0">Barang yang tersedia telah lulus QC oleh gudang kami!</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 text-center">
                        <div class="mt-5">
                            <div class="mb-2"><i class="bi-cart-check fs-1 text-success"></i></div>
                            <h3 class="h4 mb-2" style="color:green;">Belanja Dengan Mudah</h3>
                            <p class="text-muted mb-0">Jelajah marketplace tanpa perlu membuat akun</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 text-center">
                        <div class="mt-5">
                            <div class="mb-2"><i class="bi-box2-heart fs-1 text-success"></i></div>
                            <h3 class="h4 mb-2" style="color:green;">Manfaatkan Kembali</h3>
                            <p class="text-muted mb-0">ubah barang bekas anda menjadi kebaikan</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 text-center">
                        <div class="mt-5">
                            <div class="mb-2"><i class="bi-balloon-heart fs-1 text-success"></i></div>
                            <h3 class="h4 mb-2" style="color:green;">Untuk Komunitas</h3>
                            <p class="text-muted mb-0">Membantu Komunitas dan jadilah humanitarian</p>
                        </div>
                    </div>
                </div>
            </div>
    </section>
    <section class="showcase" id="hidden">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-lg-6 order-lg-2 text-white showcase-img" style="background-image: url('{{ asset('bg-showcase-1.jpg') }}');"></div>

            <div class="col-lg-6 order-lg-1 showcase-text" style="background-color: rgb(123, 198, 35); color: white;">
                <h2>Temukan, Beli, Sampai!</h2>
                <p class="lead mb-0">Jelajahi berbagai barang bekas dengan mudah, dan beli dengan aman serta nyaman. Dari menemukan barang bekas berkualitas hingga proses pembelian yang lancar, kami memastikan pengalaman berbelanja Anda memuaskan.</p>
            </div>
        </div>
        <div class="row g-0">
            <div class="col-lg-6 text-white showcase-img" style="background-image: url('{{ asset('bg-showcase-2.jpg') }}');"></div>
            <div class="col-lg-6 my-auto showcase-text">
                <h2 >Barang Lama Bisa Bercerita</h2>
                <p class="lead mb-0">Daripada menumpuk kenangan di gudang, yuk berdayakan kembali barang-barang bekas berkualitasmu. Dengan Reusemart, kamu bukan hanya menjual, tapi juga memberi kesempatan pada barang kesayanganmu untuk membuat cerita baru.</p>
            </div>
        </div>
        <div class="row g-0">
            <div class="col-lg-6 order-lg-2 text-white showcase-img" style="background-image: url('{{ asset('bg-showcase-3.jpg') }}');"></div>
            <div class="col-lg-6 order-lg-1 showcase-text" style="background-color: rgb(123, 198, 35); color: white;">
                <h2>Berkontribusi untuk sesama</h2>
                <p class="lead mb-0">Apabila barangmu tidak dapat menemukan pemilik baru, tenang saja, Reusemart akan memastikan agar barangmu membantu panti asuhan, organisasi masyarakat, dan komunitas lainya!</p>
            </div>
        </div>
    </div>
    <section class="page-section" id="services">
            <div class="container px-4 px-lg-5" id="hidden">
                <h2 class="text-center mt-0">Testimoni Pengguna</h2>
                <hr class="divider" />
            </div>
    </section>
</section>
    <div id="portfolio">
            <div class="container-fluid p-0" id="hidden">
                <div class="row g-0">
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="{{ asset('porto-2.jpg') }}" title="Kimmi Caitlyn">
                            <img class="img-fluid" src="{{ asset('porto-2.jpg') }}" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Kimmi Caitlyn</div>
                                <div class="project-name">"Beli pagi, siang udah sampai aja"</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="{{ asset('porto-1.jpg') }}" title="Made Ivan">
                            <img class="img-fluid" src="{{ asset('porto-1.jpg') }}" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Made Ivan</div>
                                <div class="project-name">"Udah nggak hobi mancing lagi, siapa tau ada yang mau"</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="{{ asset('porto-3.jpg') }}" title="Eliandani A.">
                            <img class="img-fluid" src="{{ asset('porto-3.jpg') }}" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Eliandani A.</div>
                                <div class="project-name">"Produk Top, layanan istimewa"</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="{{ asset('porto-4.jpg') }}" title="Petrus Juan">
                            <img class="img-fluid" src="{{ asset('porto-4.jpg') }}" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Petrus Juan</div>
                                <div class="project-name">"Delivery area Jogja 0 rupiah"</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="{{ asset('porto-5.jpg') }}" title="Marsella Adinda">
                            <img class="img-fluid" src="{{ asset('porto-5.jpg') }}" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Marsella Adinda</div>
                                <div class="project-name">"Harganya benar benar miring, kualitas top"</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="{{ asset('porto-6.jpg') }}" title="Yoru Shika">
                            <img class="img-fluid" src="{{ asset('porto-6.jpg') }}" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Yoru Shika</div>
                                <div class="project-name">"Lebih baik dari yang diharapkan. Reccomended pokoknya"</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>


    

    <!-- Footer Section -->
    <footer class="bg-dark text-white text-center p-3">
        <p>&copy; 2025 Reusemart</p>
    </footer>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybK5Zt9seR4Dd4VuK9ckb9F9c7B66tL8fQ1Qu4u6E9f4W/p7jm" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js"></script>
    <!-- Custom JavaScript to fetch and display products -->
    <script>
        new SimpleLightbox({
            elements: '#portfolio a.portfolio-box',
            captionsData: false,  // Disable captions data
            captionType: 'none',  // Explicitly disable captions
        });

        const target = document.querySelectorAll('#hidden');
        const observer = new IntersectionObserver((entries)=>{
            entries.forEach((entry)=>{
                console.log(entry)
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                } else {
                    entry.target.classList.remove('fade-in');
                }
            });
        });
        target.forEach((el)=>observer.observe(el));

    // const observerCallback = function (entries, observer) {
    //     entries.forEach(entry => {
    //         if (entry.isIntersecting) {
    //             entry.target.classList.add('fade-in');
    //         } else {
    //             entry.target.classList.remove('fade-in');
    //         }
    //     });
    // };

    // // const observer = new IntersectionObserver(observerCallback, options);
    // observer.observe(target);
    </script>
    
</body>
</html>
