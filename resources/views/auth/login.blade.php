@extends('layouts.app')

@section('content')
<section class="login-section">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-4">
            <div class="col-12 col-xl-10">
                <div class="card login-card shadow-lg border-0 overflow-hidden">
                    <div class="row g-0 align-items-stretch min-h-100">

                        
                        <div class="col-lg-6 d-none d-lg-block position-relative bg-image-left">
                            <div class="overlay"></div>
                            <div class="position-relative z-1 text-white p-5 d-flex flex-column h-100 justify-content-end">
                                <div class="mb-4">
                                    <h4 class="fw-bold mb-1" style="letter-spacing: 1px;">PORTAL AKADEMIK</h4>
                                    <h2 class="fw-bolder display-6">SIAKAD SDN PASIRIPIS</h2>
                                </div>
                                <p class="mb-0" style="font-size: 1.05rem; opacity: 0.9; line-height: 1.6; border-left: 4px solid #3498db; padding-left: 15px;">
                                    “Mewujudkan pendidikan yang unggul, berkarakter, dan berprestasi melalui ekosistem digital yang terintegrasi.”
                                </p>
                            </div>
                        </div>

                       
                        <div class="col-lg-6 bg-white p-4 p-md-5 d-flex flex-column justify-content-center right-panel">
                            
                           
                            <div class="d-flex justify-content-end w-100 mb-2">
                                <a href="{{ url('/') }}" class="text-decoration-none text-muted back-link">
                                    <i class="fas fa-home me-1"></i> Beranda
                                </a>
                            </div>

                            <div class="text-center mb-4">
                                <img src="{{ asset('update/img/logo.png') }}" alt="Logo SDN Pasiripis" width="75" class="mb-3 drop-shadow-sm">
                                <h3 class="fw-bold text-dark mb-1">Selamat Datang</h3>
                                <p class="text-muted small mb-0">Silakan masuk menggunakan akun SIAKAD Anda.</p>
                            </div>

                            {{-- Notifikasi Sukses --}}
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- Notifikasi Error --}}
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- Validasi Error Form --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                    <strong class="d-block mb-1"><i class="fas fa-times-circle me-1"></i> Validasi Gagal:</strong>
                                    <ul class="mb-0 ps-3 small">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- FORM LOGIN --}}
                            <form method="POST" action="{{ route('login') }}" class="login-form px-sm-3">
                                @csrf

                                {{-- ROLE AKSES --}}
                                <div class="mb-4">
                                    <label for="role" class="form-label fw-semibold text-dark small text-uppercase">Akses Sebagai</label>
                                    <div class="input-group custom-input-group">
                                        <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                                            <option value="" disabled selected>-- Pilih Hak Akses --</option>
                                            <option value="Dev" {{ old('role') == 'Dev' ? 'selected' : '' }}>Administrator</option>
                                            <option value="Guru" {{ old('role') == 'Guru' ? 'selected' : '' }}>Guru / Tenaga Pendidik</option>
                                            <option value="Siswa" {{ old('role') == 'Siswa' ? 'selected' : '' }}>Siswa / Peserta Didik</option>
                                        </select>
                                    </div>
                                    <div id="role-info" class="form-text text-primary small mt-2" style="display: none; font-weight: 500;">
                                        <i class="fas fa-info-circle me-1"></i> Kepala Sekolah & Tenaga Administrasi silakan memilih <strong>Guru</strong>.
                                    </div>
                                </div>

                                {{-- USERNAME --}}
                                <div class="mb-4">
                                    <label for="login" class="form-label fw-semibold text-dark small text-uppercase">Username / NIS</label>
                                    <div class="input-group custom-input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" placeholder="Masukkan Username / NIS" required autofocus>
                                    </div>
                                </div>

                                {{-- PASSWORD --}}
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-semibold text-dark small text-uppercase">Kata Sandi</label>
                                    <div class="input-group custom-input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Masukkan Kata Sandi" required>
                                        <button class="btn btn-outline-secondary toggle-pw" type="button" id="togglePassword">
                                            <i class="fas fa-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- SUBMIT BUTTON --}}
                                <div class="d-grid mt-5">
                                    <button type="submit" class="btn btn-primary btn-login fw-bold py-3 text-uppercase shadow-sm">
                                        Masuk Sistem <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </form>
                            
                            <div class="text-center mt-5 text-muted small copyright">
                                &copy; {{ date('Y') }} <strong>SD NEGERI PASIRIPIS</strong>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================== SCRIPT & INTERAKSI ===================== --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Auto-close alerts (menghilang mulus setelah 5 detik)
    const alerts = document.querySelectorAll('.alert.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                if (bsAlert) bsAlert.close();
            }
        }, 5000); 
    });

    // 2. Fitur Tampilkan/Sembunyikan Password
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');

    if (togglePassword && password && icon) {
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }

    // 3. Tampilkan Info Bantuan Role Guru
    const roleSelect = document.getElementById('role');
    const roleInfo = document.getElementById('role-info');

    if (roleSelect && roleInfo) {
        function toggleRoleInfo() {
            if (roleSelect.value === 'Guru') {
                roleInfo.style.display = 'block';
            } else {
                roleInfo.style.display = 'none';
            }
        }
        toggleRoleInfo(); // Cek saat load
        roleSelect.addEventListener('change', toggleRoleInfo); // Cek saat diubah
    }
});
</script>

{{-- ===================== DESAIN CSS PROFESIONAL ===================== --}}
<style>
/* Import Font */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

/* Typography Dasar */
body {
    font-family: 'Inter', sans-serif;
    background-color: #f4f7f6;
    color: #4a5568;
}

/* Kartu & Layout */
.login-card {
    border-radius: 24px !important;
    box-shadow: 0 1.5rem 4rem rgba(0,0,0,0.08) !important;
    background: #fff;
    min-height: 600px;
}
.min-h-100 {
    min-height: 600px;
}

/* Kolom Kiri - Gambar */
.bg-image-left {
    background: url('{{ asset('update/img/sd.jpg') }}') center center / cover no-repeat;
    position: relative;
}
.overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    /* Gradient yang elegan dan profesional */
    background: linear-gradient(180deg, rgba(13, 110, 253, 0.1) 0%, rgba(20, 40, 80, 0.8) 60%, rgba(15, 23, 42, 0.95) 100%);
}

/* Kolom Kanan - Input Seamless */
.custom-input-group {
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    transition: all 0.3s ease;
}
.custom-input-group .input-group-text {
    background-color: #fff;
    border: 1px solid #e2e8f0;
    border-right: none;
    color: #a0aec0;
    border-top-left-radius: 12px;
    border-bottom-left-radius: 12px;
    padding-left: 1rem;
    padding-right: 0.5rem;
}
.custom-input-group .form-control, 
.custom-input-group .form-select {
    border: 1px solid #e2e8f0;
    border-left: none;
    padding: 14px 15px;
    font-size: 15px;
    box-shadow: none;
    background: #fff;
    border-top-right-radius: 12px;
    border-bottom-right-radius: 12px;
}
.custom-input-group .form-select {
    cursor: pointer;
}

/* Efek Focus Seamless (Glow Biru) */
.custom-input-group:focus-within {
    box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.15);
}
.custom-input-group:focus-within .input-group-text, 
.custom-input-group:focus-within .form-control, 
.custom-input-group:focus-within .form-select,
.custom-input-group:focus-within .toggle-pw {
    border-color: #3498db;
}

/* Tombol Tampilkan Password */
.toggle-pw {
    border: 1px solid #e2e8f0;
    border-left: none;
    background: #fff;
    color: #a0aec0;
    border-top-right-radius: 12px !important;
    border-bottom-right-radius: 12px !important;
}
.toggle-pw:hover {
    color: #4a5568;
    background: #f8f9fa;
}

/* Tombol Login Utama */
.btn-login {
    background: #3498db;
    border: none;
    border-radius: 12px;
    letter-spacing: 0.5px;
    font-size: 15px;
    transition: all 0.3s;
}
.btn-login:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3) !important;
}

/* Tautan Kembali */
.back-link {
    font-size: 14px;
    font-weight: 500;
    transition: color 0.2s;
    background: #f8f9fa;
    padding: 8px 15px;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
}
.back-link:hover {
    color: #3498db !important;
    background: #eef2f7;
    border-color: #3498db;
}

/* Utilities */
.drop-shadow-sm { filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05)); }
.text-dark { color: #1e293b !important; }

/* Responsivitas Mobile */
@media (max-width: 991.98px) {
    .login-card { border-radius: 16px !important; }
    .right-panel { padding-top: 1.5rem !important; }
}
</style>
@endsection