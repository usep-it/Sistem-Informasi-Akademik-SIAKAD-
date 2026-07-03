<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Cek Kelulusan - SDN Pasiripis</title>
    
    <link href="{{ asset('frontend/assets/img/logo.png') }}" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --siakad-primary: #8252fa;
            --siakad-dark: #1e293b;
            --siakad-success: #27ae60;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f1f5f9; 
            min-height: 100vh;
            display: flex;
            flex-direction: column; 
            position: relative;
            overflow-x: hidden;
        }

        .top-bar {
            background: #fff;
            padding: 15px 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 10;
        }
        .top-bar .logo span { font-weight: 800; color: var(--siakad-dark); font-size: 1.1rem; letter-spacing: 1px; }

        .main-container {
            flex-grow: 1; 
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            z-index: 10;
        }

        /* Card Style Premium */
        .portal-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 950px;
            overflow: hidden;
            position: relative;
            border: 1px solid #e2e8f0;
            border-top: 6px solid var(--siakad-primary);
        }

        .portal-header { 
            position: relative;
            z-index: 5;
            padding: 40px 30px 20px; 
            text-align: center; 
            border-bottom: 1px solid #f1f5f9;
        }
        .portal-header h3 { color: var(--siakad-primary); font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
        .portal-header h2 { font-weight: 800; color: var(--siakad-dark); margin: 0; font-size: 24px; }

        .portal-body { padding: 35px 40px 45px; }

        /* Left Side: Instructions Panel */
        .instruction-panel {
            background: #f8fafc;
            border-radius: 18px;
            padding: 25px;
            height: 100%;
            border: 1px solid #e2e8f0;
            text-align: left;
        }
        .instruction-item { display: flex; gap: 15px; margin-bottom: 20px; }
        .instruction-icon {
            width: 36px; height: 36px; 
            background: var(--siakad-primary); 
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; box-shadow: 0 4px 12px rgba(130, 82, 250, 0.25);
            flex-shrink: 0; font-weight: bold; font-size: 13px;
        }
        .instruction-text h6 { font-weight: 700; margin-bottom: 3px; font-size: 14px; color: var(--siakad-dark); }
        .instruction-text p { font-size: 11.5px; color: #64748b; margin: 0; line-height: 1.4; }

        /* Form Styling */
        .form-label { font-weight: 700; color: var(--siakad-dark); font-size: 12px; margin-bottom: 8px; text-transform: uppercase; }
        .form-control {
            border-radius: 14px;
            padding: 12px 18px;
            font-size: 14.5px;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            transition: 0.3s;
        }
        .form-control:focus { border-color: var(--siakad-primary); background: #fff; box-shadow: 0 0 0 4px rgba(130, 82, 250, 0.1); }

        .captcha-wrapper {
            background: #f0f4ff;
            border-radius: 14px;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid #e0e7ff;
        }
        .captcha-question { font-weight: 800; color: var(--siakad-primary); font-size: 18px; min-width: 70px; text-align: center; }

        .btn-cek {
            background: var(--siakad-primary);
            color: #fff;
            border-radius: 14px;
            padding: 15px;
            font-weight: 800;
            width: 100%;
            border: none;
            box-shadow: 0 4px 12px rgba(130, 82, 250, 0.2);
            transition: 0.3s;
            margin-top: 10px;
            font-size: 14px;
        }
        .btn-cek:hover { transform: translateY(-2px); color: #fff; background-color: #6d3df0; }

        /* Result Area */
        .status-badge {
            display: inline-block;
            padding: 11px 32px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 16px;
            margin: 15px 0;
            position: relative;
            z-index: 3;
        }
        .status-lulus { 
            background: #dcfce7; 
            color: #16a34a; 
            border: 2px solid #bbf7d0;
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.2);
        }
        .status-proses { background: #fef3c7; color: #d97706; border: 2px solid #fde68a; }
        .status-tidak { background: #f1f5f9; color: #64748b; border: 2px solid #e2e8f0; }
        
        .student-info { background: #f8fafc; border-radius: 20px; padding: 22px; margin-bottom: 25px; border: 1px solid #edf2f7; text-align: left; }
        .student-info p { margin-bottom: 2px; font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase; }
        .student-info strong { color: var(--siakad-dark); font-size: 16px; }

        .btn-download-skl {
            background: #1e293b; color: white !important;
            border-radius: 14px; padding: 14px; font-weight: 700; width: 100%;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            text-decoration: none !important; transition: 0.3s;
            position: relative;
            z-index: 5;
        }
        .btn-download-skl:hover { background: #000; transform: translateY(-2px); }

        .countdown-container { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
        .cd-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; border-radius: 12px; min-width: 65px; text-align: center; }
        .cd-number { display: block; font-weight: 800; color: var(--siakad-primary); font-size: 22px; }

        /* Rule Icons in Modal */
        .rule-item { display: flex; gap: 15px; margin-bottom: 12px; font-size: 14px; line-height: 1.5; color: #475569; text-align: left; }
        .rule-item i { color: #ef4444; font-size: 18px; }
        .rule-item.positive i { color: #22c55e; }
        
        .custom-checkbox-container {
            background: #f8fafc; border-radius: 15px; padding: 15px 20px;
            border: 1px solid #e2e8f0; margin-top: 25px; cursor: pointer;
        }

        .footer-portal { text-align: center; padding: 25px 0; color: #94a3b8; font-size: 12px; font-weight: 600; }

        /* =================================================================
           UI/UX CELEBRATION BOX (MERAYAKAN KEMENANGAN) - DIOPTIMALKAN (PAS)
           ================================================================= */
        .celebration-wrapper {
            position: relative;
            overflow: hidden;
            min-height: auto; /* Dioptimalkan agar tidak terlalu besar */
            margin-bottom: 20px;
            padding: 20px 20px 18px; /* Lebih tipis dan seimbang */
            border-radius: 24px;
            background: radial-gradient(circle at top, rgba(255, 255, 255, 0.5), transparent 60%), 
                        linear-gradient(135deg, #f5f3ff 0%, #edd8ff 100%);
            box-shadow: 0 15px 35px rgba(130, 82, 250, 0.15);
            border: 2px solid #ddd6fe;
        }
        
        .celebration-text {
            position: relative;
            z-index: 3;
            text-align: center;
        }
        
        .celebration-text h4 {
            color: #4c1d95;
            font-size: 20px; /* Ukuran pas sesuai standar KTP/ID Card */
            font-weight: 800;
            margin-bottom: 8px;
            text-shadow: 0 2px 10px rgba(131, 58, 180, 0.15);
            animation: bounceIn 1s ease-out;
        }

        .celebration-text p {
            color: #6d28d9;
            font-size: 12.5px; /* Menyesuaikan agar proporsional */
            font-weight: 700;
            margin-bottom: 0;
            letter-spacing: 0.5px;
        }

        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }

        /* Lembaran Bunga Petal yang Lembut */
        .petal-container {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 2;
        }

        .petal-piece {
            position: absolute;
            width: 12px;
            height: 8px;
            opacity: 0;
            transform-origin: center;
            border-radius: 60% 40% 55% 45%;
            background: linear-gradient(135deg, rgba(248, 113, 113, 0.95), rgba(251, 191, 36, 0.55));
            filter: drop-shadow(0 0 10px rgba(248, 113, 113, 0.25));
            animation: petal-fall 5.6s ease-in forwards, petal-sway 3.8s ease-in-out infinite;
        }

        .petal-piece:nth-child(4n+1) { background: linear-gradient(135deg, rgba(251, 191, 36, 0.95), rgba(251, 191, 36, 0.45)); }
        .petal-piece:nth-child(4n+2) { background: linear-gradient(135deg, rgba(244, 114, 182, 0.95), rgba(244, 114, 182, 0.45)); }
        .petal-piece:nth-child(4n+3) { background: linear-gradient(135deg, rgba(168, 85, 247, 0.95), rgba(168, 85, 247, 0.35)); }
        .petal-piece:nth-child(4n)   { background: linear-gradient(135deg, rgba(59, 130, 246, 0.95), rgba(59, 130, 246, 0.35)); }

        @keyframes petal-fall {
            0% { opacity: 0; transform: translate3d(0, -20px, 0) rotate(-15deg); }
            20% { opacity: 1; }
            100% { opacity: 0.2; transform: translate3d(14px, 340px, 0) rotate(45deg); }
        }
        @keyframes petal-sway {
            0% { transform: translateX(0) rotate(0deg); }
            50% { transform: translateX(18px) rotate(12deg); }
            100% { transform: translateX(0) rotate(-6deg); }
        }

        /* Canvas Melankolis (Full Screen Overlay) */
        #melankolisCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 9999;
        }

        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .result-box { animation: slideUp 0.5s ease-out; }
        
        .grad-icon { font-size: 80px; color: var(--siakad-primary); opacity: 0.1; position: absolute; top: 20px; right: 20px; }

        @media (max-width: 767px) {
            .portal-card { max-width: 500px; }
            .instruction-panel { margin-bottom: 30px; }
            .celebration-text h4 { font-size: 20px; }
        }
    </style>
</head>
<body>

    @php
        use App\Models\GraduationSetting;
        
        date_default_timezone_set('Asia/Jakarta');
        
        // Ambil pengaturan kelulusan yang aktif dari database
        $graduationSetting = GraduationSetting::getActive();
        $waktuBuka = $graduationSetting ? $graduationSetting->waktu_buka->format('Y-m-d H:i:s') : "2026-05-01 00:00:00";
        $waktuTutup = $graduationSetting ? $graduationSetting->waktu_tutup->format('Y-m-d H:i:s') : "2026-06-30 23:59:59";
        
        $waktuSekarang = time();
        $targetBuka = strtotime($waktuBuka);
        $targetTutup = strtotime($waktuTutup);

        $isAkanBuka = ($waktuSekarang < $targetBuka);
        $isSedangBuka = ($waktuSekarang >= $targetBuka && $waktuSekarang <= $targetTutup);
        $isSudahTutup = ($waktuSekarang > $targetTutup);

        $hasResult = session()->has('hasil_kelulusan');
        $siswaResult = session('hasil_kelulusan');
    @endphp

    {{-- Canvas untuk animasi lembut dan melankolis (Hanya dirender jika siswa Lulus) --}}
    @if($hasResult && $siswaResult->status == 'Lulus')
        <canvas id="melankolisCanvas"></canvas>
    @endif

    <div class="top-bar">
        <a href="{{ url('/') }}" class="logo text-decoration-none d-flex align-items-center">
            <img src="{{ asset('update/logo.png') }}" height="38" class="me-2">
            <span>SDN PASIRIPIS</span>
        </a>
        <a href="{{ url('/') }}" class="btn btn-sm btn-light rounded-pill px-3 border fw-bold text-muted small">BERANDA</a>
    </div>

    <div class="main-container">
        <div class="portal-card">
            
            <div class="portal-header">
                <h3>Selamat Datang di laman</h3>
                <h2>Pengumuman Kelulusan</h2>
                <p class="text-muted small mt-2">SD Negeri Pasiripis | Tahun Ajaran 2025/2026</p>
            </div>

            <div class="portal-body">
                {{-- NOTIFIKASI ERROR PENCARIAN --}}
                @if(session('error'))
                    <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4 text-center small fw-bold result-box">
                        <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                @if($isAkanBuka)
                    {{-- COUNTDOWN --}}
                    <div class="text-center py-4 result-box">
                        <i class="bx bx-time-five display-4 text-primary mb-3"></i>
                        <h5 class="fw-bold text-dark">Akses Belum Dibuka</h5>
                        <div class="countdown-container" id="countdown-timer">
                            <div class="cd-box"><span class="cd-number" id="days">00</span><span class="small text-muted">Hari</span></div>
                            <div class="cd-box"><span class="cd-number" id="hours">00</span><span class="small text-muted">Jam</span></div>
                            <div class="cd-box"><span class="cd-number" id="minutes">00</span><span class="small text-muted">Menit</span></div>
                            <div class="cd-box"><span class="cd-number text-danger" id="seconds">00</span><span class="small text-muted">Detik</span></div>
                        </div>
                    </div>

                @elseif($isSudahTutup)
                    {{-- TUTUP --}}
                    <div class="text-center py-5 result-box">
                        <i class="bx bx-lock-alt display-3 text-danger opacity-25 mb-3"></i>
                        <h5 class="fw-bold text-danger">Akses Telah Ditutup</h5>
                        <p class="text-muted small">Masa pengecekan online telah berakhir.</p>
                    </div>

                @else
                    {{-- HASIL ATAU FORM --}}
                    @if($hasResult)
                        @php $siswa = $siswaResult; @endphp
                        <div class="text-center result-box">
                            
                            {{-- LOGIKA JIKA SISWA KELAS 1-5 --}}
                            @if($siswa->kelas && $siswa->kelas->kelas < 6)
                                <div class="student-info shadow-sm">
                                    <p>Nama Lengkap</p>
                                    <strong>{{ $siswa->nama }}</strong>
                                    <hr class="my-2 opacity-5">
                                    <p>NISN / NIS</p>
                                    <strong>{{ $siswa->nisn }} / {{ $siswa->nis ?? '-' }}</strong>
                                    <hr class="my-2 opacity-5">
                                    <p>Kelas Saat Ini</p>
                                    <strong>Kelas {{ $siswa->kelas->kelas }}</strong>
                                </div>
                                <div class="status-badge status-tidak shadow-sm"><i class="bx bxs-face me-1"></i> BELUM WAKTUNYA</div>
                                <p class="text-muted small mb-4 px-lg-5">
                                    Halo <strong>{{ explode(' ', $siswa->nama)[0] }}</strong>! Kamu masih berada di bangku <strong>Kelas {{ $siswa->kelas->kelas }}</strong>. <br>
                                    Teruslah rajin belajar ya, perjalananmu masih panjang. Fitur ini hanya untuk kakak-kakak Kelas VI.
                                </p>
                            
                            {{-- LOGIKA JIKA SISWA KELAS 6 & LULUS --}}
                            @elseif($siswa->status == 'Lulus')
                                <div class="celebration-wrapper result-box">
                                    <div class="celebration-text">
                                        <h4>Selamat & Sukses! Anda Dinyatakan Lulus</h4>
                                        <p>SD Negeri Pasiripis mengapresiasi tinggi atas segala perjuangan, ketekunan, dan prestasi belajar yang telah Anda raih.</p>
                                    </div>
                                    <div class="petal-container"></div>
                                </div>

                                <div class="student-info shadow-sm">
                                    <p>Nama Lengkap</p>
                                    <strong>{{ $siswa->nama }}</strong>
                                    <hr class="my-2 opacity-5">
                                    <p>NISN / NIS</p>
                                    <strong>{{ $siswa->nisn }} / {{ $siswa->nis ?? '-' }}</strong>
                                    <hr class="my-2 opacity-5">
                                    <p>Kelas Saat Ini</p>
                                    <strong>Kelas {{ $siswa->kelas->kelas ?? '6' }} (Alumni)</strong>
                                </div>

                                {{-- PESAN MOTIVASI DARI WALI KELAS --}}
                                <div class="text-start p-4 mb-4 shadow-sm" style="background: #fffbeb; border-left: 4px solid #fbbf24; border-radius: 16px; border-top: 1px solid #fef3c7; border-right: 1px solid #fef3c7; border-bottom: 1px solid #fef3c7;">
                                    <small class="text-muted d-block text-uppercase font-weight-bold mb-2" style="font-size: 10px; letter-spacing: 0.5px;">
                                        <i class="bi bi-chat-quote-fill text-warning me-1"></i> Pesan dari Wali Kelas
                                    </small>
                                    <p class="text-secondary fst-italic mb-3" style="font-size: 13px; line-height: 1.6;">
                                        "Anaking, geus cunduk na waktu geus ninggang na mangsa. Upamana beurang paselang jeung peuting, kitu deui ayeuna, aya tepung tangtu aya pipisahan. Bihari hidep nyuprih elmu di ieu sakola. Kiwari hidep geus bisa nuntaskeunana. Bral... Anaking, Ibu ngan sakur ngajurung, muga-muga cita-cita sing laksana tinekanan."
                                    </p>
                                    <span class="font-weight-bold text-dark small"><i class="bi bi-person-fill text-muted me-1"></i> {{ $siswa->kelas->pegawai->nama ?? 'ELIS SULASTRI, S.Pd.SD' }}</span>
                                </div>

                                @if($siswa->link_skl)
                                    <a href="{{ $siswa->link_skl }}" target="_blank" class="btn-download-skl shadow-sm mb-3">
                                        <i class="bi bi-file-earmark-pdf-fill"></i> UNDUH SURAT KETERANGAN LULUS (SKL)
                                    </a>
                                @endif

                            {{-- LOGIKA JIKA SISWA KELAS 6 TETAPI MASIH AKTIF --}}
                            @elseif($siswa->kelas && $siswa->kelas->kelas == 6 && $siswa->status == 'Aktif')
                                <div class="student-info shadow-sm">
                                    <p>Nama Lengkap</p>
                                    <strong>{{ $siswa->nama }}</strong>
                                    <hr class="my-2 opacity-5">
                                    <p>NISN / NIS</p>
                                    <strong>{{ $siswa->nisn }} / {{ $siswa->nis ?? '-' }}</strong>
                                    <hr class="my-2 opacity-5">
                                    <p>Kelas Saat Ini</p>
                                    <strong>Kelas {{ $siswa->kelas->kelas }}</strong>
                                </div>
                                <div class="status-badge status-proses shadow-sm"><i class="bx bxs-hourglass-top me-1"></i> DALAM VERIFIKASI</div>
                                <p class="text-muted small mb-4 px-lg-5">
                                    Data ditemukan. Namun, saat ini status kelulusanmu <strong>masih dalam tahap verifikasi akhir</strong>. Silakan cek kembali beberapa saat lagi.
                                </p>
                            @else
                                <div class="status-badge status-tidak shadow-sm"><i class="bx bxs-x-circle me-1"></i> DATA TIDAK TERSEDIA</div>
                            @endif
                            
                            <a href="{{ route('cek-kelulusan') }}" class="btn btn-light rounded-pill w-100 mt-4 fw-bold border shadow-sm">KEMBALI KE PENCARIAN</a>
                        </div>
                    @else
                        {{-- FORM DENGAN PETUNJUK --}}
                        <div class="row">
                            <div class="col-md-5">
                                <div class="instruction-panel">
                                    <h5 class="text-dark font-weight-bold mb-4" style="font-size: 16px;">Petunjuk Penggunaan:</h5>
                                    
                                    <div class="instruction-item">
                                        <div class="instruction-icon">1</div>
                                        <div class="instruction-text">
                                            <h6>Siapkan NISN</h6>
                                            <p>Gunakan 10 digit nomor induk siswa nasional Anda.</p>
                                        </div>
                                    </div>
                                    <div class="instruction-item">
                                        <div class="instruction-icon">2</div>
                                        <div class="instruction-text">
                                            <h6>Input Tgl Lahir</h6>
                                            <p>Pilih tanggal, bulan, dan tahun lahir sesuai akta.</p>
                                        </div>
                                    </div>
                                    <div class="instruction-item">
                                        <div class="instruction-icon">3</div>
                                        <div class="instruction-text">
                                            <h6>Verifikasi Keamanan</h6>
                                            <p>Selesaikan hitungan angka sederhana untuk mengakses formulir.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 p-3 bg-white rounded-4 border-0 text-center shadow-sm">
                                        <i class="bx bxs-help-circle text-primary fs-4"></i>
                                        <p class="small text-muted mt-1">Kesulitan? Hubungi Operator.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <form id="form-cek-kelulusan" action="{{ route('cek-kelulusan.proses') }}" method="POST" class="mt-2">
                                    @csrf
                                    <div class="mb-4 text-start">
                                        <label class="form-label">NISN (10 Digit)</label>
                                        <input type="number" name="nisn" class="form-control shadow-sm" placeholder="Contoh: 0123456789" required autofocus>
                                    </div>
                                    <div class="mb-4 text-start">
                                        <label class="form-label">Tanggal Lahir</label>
                                        <input type="date" name="ttl" class="form-control shadow-sm" required>
                                    </div>

                                    <div class="mb-4 text-start">
                                        <label class="form-label">Verifikasi Keamanan</label>
                                        <div class="captcha-wrapper shadow-sm">
                                            <div class="captcha-question" id="captcha-display">...</div>
                                            <input type="number" id="user-answer" class="form-control border-0 bg-transparent p-0 font-weight-bold" placeholder="?" required style="font-size: 18px;">
                                        </div>
                                        <small id="captcha-msg" class="text-danger mt-2 d-none font-weight-bold">Jawaban salah!</small>
                                    </div>

                                    <button type="submit" class="btn-cek shadow">LIHAT HASIL SEKARANG</button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    
    <div class="footer-portal">&copy; {{ date('Y') }} PORTAL AKADEMIK SDN PASIRIPIS</div>

    {{-- ========================== MODAL HIMBAUAN ========================== --}}
    {{-- FIX LOGIKAL: Modal tidak akan dirender ke HTML jika ada session error atau session data ditemukan, 
        namun jika halaman direfresh manual (session terhapus), modal akan dirender kembali secara utuh. --}}
    @if($isSedangBuka && !session()->has('hasil_kelulusan') && !session()->has('error'))
    <div class="modal fade" id="modalHimbauan" tabindex="-1" role="dialog" aria-labelledby="modalHimbauanLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content shadow-lg">
                <div class="modal-header d-flex flex-column text-center">
                    <h3 class="text-primary font-weight-bold mb-1" style="font-size: 14px; letter-spacing: 2px;">PENGUMUMAN KELULUSAN</h3>
                    <h2 class="text-dark font-weight-bold mb-1" style="font-size: 22px;">SD NEGERI PASIRIPIS</h2>
                    <p class="text-muted small mb-0">TAHUN AJARAN 2025/2026</p>
                </div>
                <div class="modal-body">
                    <h5 class="font-weight-bold text-dark text-center mb-4">HIMBAUAN PASCA PENGUMUMAN KELULUSAN</h5>
                    
                    <p class="small text-muted mb-3">Kepada seluruh peserta didik Kelas VI SD Negeri Pasiripis Tahun Ajaran 2025/2026 untuk <strong>tidak melakukan</strong> hal-hal berikut:</p>
                    
                    <div class="rule-item">
                        <i class="bx bxs-x-circle"></i>
                        <span>Berkumpul berlebihan yang mengganggu ketertiban umum di jalanan maupun tempat umum lainnya.</span>
                    </div>
                    <div class="rule-item">
                        <i class="bx bxs-x-circle"></i>
                        <span>Melakukan tindakan perundungan (bullying) atau perilaku tidak terpuji lainnya kepada sesama rekan.</span>
                    </div>
                    <div class="rule-item">
                        <i class="bx bxs-x-circle"></i>
                        <span>Melakukan perayaan berlebihan (corat-coret seragam, konvoi) yang membahayakan diri sendiri maupun orang lain.</span>
                    </div>

                    <hr class="my-4">

                    <p class="small text-muted mb-3">Kami mengimbau seluruh siswa untuk merayakan momen ini dengan cara yang positif:</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="rule-item positive">
                                <i class="bx bxs-check-circle"></i>
                                <span>Bersyukur kepada Tuhan Yang Maha Esa.</span>
                            </div>
                            <div class="rule-item positive">
                                <i class="bx bxs-check-circle"></i>
                                <span>Menghormati orang tua dan bapak/ibu guru.</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="rule-item positive">
                                <i class="bx bxs-check-circle"></i>
                                <span>Tetap disiplin dan menjaga nama baik sekolah.</span>
                            </div>
                            <div class="rule-item positive">
                                <i class="bx bxs-check-circle"></i>
                                <span>Tetap berakhlak baik di mana pun berada.</span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning border-0 small mt-3 py-2 px-3" style="border-radius: 12px; background: #fffbeb; color: #92400e;">
                        <i class="bx bx-info-circle mr-1"></i> Pelanggaran terhadap aturan di atas akan diberikan pembinaan sesuai ketentuan sekolah.
                    </div>

                    <div class="custom-checkbox-container d-flex align-items-center" id="checkboxWrapper">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="checkPaham" style="cursor: pointer;">
                            <label class="form-check-label font-weight-bold text-dark ml-2" for="checkPaham" style="font-size: 13px; cursor: pointer;">
                                Saya sudah membaca dan memahami informasi di atas
                            </label>
                        </div>
                    </div>

                    <button type="button" class="btn-cek shadow-sm" id="btnLanjut" disabled style="opacity: 0.5;">
                        LANJUTKAN KE CEK KELULUSAN <i class="bx bx-right-arrow-alt ml-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('frontend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- 0. AUDIO MELODI KEMENANGAN (CUSTOM AUDIO FILE PLAYER) ---
            function playFanfare() {
                try {
                    // Sistem akan memanggil file victory.mp3 dari folder public/update/audio/ secara dinamis
                    const audioPath = "{{ asset('update/audio/kls6.mp3') }}";
                    const audio = new Audio(audioPath);
                    
                    audio.volume = 0.6; // Mengatur volume suara (0.0 sampai 1.0)
                    audio.play();
                } catch (e) {
                    console.warn("Autoplay diblokir browser atau file audio tidak ditemukan:", e);
                }
            }

            // --- 1. LOGIKA MODAL HIMBAUAN (ALWAYS ON REFRESH) ---
            @if($isSedangBuka && !$hasResult && !session()->has('error'))
                const modalEl = document.getElementById('modalHimbauan');
                if(modalEl) {
                    const myModal = new bootstrap.Modal(modalEl);
                    myModal.show();

                    const checkbox = document.getElementById('checkPaham');
                    const btnLanjut = document.getElementById('btnLanjut');
                    const wrapper = document.getElementById('checkboxWrapper');

                    checkbox.addEventListener('change', function() {
                        if(this.checked) {
                            btnLanjut.disabled = false;
                            btnLanjut.style.opacity = "1";
                            wrapper.style.borderColor = "var(--siakad-primary)";
                            wrapper.style.background = "#f0f4ff";
                        } else {
                            btnLanjut.disabled = true;
                            btnLanjut.style.opacity = "0.5";
                            wrapper.style.borderColor = "#e2e8f0";
                            wrapper.style.background = "#f8fafc";
                        }
                    });

                    btnLanjut.addEventListener('click', function() {
                        myModal.hide();
                    });
                }
            @endif

            // --- 2. LOGIKA CAPTCHA PENJUMLAHAN ---
            const form = document.getElementById('form-cek-kelulusan');
            if(form) {
                const captchaDisplay = document.getElementById('captcha-display');
                const userAnswer = document.getElementById('user-answer');
                const captchaMsg = document.getElementById('captcha-msg');
                
                let num1 = Math.floor(Math.random() * 10) + 1;
                let num2 = Math.floor(Math.random() * 10) + 1;
                let result = num1 + num2;
                
                captchaDisplay.innerText = `${num1} + ${num2} =`;

                form.addEventListener('submit', function(e) {
                    if (parseInt(userAnswer.value) !== result) {
                        e.preventDefault();
                        captchaMsg.classList.remove('d-none');
                        userAnswer.classList.add('is-invalid');
                        
                        // Reset numbers on fail
                        num1 = Math.floor(Math.random() * 10) + 1;
                        num2 = Math.floor(Math.random() * 10) + 1;
                        result = num1 + num2;
                        captchaDisplay.innerText = `${num1} + ${num2} =`;
                        userAnswer.value = '';
                    }
                });

                userAnswer.addEventListener('input', function() {
                    captchaMsg.classList.add('d-none');
                    userAnswer.classList.remove('is-invalid');
                });
            }

            // --- 3. RENDERING BLOSSOM PETALS ---
            const petalRoot = document.querySelector('.petal-container');
            if (petalRoot) {
                const count = 45;
                for (let i = 0; i < count; i++) {
                    const piece = document.createElement('span');
                    piece.className = 'petal-piece';
                    const startLeft = Math.random() * 100;
                    const delay = Math.random() * 0.8;
                    const duration = 4.5 + Math.random() * 1.8;
                    piece.style.left = `${startLeft}%`;
                    piece.style.top = `${-20 - Math.random() * 40}px`;
                    piece.style.width = `${8 + Math.random() * 10}px`;
                    piece.style.height = `${6 + Math.random() * 6}px`;
                    piece.style.animationDelay = `${delay}s`;
                    piece.style.animationDuration = `${duration}s`;
                    piece.style.transform = `rotate(${Math.random() * 360}deg)`;
                    petalRoot.appendChild(piece);
                }
            }

            // --- 4. HIGH-PERFORMANCE CANVAS MELANKOLIS ---
            const pCanvas = document.getElementById('melankolisCanvas');
            if (pCanvas) {
                // Mainkan lagu haru/sedih saat hasil lulus muncul
                playFanfare();

                const ctx = pCanvas.getContext('2d');
                let petals = [];

                function resizeCanvas() {
                    pCanvas.width = window.innerWidth;
                    pCanvas.height = window.innerHeight;
                }
                resizeCanvas();
                window.addEventListener('resize', resizeCanvas);

                class Petal {
                    constructor() {
                        this.x = Math.random() * pCanvas.width;
                        this.y = -20 - Math.random() * 60;
                        this.size = 12 + Math.random() * 10;
                        this.angle = Math.random() * Math.PI * 2;
                        this.speed = 0.8 + Math.random() * 0.9;
                        this.vx = Math.sin(this.angle) * 0.4;
                        this.vy = this.speed;
                        this.alpha = 0;
                        this.fadeIn = 0.02 + Math.random() * 0.02;
                        this.color = `hsla(${320 + Math.random() * 40}, 85%, ${55 + Math.random() * 15}%, 1)`;
                        this.swing = Math.random() * 0.015 + 0.005;
                        this.rotation = Math.random() * Math.PI * 2;
                    }

                    update() {
                        this.x += this.vx;
                        this.y += this.vy;
                        this.rotation += this.swing;
                        if (this.alpha < 1) this.alpha += this.fadeIn;
                        return this.y < pCanvas.height + 30;
                    }

                    draw() {
                        ctx.save();
                        ctx.globalAlpha = this.alpha * 0.85;
                        ctx.translate(this.x, this.y);
                        ctx.rotate(this.rotation);
                        ctx.fillStyle = this.color;
                        ctx.beginPath();
                        ctx.ellipse(0, 0, this.size * 0.48, this.size * 0.28, Math.PI / 6, 0, Math.PI * 2);
                        ctx.fill();
                        ctx.restore();
                    }
                }

                function spawnPetal() {
                    if (petals.length < 30) {
                        petals.push(new Petal());
                    }
                    setTimeout(spawnPetal, Math.random() * 1100 + 700);
                }
                spawnPetal();

                function animate() {
                    ctx.clearRect(0, 0, pCanvas.width, pCanvas.height);
                    for (let i = petals.length - 1; i >= 0; i--) {
                        const petal = petals[i];
                        if (!petal.update()) {
                            petals.splice(i, 1);
                            continue;
                        }
                        petal.draw();
                    }
                    requestAnimationFrame(animate);
                }
                animate();
            }

            // --- 5. LOGIKA COUNTDOWN ---
            @if($isAkanBuka)
                const targetDate = new Date("{{ $waktuBuka }}").getTime();
                const interval = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = targetDate - now;

                    const d = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById("days").innerText = d < 10 ? '0'+d : d;
                    document.getElementById("hours").innerText = h < 10 ? '0'+h : h;
                    document.getElementById("minutes").innerText = m < 10 ? '0'+m : m;
                    document.getElementById("seconds").innerText = s < 10 ? '0'+s : s;

                    if (distance < 0) {
                        clearInterval(interval);
                        location.reload();
                    }
                }, 1000);
            @endif
        });
    </script>
</body>
</html>