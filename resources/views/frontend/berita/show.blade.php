<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    {{-- SEO & OPEN GRAPH (PENTING AGAR WA MUNCUL FOTO) --}}
    <title>{{ $berita->judul }} - SDN Pasiripis</title>
    <meta name="description" content="{{ Str::limit(strip_tags($berita->isi), 150) }}">
    <meta property="og:title" content="{{ $berita->judul }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($berita->isi), 150) }}">
    <meta property="og:image" content="{{ $berita->foto ? url('/informasi_foto/' . $berita->foto) : asset('update/logo.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    <meta name="twitter:card" content="summary_large_image">

    <link href="{{ asset('frontend/assets/img/logo.png') }}" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #fff; color: #333; line-height: 1.8; }
        .breadcrumb-item a { text-decoration: none; color: #3498db; }
        .article-title { font-weight: 800; color: #2c3e50; font-size: 32px; margin: 20px 0; line-height: 1.3; }
        .article-meta { color: #888; font-size: 14px; margin-bottom: 30px; }
        
        .featured-img { border-radius: 20px; width: 100%; max-height: 500px; object-fit: cover; margin-bottom: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .article-content { font-size: 17px; text-align: justify; white-space: pre-line; color: #444; }

        /* Sidebar Styling */
        .sidebar-card { background: #f8f9fa; border-radius: 15px; padding: 25px; margin-bottom: 30px; border: none; }
        .sidebar-title { font-weight: 700; margin-bottom: 20px; border-left: 4px solid #3498db; padding-left: 15px; font-size: 18px; color: #2c3e50; }
        
        .bg-primary-dark { 
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important; 
            color: #ffffff !important;
        }
        .bg-primary-dark h6 { color: #ffffff !important; font-weight: 700; }

        .recent-post-item { display: flex; gap: 15px; margin-bottom: 20px; text-decoration: none; }
        .recent-post-img { width: 70px; height: 70px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
        .recent-post-info h6 { font-size: 14px; font-weight: 600; margin-bottom: 5px; color: #2c3e50; transition: 0.3s; }
        .recent-post-item:hover h6 { color: #3498db; }

        /* Toast Notification */
        #toast-copy {
            visibility: hidden; min-width: 200px; background-color: #333; color: #fff; text-align: center;
            border-radius: 50px; padding: 10px; position: fixed; z-index: 1000; left: 50%; bottom: 30px;
            transform: translateX(-50%); font-size: 14px;
        }
        #toast-copy.show { visibility: visible; animation: fadein 0.5s, fadeout 0.5s 2.5s; }
        @keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
        @keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }

        @media (max-width: 768px) { .article-title { font-size: 24px; } }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('update/logo.png') }}" width="40" class="me-2">
                <span class="fw-bold text-dark">SDN PASIRIPIS</span>
            </a>
            <a href="{{ route('berita.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                <i class="bi bi-grid"></i> Lihat Semua Berita
            </a>
        </div>
    </nav>

    <main class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('berita.index') }}">Berita</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </nav>

                    <h1 class="article-title">{{ $berita->judul }}</h1>
                    
                    <div class="article-meta d-flex align-items-center flex-wrap gap-3">
                        <div><i class="bi bi-calendar3 me-2"></i> {{ \Carbon\Carbon::parse($berita->created_at)->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                        <div><i class="bi bi-person me-2"></i> Admin Sekolah</div>
                        <div><i class="bi bi-eye me-2"></i> Dibaca {{ rand(50, 200) }} kali</div>
                    </div>

                    @if($berita->foto)
                        <img src="{{ url('/informasi_foto/' . $berita->foto) }}" class="featured-img" alt="{{ $berita->judul }}">
                    @endif

                    <div class="article-content">
                        {!! $berita->isi !!}
                    </div>

                    {{-- FITUR BERBAGI TERBARU --}}
                    <div class="mt-5 pt-4 border-top">
                        <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-share-fill me-2"></i> Bagikan Berita Ini:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            {{-- WhatsApp --}}
                            <a href="https://api.whatsapp.com/send?text={{ urlencode('*' . $berita->judul . '*' . "\n\n" . url()->current()) }}" 
                               target="_blank" class="btn btn-success rounded-pill px-3 shadow-sm btn-sm">
                                <i class="bi bi-whatsapp me-2"></i> WhatsApp
                            </a>

                            {{-- Instagram (Deep Link untuk Story / Mobile) --}}
                            <button onclick="shareToInstagram()" class="btn btn-danger rounded-pill px-3 shadow-sm btn-sm" style="background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); border:none;">
                                <i class="bi bi-instagram me-2"></i> Instagram
                            </button>

                            {{-- Facebook --}}
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                               target="_blank" class="btn btn-primary rounded-pill px-3 shadow-sm btn-sm">
                                <i class="bi bi-facebook me-2"></i> Facebook
                            </a>

                            {{-- Salin Tautan --}}
                            <button onclick="copyToClipboard()" class="btn btn-secondary rounded-pill px-3 shadow-sm btn-sm">
                                <i class="bi bi-link-45deg me-1"></i> Salin Tautan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4 mt-5 mt-lg-0">
                    <div class="sidebar-card shadow-sm">
                        <h5 class="sidebar-title">Berita Terbaru</h5>
                        @foreach($beritaLain as $item)
                            <a href="{{ route('berita.show', \Illuminate\Support\Str::slug($item->judul)) }}" class="recent-post-item">
                                <img src="{{ $item->foto ? url('/informasi_foto/' . $item->foto) : 'https://placehold.co/100x100?text=SIAKAD' }}" class="recent-post-img" alt="Thumbnail">
                                <div class="recent-post-info">
                                    <h6 class="mb-1">{{ Str::limit($item->judul, 45) }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($item->created_at)->locale('id')->diffForHumans() }}
                                    </small>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="sidebar-card text-center bg-primary-dark shadow-sm">
                        <img src="{{ asset('update/logo.png') }}" width="60" class="mb-3">
                        <h6>SIAKAD SDN PASIRIPIS</h6>
                        <p class="small opacity-75">Platform digital untuk kemajuan pendidikan kita bersama.</p>
                        <a href="{{ url('login') }}" class="btn btn-light btn-sm w-100 rounded-pill mt-2 fw-bold text-primary shadow-sm">Login Sistem</a>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <footer class="py-5 bg-light text-center border-top">
        <div class="container">
            <p class="mb-0 text-muted small">&copy; {{ date('Y') }} SD Negeri Pasiripis. Seluruh data dikelola secara digital.</p>
        </div>
    </footer>

    {{-- Toast Copy Feedback --}}
    <div id="toast-copy">Tautan berhasil disalin!</div>

    <script>
        // Fungsi Salin Tautan
        function copyToClipboard() {
            const el = document.createElement('textarea');
            el.value = window.location.href;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);

            // Show Toast
            const toast = document.getElementById("toast-copy");
            toast.className = "show";
            setTimeout(function(){ toast.className = toast.className.replace("show", ""); }, 3000);
        }

        // Fungsi Simulasi Share Instagram
        function shareToInstagram() {
            const userAgent = navigator.userAgent || navigator.vendor || window.opera;
            
            // Jika di Mobile, coba buka Instagram
            if (/android/i.test(userAgent) || /iPad|iPhone|iPod/.test(userAgent)) {
                // Catatan: Instagram Story tidak mendukung direct web-to-story URL. 
                // Kita salin link dulu dan beri instruksi.
                copyToClipboard();
                alert("Link berita sudah disalin. \n\nSilakan buka Instagram Story, tempel link menggunakan stiker 'Link'.");
                window.location.href = "instagram://camera";
            } else {
                alert("Fitur Instagram Story lebih optimal digunakan melalui perangkat seluler.");
            }
        }
    </script>

</body>
</html>