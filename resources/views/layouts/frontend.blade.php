<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>SIAKAD - SDN PASIRIPIS</title>
    <meta content="Sistem Informasi Akademik SD Negeri Pasiripis" name="description">
    <meta content="SIAKAD, SD Negeri Pasiripis, Sekolah, Pendidikan, Kemdikbud, Dapodik" name="keywords">

    <link href="{{ asset('frontend/assets/img/logo.png') }}" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&family=Poppins:wght@300;400;500;600;700&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('frontend/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('frontend/assets/css/style.css') }}" rel="stylesheet">

    {{-- CSS TAMBAHAN UNTUK ANIMASI & PROFESIONALISME --}}
    <style>
        /* Tipografi Hero Section */
        #hero {
            background: linear-gradient(135deg, #f3f8fa 0%, #e0eafc 100%);
            padding: 120px 0 60px 0;
            min-height: 80vh;
        }
        #hero h1 {
            font-family: 'Jost', sans-serif;
            font-size: 48px;
            font-weight: 700;
            line-height: 56px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        #hero h1 span {
            color: #3498db;
        }
        #hero h2 {
            color: #556877;
            margin-bottom: 30px;
            font-size: 18px;
            font-weight: 400;
            line-height: 1.6;
        }
        
        /* Tombol Login Utama (Hero) */
        .btn-login-hero {
            font-family: "Poppins", sans-serif;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            padding: 12px 32px;
            border-radius: 50px;
            transition: 0.3s;
            color: #fff;
            background: #3498db;
            box-shadow: 0 8px 15px rgba(52, 152, 219, 0.3);
            text-decoration: none;
        }
        .btn-login-hero:hover {
            background: #2980b9;
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.4);
        }

        /* Tombol Sekunder (Hero) */
        .btn-outline-hero {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            padding: 10px 30px;
            border-radius: 50px;
            transition: 0.3s;
            color: #3498db;
            border: 2px solid #3498db;
            text-decoration: none;
        }
        .btn-outline-hero:hover {
            background: #3498db;
            color: #fff;
        }

        /* Tombol Login Navbar */
        .navbar ul li a.nav-link {
            color: #2c3e50 !important; /* Warna teks gelap */
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            padding: 10px 15px;
            transition: 0.3s;
        }
        .navbar ul li a.nav-link:hover, 
        .navbar ul li a.nav-link.active {
            color: #3498db !important; /* Warna biru saat dihover/aktif */
        }
        
        /* Ikon toggle (hamburger menu) di layar HP agar warnanya gelap */
        .mobile-nav-toggle {
            color: #2c3e50 !important;
            font-size: 28px;
            cursor: pointer;
            line-height: 0;
            transition: 0.5s;
        }

        /* Efek hover untuk kartu-kartu */
        .icon-box, .member, .info-box {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .icon-box:hover, .member:hover, .info-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        }

        /* Banner Kemdikbud Spesial (Diperbaiki) */
        .banner-kemdikbud {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none !important;
            display: flex;
            align-items: center;
        }
        .banner-kemdikbud h4 {
            color: #ffffff !important;
        }
        .banner-kemdikbud p {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        .banner-kemdikbud:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(30, 60, 114, 0.4) !important;
        }

        /* Team section Responsif */
        .team .member { text-align: center; padding: 20px; background: #fff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid #f1f1f1; }
        .team .member-img { margin-bottom: 15px; }
        .team .member-img img {
            width: 100%;
            max-width: 160px;
            height: auto;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            border-radius: 50%; /* Bulat agar lebih profesional seperti profil */
            margin: 0 auto;
            border: 5px solid #f3f8fa;
            transition: transform 0.3s ease;
        }
        .team .member:hover .member-img img { transform: scale(1.05); }
        .team .member-info h5 { font-weight: 700; font-size: 18px; margin-bottom: 5px; color: #2c3e50; }
        .team .member-info span { font-style: normal; font-size: 13px; color: #7f8c8d; font-weight: 500; display: block; }
        .team .member-info .divider { width: 40px; height: 3px; background: #3498db; margin: 10px auto; border-radius: 2px; }

        /* Section Titles */
        .section-title h2 { color: #2c3e50; font-weight: 700; }
        .section-title h2::after { background: #3498db; }
        
        .social-btn {
            width: 45px; height: 45px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; color: #fff; transition: all 0.3s ease;
        }
        .social-btn.facebook { background: #1877f2; }
        .social-btn.instagram { background: linear-gradient(45deg, #feda75, #fa7e1e, #d62976, #962fbf, #4f5bd5); }
        .social-btn.youtube { background: #ff0000; }
        .social-btn.gmail { background: #ea4335; }
        .social-btn:hover { transform: translateY(-5px) scale(1.1); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25); color: #fff;}

        /* Frame Estetik untuk Foto Sekolah di Hero Section */
        .hero-photo-wrapper {
            position: relative;
            max-width: 480px;
            margin: 0 auto;
            z-index: 1;
        }
        .hero-photo-bg {
            position: absolute;
            top: -15px;
            right: -20px;
            bottom: 20px;
            left: 20px;
            background: linear-gradient(135deg, #3498db 0%, #1e3c72 100%);
            border-radius: 20px;
            z-index: -1;
            transform: rotate(3deg);
            box-shadow: 0 15px 30px rgba(30, 60, 114, 0.2);
        }
        .hero-photo-wrapper .carousel-inner {
            border: 8px solid #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
            overflow: hidden;
            background-color: #fff;
        }
        .hero-photo-wrapper .carousel-item img {
            width: 100%;
            aspect-ratio: 4 / 3; /* Memastikan semua foto ukurannya seragam tanpa loncat */
            object-fit: cover;
        }
        .floating-badge-hero {
            position: absolute;
            bottom: -20px;
            left: -20px;
            background: #ffffff;
            padding: 12px 25px;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid #f1f5f9;
            animation: float 3s ease-in-out infinite;
        }
        .floating-badge-hero i {
            font-size: 28px;
            color: #f1c40f;
        }
        .floating-badge-hero div {
            display: flex;
            flex-direction: column;
            text-align: left;
        }
        .floating-badge-hero .title {
            font-weight: 700;
            color: #2c3e50;
            font-size: 15px;
            line-height: 1.2;
        }
        .floating-badge-hero .subtitle {
            font-size: 12px;
            color: #7f8c8d;
            font-weight: 500;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        /* --- ABOUT / SAMBUTAN SECTION (FIXED LONG TEXT) --- */
        .about .content-wrapper {
            background: #fff; border-radius: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.05);
            overflow: hidden; border: 1px solid #f1f5f9;
        }
        .about .principal-box { 
            background: #f8faff; padding: 50px 40px; text-align: center; 
            border-right: 1px solid #f1f5f9;
        }
        .about .principal-img {
            width: 200px; height: 200px; object-fit: cover; border-radius: 50%;
            border: 8px solid #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin-bottom: 25px;
        }
        .about .text-content {
            padding: 50px; max-height: 550px; overflow-y: auto; position: relative;
        }

        /* Footer Professional */
        #footer {
            background: #0f1728;
            color: #cbd5e1;
            position: relative;
            z-index: 10;
            width: 100%;
        }
        #footer .footer-top {
            padding: 50px 0 30px;
        }
        #footer .footer-top h4,
        #footer .footer-top h5 {
            color: #ffffff;
            font-weight: 700;
            margin-bottom: 20px;
        }
        #footer .footer-top p,
        #footer .footer-top ul li,
        #footer .footer-top .footer-contact li {
            color: #94a3b8;
            line-height: 1.8;
            font-size: 14px;
        }
        #footer .footer-top ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        #footer .footer-top ul li {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        #footer .footer-top ul li i {
            flex-shrink: 0;
            margin-top: 3px;
            color: #5990e4;
            font-size: 16px;
        }
        #footer .footer-top ul li a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        #footer .footer-top ul li a:hover {
            color: #ffffff;
            transform: translateX(3px);
        }
        #footer .footer-top .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            margin-right: 10px;
            margin-bottom: 10px;
            transition: background 0.3s ease, transform 0.3s ease;
        }
        #footer .footer-top .social-links a:hover {
            background: #2b7dde;
            transform: translateY(-2px);
        }
        #footer .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 20px;
            padding-bottom: 20px;
            margin-top: 25px;
            font-size: 13px;
            color: #8b96a7;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            position: relative;
            z-index: 20;
        }
        #footer .footer-bottom .copyright {
            margin: 0;
            flex: 1;
            min-width: 200px;
        }
        #footer .footer-bottom .credits {
            margin: 0;
            flex-shrink: 0;
            white-space: nowrap;
            padding: 8px 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            border-left: 3px solid #5990e4;
        }
        #footer .footer-note {
            margin-bottom: 15px;
            color: #94a3b8;
        }
    </style>
</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top shadow-sm bg-white">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="logo d-flex align-items-center">
                <a href="{{ url('/') }}" class="me-2">
                    <img src="{{ asset('update/logo.png') }}" alt="Logo SDN PASIRIPIS" class="img-fluid" style="height:45px;">
                </a>
                <h1 class="m-0 fs-4 fw-bold" style="font-family: 'Poppins', sans-serif;">
                    <a href="{{ url('/') }}" style="color: #2c3e50;">SD NEGERI PASIRIPIS</a>
                </h1>
            </div>

            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto active" href="#hero">Beranda</a></li>
                    <li><a class="nav-link scrollto" href="#about">Sambutan</a></li>
                    <li><a class="nav-link scrollto" href="#visimisi">Visi & Misi</a></li>
                    <li><a class="nav-link scrollto" href="#informasi">Informasi</a></li>
                    <li><a class="nav-link scrollto" href="#team">Direktori GTK</a></li>
                    <li><a class="nav-link scrollto" href="#contact">Kontak</a></li>
                    <li><a class="nav-link nav-kelulusan" href="{{ route('cek-kelulusan') }}"><i class="bx bxs-graduation me-1"></i> Cek Kelulusan</a></li>
                    {{-- TOMBOL LOGIN UTAMA DI NAVBAR --}}
                    <li>
                        <a class="getstarted d-flex align-items-center" href="{{ url('login') }}">
                            <i class="bx bx-log-in-circle fs-5 me-1"></i> Login SIAKAD
                        </a>
                    </li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
        </div>
    </header>

    <!-- ======= Hero Section ======= -->
    <section id="hero" class="d-flex align-items-center">
        <div class="container">
            <div class="row d-flex align-items-center">
                <div class="col-lg-6 pt-5 pt-lg-0 order-2 order-lg-1" data-aos="fade-right" data-aos-delay="100">
                    <h6 class="text-uppercase fw-bold text-primary mb-2" style="letter-spacing: 2px;">Selamat Datang di Portal Resmi</h6>
                    <h1>Sistem Informasi Akademik<br><span>SIAKAD</span></h1>
                    <h2>Platform digital terintegrasi untuk mendukung proses belajar mengajar, pengelolaan data akademik, dan transparansi informasi sekolah secara presisi.</h2>
                    
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="{{ url('login') }}" class="btn-login-hero">
                            <i class="bx bx-log-in-circle fs-4 me-2"></i> Masuk ke Akun
                        </a>
                        <a href="#about" class="btn-outline-hero scrollto">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2 text-center mt-5 mt-lg-0" data-aos="fade-left" data-aos-delay="200">
                    
                    <!-- BINGKAI FOTO PROFESIONAL UNTUK SLIDESHOW SEKOLAH -->
                    <div class="hero-photo-wrapper animated">
                        <!-- Latar belakang geometris -->
                        <div class="hero-photo-bg"></div>
                        
                        <!-- SLIDESHOW FOTO SEKOLAH (BOOTSTRAP CAROUSEL) -->
                        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
                            <div class="carousel-inner">
                                <!-- Foto 1 -->
                                <div class="carousel-item active">
                                    <img src="{{ asset('frontend/assets/img/foto_sekolah_1.jpg') }}" class="d-block w-100" alt="Gedung SDN Pasiripis" onerror="this.src='https://placehold.co/800x600/3498db/ffffff?text=Foto+Sekolah+1'">
                                </div>
                                <!-- Foto 2 -->
                                <div class="carousel-item">
                                    <img src="{{ asset('frontend/assets/img/foto_sekolah_2.jpg') }}" class="d-block w-100" alt="Kegiatan SDN Pasiripis" onerror="this.src='https://placehold.co/800x600/2980b9/ffffff?text=Foto+Sekolah+2'">
                                </div>
                                <!-- Foto 3 -->
                                <div class="carousel-item">
                                    <img src="{{ asset('frontend/assets/img/foto_sekolah_3.jpg') }}" class="d-block w-100" alt="Fasilitas SDN Pasiripis" onerror="this.src='https://placehold.co/800x600/1abc9c/ffffff?text=Foto+Sekolah+3'">
                                </div>
                            </div>
                            
                            <!-- Tombol Navigasi Kiri/Kanan -->
                            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.3); border-radius: 50%; padding: 20px;"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.3); border-radius: 50%; padding: 20px;"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        
                        <!-- Lencana Mengambang -->
                        <div class="floating-badge-hero">
                            <i class="bx bxs-check-shield"></i>
                            <div>
                                <span class="title">Sistem Terintegrasi</span>
                                <span class="subtitle">Data Aman & Transparan</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <main id="main">
        <!-- Sambutan Section (Fixed Scrollable) -->
        @if(isset($kepalaSekolah))
        <section id="about" class="about section-bg py-5">
            <div class="container">
                <div class="section-title"><h2 data-aos="fade-up">Sambutan Kepala Sekolah</h2></div>
                <div class="content-wrapper row g-0" data-aos="fade-up">
                    <div class="col-lg-4 principal-box">
                        <img src="{{ $kepalaSekolah->foto ? asset('foto_pegawai/' . $kepalaSekolah->foto) : asset('update/img/ks.jpg') }}" class="principal-img shadow-sm" alt="Kepala Sekolah">
                        <h5 class="fw-bold text-dark mb-1">{{ $kepalaSekolah->nama }}</h5>
                        <span class="text-primary small fw-bold text-uppercase" style="letter-spacing: 1px;">Kepala SDN Pasiripis</span>
                    </div>
                    <div class="col-lg-8 text-content">
                        <h4 class="fw-bold text-dark mb-3">Assalamualaikum Warahmatullahi Wabarakatuh,</h4>
                        <p class="fst-italic text-muted mb-4" style="font-size: 16px;">"Pendidikan bukan hanya tentang mengisi wadah, melainkan tentang menyalakan api karakter dan kecerdasan."</p>
                        <div class="description text-secondary" style="line-height: 1.9; font-size: 15px; text-align: justify;">
                            <p>Selamat datang di SD Negeri Pasiripis, tempat di mana visi, misi, dan tujuan sekolah bukan hanya menjadi kata-kata, tetapi juga menjadi kompas yang mengarahkan perjalanan pendidikan kita. Kami berkomitmen untuk mengoptimalkan pendidikan dasar, memastikan bahwa setiap siswa mendapatkan dasar kecerdasan, pengetahuan, kepribadian, akhlak mulia, dan keterampilan yang diperlukan untuk hidup mandiri dan mengikuti pendidikan lebih lanjut.</p>
                            <p>Visi kami adalah menghasilkan lulusan yang tidak hanya pintar, tetapi juga berakhlakul karimah. Kami percaya bahwa kecerdasan akademik harus selaras dengan pembentukan karakter yang baik. Ini adalah prinsip inti yang menginspirasi setiap aspek kehidupan di SD Negeri Pasiripis.</p>
                            <p>Dalam mewujudkan visi ini, kami memiliki misi yang kuat. Pertama, kami berkomitmen untuk membentuk siswa berprestasi yang berakhlakul karimah. Kami ingin melihat mereka tumbuh menjadi individu yang pintar, bermoral, dan bertanggung jawab. Kedua, kami akan terus bekerja keras untuk meningkatkan pencapaian dalam setiap mata pelajaran, sehingga siswa kami memiliki pengetahuan yang kuat.</p>
                            <p>Tujuan utama kami adalah mengembangkan capaian pembelajaran sesuai dengan situasi dan tuntutan zaman. Dengan program pembelajaran yang aktif, inovatif, kreatif, dan menyenangkan melalui platform SIAKAD ini, kami ingin menjadikan pembelajaran sebagai pengalaman yang bermakna bagi seluruh keluarga besar SDN Pasiripis.</p>
                            <p>Wassalamualaikum Warahmatullahi Wabarakatuh.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- VISI & MISI --}}
        <section id="visimisi" class="py-5">
            <div class="container">
                <div class="section-title">
                    <h2 data-aos="fade-in">VISI & MISI SEKOLAH</h2>
                </div>
                <div class="row gy-4">
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="icon-box h-100 p-4 p-md-5 border rounded-4 bg-white">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="bx bx-show fs-1"></i>
                                </div>
                                <h3 class="fw-bold m-0">Visi Sekolah</h3>
                            </div>
                            <p class="text-primary fw-bold fs-5 mb-3" style="line-height: 1.4;">
                                "Terwujudnya generasi yang beriman, berakhlak mulia, cerdas, dan berkarakter."
                            </p>
                            <p class="text-muted" style="text-align: justify; font-size: 14px;">
                                Cita-cita luhur untuk membentuk peserta didik yang tidak hanya unggul dalam keilmuan, tetapi teguh dalam keyakinan spiritual dan budi pekerti, guna menghadapi tantangan masa depan dengan bijaksana.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="icon-box h-100 p-4 p-md-5 border rounded-4 bg-white">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="bx bx-target-lock fs-1"></i>
                                </div>
                                <h3 class="fw-bold m-0">Misi Sekolah</h3>
                            </div>
                            <ul class="text-muted ps-3 m-0" style="text-align: justify; font-size: 15px; line-height: 1.8;">
                                <li>Mewujudkan peserta didik yang bertakwa terhadap Tuhan Yang Maha Esa.</li>
                                <li>Mewujudkan peserta didik yang berakhlakul karimah.</li>
                                <li>Menerapkan strategi pembelajaran modern yang berorientasi pada kemajuan IPTEK.</li>
                                <li>Mencetak generasi bangsa yang berbudi pekerti luhur dan siap berkolaborasi.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Keunggulan --}}
        <section id="keunggulan" class="section-bg py-5">
            <div class="container">
                <div class="section-title">
                    <h2 data-aos="fade-in">Mengapa Memilih Kami?</h2>
                </div>
                <div class="row text-center gy-4">
                    <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="100">
                        <div class="icon-box p-4 bg-white rounded-4 h-100 border-bottom border-primary border-5">
                            <i class="bi bi-book-half display-4 text-primary mb-3 d-block"></i>
                            <h4 class="fw-bold">Kurikulum Berkualitas</h4>
                            <p class="text-muted small">Pendekatan pembelajaran inovatif, kreatif, dan berpusat penuh pada optimalisasi potensi siswa.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                        <div class="icon-box p-4 bg-white rounded-4 h-100 border-bottom border-warning border-5">
                            <i class="bi bi-trophy display-4 text-warning mb-3 d-block"></i>
                            <h4 class="fw-bold">Bina Minat & Bakat</h4>
                            <p class="text-muted small">Program ekstrakurikuler komprehensif di bidang olahraga, seni, keagamaan, hingga kepramukaan.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="300">
                        <div class="icon-box p-4 bg-white rounded-4 h-100 border-bottom border-info border-5">
                            <i class="bi bi-shield-check display-4 text-info mb-3 d-block"></i>
                            <h4 class="fw-bold">Lingkungan Kondusif</h4>
                            <p class="text-muted small">Fasilitas terawat, aman, dan ekosistem pendidikan yang asri untuk menunjang daya konsentrasi siswa.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section id="informasi" class="py-5">
            <div class="container">
                <div class="section-title"><h2>Papan Informasi Akademik</h2></div>
                @if ($info->isEmpty())
                    <div class="alert alert-secondary text-center py-4 rounded-4"><h5>Belum Ada Pengumuman</h5></div>
                @else
                    <div id="infoCarousel" class="carousel slide shadow-sm rounded-4 overflow-hidden" data-bs-ride="carousel">
                        <div class="carousel-inner bg-white">
                            @foreach ($info as $item)
                                <div class="carousel-item @if($loop->first) active @endif p-0">
                                    <div class="row g-0 align-items-center">
                                        @if($item->foto)
                                        <div class="col-md-5">
                                            <img src="{{ url('/informasi_foto/' . $item->foto) }}" class="img-fluid w-100" style="height:350px; object-fit:cover;">
                                        </div>
                                        @endif
                                        <div class="@if($item->foto) col-md-7 @else col-md-12 @endif p-4 p-md-5">
                                            <div class="badge bg-primary mb-3"><i class="bx bx-calendar"></i> {{ \Carbon\Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMM YYYY') }}</div>
                                            <h3 class="fw-bold mb-3">{{ $item->judul }}</h3>
                                            {{-- FIX: Penerapan Justify pada preview teks berita --}}
                                            <div class="news-preview-text mb-4">
                                                {{ Str::limit(strip_tags($item->isi), 180) }}
                                            </div>
                                            <a href="{{ route('berita.show', \Illuminate\Support\Str::slug($item->judul)) }}" class="btn btn-outline-primary rounded-pill px-4">
                                                Baca Selengkapnya <i class="bx bx-right-arrow-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#infoCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon bg-dark rounded-circle p-3"></span></button>
                        <button class="carousel-control-next" type="button" data-bs-target="#infoCarousel" data-bs-slide="next"><span class="carousel-control-next-icon bg-dark rounded-circle p-3"></span></button>
                    </div>
                @endif
            </div>
        </section>

        {{-- Tenaga Pendidik & Administrasi --}}
        <section id="team" class="team py-5 section-bg">
            <div class="container">
                <div class="section-title text-center mb-5" data-aos="fade-up">
                    <h2>Direktori Tenaga Pendidik & Kependidikan</h2>
                    <p>Mengenal lebih dekat ujung tombak penggerak pendidikan di SDN Pasiripis.</p>
                </div>

                {{-- Guru --}}
                @if(isset($guru) && $guru->count() > 0)
                <div class="mb-4"><h4 class="fw-bold text-primary border-bottom pb-2 d-inline-block">Guru</h4></div>
                <div class="org-row row mb-5">
                    @foreach($guru as $g)
                    <div class="col-lg-3 col-md-4 col-sm-6 text-center mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <div class="member pt-4 pb-4">
                            <div class="member-img">
                                <img src="{{ $g->foto ? asset('foto_pegawai/' . $g->foto) : 'https://placehold.co/180x180/EFEFEF/AAAAAA?text=Foto' }}" alt="Foto {{ $g->nama }}">
                            </div>
                            <div class="member-info mt-3 px-2">
                                <h5 class="text-truncate" title="{{ $g->nama }}">{{ $g->nama }}</h5>
                                <div class="divider"></div>
                                <span>{{ $g->jabatan }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Tenaga Kependidikan / Operator Sekolah --}}
                @if(isset($admin) && $admin->count() > 0)
                <div class="mb-4"><h4 class="fw-bold text-primary border-bottom pb-2 d-inline-block">Tenaga Kependidikan</h4></div>
                <div class="org-row row mt-4">
                    @foreach($admin as $a)
                    <div class="col-lg-3 col-md-4 col-sm-6 text-center mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <div class="member pt-4 pb-4">
                            <div class="member-img">
                                <img src="{{ $a->foto ? asset('foto_pegawai/' . $a->foto) : 'https://placehold.co/180x180/EFEFEF/AAAAAA?text=Foto' }}" alt="Foto {{ $a->nama }}">
                            </div>
                            <div class="member-info mt-3 px-2">
                                <h5 class="text-truncate" title="{{ $a->nama }}">{{ $a->nama }}</h5>
                                <div class="divider"></div>
                                <span>{{ $a->jabatan }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </section>

        {{-- Kontak --}}
        <section id="contact" class="contact py-5 bg-white">
            <div class="container" data-aos="fade-up">

                <div class="section-title text-center mb-5">
                    <h2>Kontak & Lokasi Kami</h2>
                    <p>Pusat informasi dan layanan terpadu SDN Pasiripis.</p>
                </div>

                <div class="row gy-4">
                    <!-- Peta Lokasi -->
                    <div class="col-lg-7">
                        <div class="info-box shadow-sm p-3 bg-white rounded-4 border h-100" data-aos="fade-up">
                            <div style="width: 100%; height: 100%; min-height: 350px; border-radius: 10px; overflow: hidden;">
                                <iframe 
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3064.9608711581272!2d107.90968157362784!3d-6.6744432933206665!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69292c0c3458af%3A0xce5c71aa20fc4f8e!2sSDN%20Pasiripis!5e1!3m2!1sid!2sid!4v1760154829435!5m2!1sid!2sid" 
                                    width="100%" 
                                    height="100%" 
                                    style="border:0;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        </div>
                    </div>

                    <!-- Info Kontak & Link Kemdikbud -->
                    <div class="col-lg-5 d-flex flex-column gap-3">
                        
                        <!-- Baris Email & Telepon -->
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="info-box shadow-sm p-4 bg-white rounded-4 border text-center h-100 d-flex flex-column justify-content-center" data-aos="fade-left" data-aos-delay="100">
                                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                                        <i class="bx bx-envelope fs-3"></i>
                                    </div>
                                    <h4 class="fw-bold mb-1 fs-6">Email Info</h4>
                                    <p class="mb-0 text-muted small text-break">info@sdnpasiripis.sch.id</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="info-box shadow-sm p-4 bg-white rounded-4 border text-center h-100 d-flex flex-column justify-content-center" data-aos="fade-left" data-aos-delay="150">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                                        <i class="bx bx-phone-call fs-3"></i>
                                    </div>
                                    <h4 class="fw-bold mb-1 fs-6">Telepon / WA</h4>
                                    <p class="mb-0 text-muted small">+6281-563-883-919</p>
                                </div>
                            </div>
                        </div>

                        <!-- Profil Resmi Kemdikbud -->
                        <a href="https://referensi.data.kemendikdasmen.go.id/pendidikan/npsn/20233962" target="_blank" rel="noopener"
                           class="banner-kemdikbud shadow-sm p-4 rounded-4 gap-3 mt-3" data-aos="fade-left" data-aos-delay="200">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm me-3" style="width: 60px; height: 60px; flex-shrink: 0;">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/9/9c/Logo_of_Ministry_of_Education_and_Culture_of_Republic_of_Indonesia.svg" alt="Kemdikbud" style="width: 40px;">
                            </div>
                            <div>
                                <h4 class="fw-bold mb-1 fs-5 text-white">Data Referensi</h4>
                                <p class="mb-0 text-white-50 small" style="line-height: 1.4;">Lihat identitas dan data pokok pendidikan (Dapodik) resmi di portal Kemendikdasmen 2026.</p>
                            </div>
                        </a>

                        <!-- Media Sosial -->
                        <div class="info-box shadow-sm p-4 bg-white rounded-4 border text-center mt-auto" data-aos="fade-left" data-aos-delay="250">
                            <h5 class="fw-bold mb-3 text-dark fs-6">Ikuti Media Sosial Kami</h5>
                            <div class="social-icons d-flex justify-content-center flex-wrap gap-3">
                                <!-- <a href="https://www.facebook.com/sdnpasiripis" class="social-btn facebook" target="_blank" title="Facebook"><i class="bx bxl-facebook"></i></a> -->
                                <a href="https://www.instagram.com/sdnpasiripis" class="social-btn instagram" target="_blank" title="Instagram"><i class="bx bxl-instagram"></i></a>
                                <a href="https://www.youtube.com/@sdnpasiripis" class="social-btn youtube" target="_blank" title="YouTube"><i class="bx bxl-youtube"></i></a>
                                <a href="mailto:sdnpasiripis20@gmail.com" class="social-btn gmail" target="_blank" title="Email"><i class="bx bx-envelope"></i></a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer">
        <div class="container">
            <div class="footer-top">
                <div class="row gy-4">
                    <div class="col-lg-4 col-md-6" data-aos="fade-up">
                        <h4>SDN PASIRIPIS</h4>
                        <p class="footer-note">Melayani pendidikan dasar dengan cara modern, ramah siswa, dan berbasis transparansi informasi melalui platform SIAKAD.</p>
                        <div class="footer-contact">
                            <ul class="ps-0">
                                <li><i class="bi bi-geo-alt-fill"></i><strong>Alamat:</strong> Dusun Pasiripis Kidul RT.001/RW.001 Desa Karangbungur Kec. Buahdua - Sumedang. 45392</li>
                                <li><i class="bi bi-envelope-fill"></i><strong>Email:</strong> info@sdnpasiripis.sch.id</li>
                                <li><i class="bi bi-whatsapp"></i><strong>WA:</strong> +62 81 563 883 919</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <h5>Link Cepat</h5>
                        <ul>
                            <li><a href="#hero">Beranda</a></li>
                            <li><a href="#about">Sambutan</a></li>
                            <li><a href="#visimisi">Visi & Misi</a></li>
                            <li><a href="#contact">Kontak</a></li>
                            <li><a href="{{ url('login') }}">Login SIAKAD</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-4 col-md-12" data-aos="fade-up" data-aos-delay="200">
                        <h5>Terhubung Dengan Kami</h5>
                        <p class="footer-note">Ikuti perkembangan kegiatan dan informasi sekolah melalui saluran resmi kami.</p>
                        <div class="social-links d-flex flex-wrap">
                            <a href="https://www.instagram.com/sdnpasiripis" class="instagram" target="_blank" rel="noopener" aria-label="Instagram"><i class="bx bxl-instagram"></i></a>
                            <a href="https://www.youtube.com/@sdnpasiripis" class="youtube" target="_blank" rel="noopener" aria-label="YouTube"><i class="bx bxl-youtube"></i></a>
                            <a href="mailto:sdnpasiripis20@gmail.com" class="gmail" target="_blank" rel="noopener" aria-label="Email"><i class="bx bx-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="copyright mb-2 mb-md-0">
                    &copy; {{ date('Y') }} <strong><span>SDN PASIRIPIS</span></strong>. Selalu siap mendukung pendidikan dan kemajuan anak.
                </div>
                <div class="credits">
                    Dikembangkan oleh Usep Suherman
                </div>
            </div>
        </div>
    </footer>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    {{-- Modal Informasi --}}
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-light border-bottom-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 px-md-5 pb-5 pt-0">
                    <small id="infoModalDate" class="text-primary fw-bold text-uppercase" style="letter-spacing: 1px;"></small>
                    <h3 class="modal-title fw-bold text-dark mt-2 mb-4" id="infoModalLabel" style="line-height: 1.4;">Detail Informasi</h3>
                    <img id="infoModalImage" src="" class="img-fluid rounded-4 mb-4 shadow-sm w-100" alt="Foto Pengumuman" style="display: none; max-height: 400px; object-fit: cover;">
                    <div id="infoModalContent" class="text-muted" style="line-height: 1.8; font-size: 16px; text-align: justify;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('frontend/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/main.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const infoModal = document.getElementById('infoModal');
            if (infoModal) {
                infoModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const judul = button.getAttribute('data-judul');
                    const isi = button.getAttribute('data-isi');
                    const foto = button.getAttribute('data-foto');
                    const tanggal = button.getAttribute('data-tanggal');
                    
                    infoModal.querySelector('.modal-title').textContent = judul;
                    infoModal.querySelector('#infoModalContent').textContent = isi;
                    infoModal.querySelector('#infoModalDate').textContent = tanggal;
                    
                    const modalImage = infoModal.querySelector('#infoModalImage');
                    if (foto) {
                        modalImage.src = foto;
                        modalImage.style.display = 'block';
                    } else {
                        modalImage.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>