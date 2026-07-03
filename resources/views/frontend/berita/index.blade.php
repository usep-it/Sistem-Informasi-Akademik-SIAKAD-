<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Berita & Informasi - SDN Pasiripis</title>
    <link href="{{ asset('frontend/assets/img/logo.png') }}" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; color: #444; }
        .bg-primary-dark { background-color: #2c3e50; color: white; }
        
        /* Hero Berita */
        .news-header {
            padding: 80px 0 40px;
            background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.9)), url('https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
        }

        /* Card Berita */
        .news-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            background: #fff;
            height: 100%;
        }
        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .news-img-wrapper {
            height: 220px;
            overflow: hidden;
        }
        .news-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .news-card:hover .news-img-wrapper img { transform: scale(1.1); }
        .news-body { padding: 25px; }
        .news-date { font-size: 13px; color: #3498db; font-weight: 600; margin-bottom: 10px; display: block; }
        .news-title { font-size: 18px; font-weight: 700; line-height: 1.4; margin-bottom: 15px; color: #2c3e50; }
        .news-excerpt { font-size: 14px; color: #7f8c8d; margin-bottom: 20px; }
        
        .btn-read {
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 2px solid #3498db;
            color: #3498db;
            transition: 0.3s;
            display: inline-block;
        }
        .btn-read:hover { background: #3498db; color: #fff; }

        /* Pagination Styling */
        .pagination .page-link { border-radius: 50%; margin: 0 5px; color: #2c3e50; }
        .pagination .page-item.active .page-link { background-color: #3498db; border-color: #3498db; }
    </style>
</head>
<body>

    <!-- Navbar Sederhana -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('update/logo.png') }}" width="40" class="me-2">
                <span class="fw-bold text-dark">SDN PASIRIPIS</span>
            </a>
            <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </nav>

    <header class="news-header">
        <div class="container">
            <h1 class="fw-bold">Berita & Informasi</h1>
            <p>Ikuti terus perkembangan kegiatan dan prestasi di SD Negeri Pasiripis</p>
            
            <div class="row justify-content-center mt-4">
                <div class="col-md-6">
                    <form action="{{ route('berita.index') }}" method="GET" class="input-group shadow-sm">
                        <input type="text" name="cari" class="form-control border-0 py-3 ps-4" placeholder="Cari berita atau pengumuman..." value="{{ request('cari') }}" style="border-radius: 50px 0 0 50px;">
                        <button class="btn btn-primary px-4" type="submit" style="border-radius: 0 50px 50px 0;">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="py-5">
        <div class="container">
            @if(request('cari'))
                <div class="mb-4">
                    <h5>Hasil pencarian untuk: <span class="text-primary">"{{ request('cari') }}"</span></h5>
                    <a href="{{ route('berita.index') }}" class="text-muted small">Bersihkan Pencarian</a>
                </div>
            @endif

            <div class="row g-4">
                @forelse($berita as $item)
                    <div class="col-md-6 col-lg-4">
                        <div class="news-card shadow-sm">
                            <div class="news-img-wrapper">
                                <img src="{{ $item->foto ? url('/informasi_foto/' . $item->foto) : 'https://placehold.co/600x400?text=SIAKAD+Informasi' }}" alt="{{ $item->judul }}">
                            </div>
                            <div class="news-body">
                                <span class="news-date">
                                    <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMMM YYYY') }}
                                </span>
                                <h5 class="news-title">{{ Str::limit($item->judul, 60) }}</h5>
                                <p class="news-excerpt">{{ Str::limit(strip_tags($item->isi), 100) }}</p>
                                
                                {{-- PERBAIKAN: Menggunakan Str::slug agar link sesuai dengan Controller --}}
                                <a href="{{ route('berita.show', \Illuminate\Support\Str::slug($item->judul)) }}" class="btn-read">Baca Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="100" class="mb-3 opacity-50">
                        <h4 class="text-muted">Maaf, berita belum tersedia.</h4>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $berita->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </main>

    <footer class="py-4 bg-white border-top text-center">
        <p class="mb-0 text-muted small">&copy; {{ date('Y') }} SD Negeri Pasiripis. All Rights Reserved.</p>
    </footer>

</body>
</html>