<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $username;
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();
    }

    public function findUsername()
    {
        $login = request()->input('login');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    /**
     * Override fungsi login agar cek role sesuai pilihan user
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
            'role' => 'required',
        ]);

        $loginType = $this->findUsername();

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // 1. CEK STATUS AKUN DI TABEL USERS (TOLAK JIKA TIDAK AKTIF)
            if ($user->status === 'Tidak Aktif') {
                Auth::logout();
                return back()->with('error', 'Login Ditolak: Akun Anda sudah dinonaktifkan.')
                             ->withInput($request->except('password'));
            }

            // 2. PASTIKAN ROLE COCOK DENGAN PILIHAN DI FORM (CEK LEBIH DULU)
            if ($user->role !== $request->role) {
                Auth::logout();
                return back()->with('error', 'Anda tidak memiliki akses sebagai ' . ucfirst($request->role))
                            ->withInput($request->except('password'));
            }

            // 3. CEK STATUS SISWA DI TABEL SISWAS (TOLAK HANYA JIKA AKUN NON-AKTIF)
            if ($user->role === 'Siswa' && $user->siswa) {
                if ($user->siswa->status === 'Tidak Aktif') {
                    Auth::logout();
                    return back()->with('error', 'Login Ditolak: Akun Anda sudah dinonaktifkan.')
                                 ->withInput($request->except('password'));
                }
            }
            // Arahkan sesuai role
            switch ($user->role) {
                case 'Dev':
                    return redirect('/home')->with('success', 'Login berhasil. Selamat datang!');
                
                // --- LOGIKA BARU UNTUK GURU, KEPALA SEKOLAH, & TENAGA ADMIN ---
                case 'Guru':
                    // Ambil jabatan dari relasi pegawai
                    // Pastikan model User Anda memiliki relasi ->pegawai()
                    $jabatan = $user->pegawai?->jabatan ?? null;

                    if ($jabatan == 'Kepala Sekolah') {
                        return redirect('/home')->with('success', 'Login berhasil. Selamat datang!');
                    } 
                    
                    if ($jabatan == 'Tenaga Administrasi') {
                        return redirect('/home')->with('success', 'Login berhasil. Selamat datang!');
                    }

                    // Jika bukan keduanya (Guru biasa atau $jabatan = null)
                    return redirect('/home')->with('success', 'Login berhasil. Selamat datang!');
                // --- AKHIR LOGIKA BARU ---

                case 'Siswa':
                    if ($user->siswa && $user->siswa->status !== 'Aktif') {
                        return redirect()->route('alumni.dashboard')->with('success', 'Login berhasil. Selamat datang di portal alumni!');
                    }
                    return redirect('/home')->with('success', 'Login berhasil. Selamat datang!');
                
                default:
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Role tidak dikenali.');
            }
        }

        return back()->with('error', 'Username atau password salah.')
                        ->withInput();
    }

    /**
     * Logout kustom dengan notifikasi
     */
    public function logout(Request $request)
{
    // 1. Ambil data user yang sedang login
    $user = Auth::user();
    $userId = $user?->id;
    $userIp = $request->ip();

    try {
        // 2. Proses logout standar Laravel DULU (Priority)
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 3. Update database SETELAH session invalid - set last_seen_at ke waktu SEKARANG (real-time logout)
        if ($userId) {
            $now = now('Asia/Jakarta')->toDateTimeString();
            
            try {
                // Attempt update dengan ip_address (jika column sudah ada)
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $userId)
                    ->update([
                        'last_seen_at' => $now,
                        'ip_address' => $userIp,
                        'updated_at' => $now,
                    ]);
            } catch (\Exception $dbError) {
                // Jika ip_address column belum ada (migration belum jalan)
                // Update tanpa ip_address dulu
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $userId)
                    ->update([
                        'last_seen_at' => $now,
                        'updated_at' => $now,
                    ]);
                
                \Log::warning('Migration "add_ip_address_to_users_table" belum jalan. Update tanpa ip_address. Error: ' . $dbError->getMessage());
            }
        }

        return redirect('/login')->with('success', 'Anda telah berhasil logout.');
    } catch (\Exception $e) {
        \Log::error('Logout Error: ' . $e->getMessage());
        return redirect('/login')->with('error', 'Error saat logout');
    }
}
}