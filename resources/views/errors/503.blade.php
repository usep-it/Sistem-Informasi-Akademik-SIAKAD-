<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situs Sedang Diperbarui - SDN Pasiripis</title>
    
    <link href="{{ asset('update/logo.png') }}" rel="icon">
    <!-- Google Fonts, Tailwind CSS & Boxicons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        :root {
            --siakad-primary: #8252fa;
            --siakad-secondary: #eca2f1;
            --siakad-dark: #1e293b;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f3f8fa 0%, #eef2ff 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        /* Gear Rotation Animation */
        @keyframes gear-rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes gear-rotate-reverse {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }
        .gear-main { animation: gear-rotate 12s linear infinite; }
        .gear-second { animation: gear-rotate-reverse 8s linear infinite; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between p-4 sm:p-6">

    <!-- Top Navigation -->
    <header class="w-full max-w-4xl mx-auto flex justify-between items-center py-4 px-6 bg-white/80 backdrop-blur-md rounded-2xl border border-white/50 shadow-sm mt-2">
        <div class="flex items-center gap-3">
            <img src="{{ asset('update/logo.png') }}" alt="Logo" class="h-10 w-auto">
            <span class="font-extrabold text-slate-800 tracking-wide text-sm sm:text-base">SIAKAD SDN PASIRIPIS</span>
        </div>
        <span class="px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold border border-indigo-100 flex items-center gap-2">
            <span class="flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
            </span>
            Sistem Maintenance
        </span>
    </header>

    <!-- Main Container (Centered & Professional Layout) -->
    <main class="w-full max-w-2xl mx-auto my-6 flex flex-col gap-6 items-stretch">
        
        <!-- Status Pemeliharaan Card -->
        <div class="glass-card p-6 sm:p-10 rounded-[28px] shadow-xl text-center relative overflow-hidden">
            
            <!-- Animated SVG Tech Illustration (Professional Server & Gears) -->
            <div class="flex justify-center mb-6 relative">
                <svg class="w-32 h-32 text-indigo-500" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Server Base -->
                    <rect x="25" y="45" width="50" height="40" rx="4" fill="#e2e8f0" stroke="#4f46e5" stroke-width="3"/>
                    <line x1="30" y1="55" x2="70" y2="55" stroke="#4f46e5" stroke-width="3" stroke-linecap="round"/>
                    <line x1="30" y1="65" x2="60" y2="65" stroke="#4f46e5" stroke-width="3" stroke-linecap="round"/>
                    <line x1="30" y1="75" x2="50" y2="75" stroke="#4f46e5" stroke-width="3" stroke-linecap="round"/>
                    <!-- Server Lights -->
                    <circle cx="70" cy="65" r="3" fill="#22c55e" class="animate-pulse"/>
                    <circle cx="70" cy="75" r="3" fill="#3b82f6" class="animate-pulse" style="animation-delay: 0.5s;"/>
                    
                    <!-- Decorative Gears -->
                    <g class="gear-main" style="transform-origin: 32px 28px;">
                        <circle cx="32" cy="28" r="10" stroke="#8252fa" stroke-width="3" stroke-dasharray="4 2"/>
                        <circle cx="32" cy="28" r="4" fill="#8252fa"/>
                    </g>
                    <g class="gear-second" style="transform-origin: 68px 24px;">
                        <circle cx="68" cy="24" r="14" stroke="#6366f1" stroke-width="3" stroke-dasharray="6 3"/>
                        <circle cx="68" cy="24" r="5" fill="#6366f1"/>
                    </g>
                </svg>
            </div>

            <h3 class="text-[#8252fa] font-extrabold text-xs tracking-widest uppercase mb-2">Pembaruan Infrastruktur</h3>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 leading-tight mb-4">
                Sistem Sedang Dalam<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-600">Pemeliharaan Berkala</span>
            </h1>
            <p class="text-slate-500 text-sm leading-relaxed max-w-md mx-auto mb-8">
                Kami sedang melakukan optimalisasi pangkalan data dan pembaruan sistem keamanan server SIAKAD SDN Pasiripis untuk pengalaman akses yang lebih cepat dan stabil.
            </p>
            
            <!-- Progress Bar Area -->
            <div class="max-w-md mx-auto bg-slate-50 border border-slate-100 p-4 rounded-2xl shadow-inner">
                <div class="mb-2 flex justify-between items-center text-xs font-bold text-slate-600">
                    <span class="flex items-center gap-1.5">
                        <i class="bx bx-loader-alt animate-spin text-indigo-500"></i>
                        Proses Sinkronisasi Data
                    </span>
                    <span id="progress-text" class="text-indigo-600 font-extrabold">0%</span>
                </div>
                <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden p-0.5">
                    <div id="progress-bar" class="bg-gradient-to-r from-indigo-500 to-purple-500 h-full rounded-full transition-all duration-1000 ease-out" style="width: 0%"></div>
                </div>
            </div>

            <!-- Estimasi Selesai Badge -->
            <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 rounded-xl text-xs font-bold border border-amber-100">
                <i class="bx bx-time-five text-sm"></i>
                Estimasi Pengerjaan: 10 - 20 Menit
            </div>
        </div>

        <!-- FAQ & Support Section -->
        <div class="glass-card p-6 sm:p-8 rounded-[24px] shadow-md">
            <h4 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="bx bx-info-circle text-lg text-[#8252fa]"></i> Tanya & Jawab Resmi
            </h4>
            <div class="space-y-3">
                <!-- FAQ 1 -->
                <div class="border border-slate-100 rounded-xl overflow-hidden bg-white/60">
                    <button onclick="toggleFaq(1)" class="w-full p-4 text-left font-bold text-xs sm:text-sm text-slate-700 flex justify-between items-center hover:bg-slate-50 transition-all">
                        <span>Mengapa SIAKAD dinonaktifkan sementara?</span>
                        <i id="faq-icon-1" class="bx bx-chevron-down text-lg text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div id="faq-body-1" class="hidden p-4 pt-0 text-xs sm:text-sm text-slate-500 leading-relaxed border-t border-slate-50/50 bg-slate-50/30">
                        Kami sedang menyinkronkan data transkrip nilai digital akhir semester, integrasi leger e-Rapor, serta pemeliharaan tabel ujian kelas VI yang baru.
                    </div>
                </div>
                <!-- FAQ 2 -->
                <div class="border border-slate-100 rounded-xl overflow-hidden bg-white/60">
                    <button onclick="toggleFaq(2)" class="w-full p-4 text-left font-bold text-xs sm:text-sm text-slate-700 flex justify-between items-center hover:bg-slate-50 transition-all">
                        <span>Apakah data akademik saya aman selama pemeliharaan?</span>
                        <i id="faq-icon-2" class="bx bx-chevron-down text-lg text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div id="faq-body-2" class="hidden p-4 pt-0 text-xs sm:text-sm text-slate-500 leading-relaxed border-t border-slate-50/50 bg-slate-50/30">
                        Ya, seluruh data nilai, jadwal, dan identitas siswa diproteksi secara aman menggunakan sistem backup database terenkripsi sebelum proses pemeliharaan dimulai.
                    </div>
                </div>
            </div>

            <!-- Support Contact Info -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-400">
                    Mengalami kendala mendesak? Silakan hubungi Unit Pelaksana Teknis Operator di grup komunikasi sekolah.
                </p>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="text-center py-6 text-xs text-slate-400 font-medium">
        &copy; {{ date('Y') }} SD NEGERI PASIRIPIS. Seluruh data akademik tersimpan aman secara digital.
    </footer>

    <!-- Scripts -->
    <script>
        // --- 1. ACCORDION FAQ LOGIC ---
        function toggleFaq(id) {
            const body = document.getElementById(`faq-body-${id}`);
            const icon = document.getElementById(`faq-icon-${id}`);
            if (body.classList.contains('hidden')) {
                body.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                body.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        // --- 2. DYNAMIC PROGRESS BAR ---
        document.addEventListener('DOMContentLoaded', () => {
            const bar = document.getElementById('progress-bar');
            const txt = document.getElementById('progress-text');
            let progress = 0;

            const interval = setInterval(() => {
                // Kenaikan persentase acak agar realistis
                progress += Math.floor(Math.random() * 8) + 3;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                    txt.innerText = "Selesai! Menyegarkan...";
                    setTimeout(() => location.reload(), 1500); // refresh otomatis jika selesai
                } else {
                    txt.innerText = progress + "%";
                }
                bar.style.width = progress + "%";
            }, 2000);
        });
    </script>
</body>
</html>