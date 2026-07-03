<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pegawai;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GuruExport;
use App\Exports\SiswaUsersExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon; // Pastikan import ini benar

class UserController extends Controller
{
    // ==================== GURU ====================
    public function guru()
    {
        $aguru = User::where('role', 'Guru')->with('pegawai')->latest()->get();
        $guru = Pegawai::whereDoesntHave('user')
                        ->whereNotNull('email')
                        ->orderBy('nama')
                        ->get();

        return view('user.guru', compact('aguru', 'guru'));
    }

    public function store_guru(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id|unique:users,pegawai_id',
            'password'   => 'nullable|string|min:6', 
        ], [
            'pegawai_id.unique' => 'Pegawai ini sudah memiliki akun.',
            'pegawai_id.required' => 'Anda harus memilih guru.',
        ]);

        $pegawai = Pegawai::findOrFail($request->pegawai_id);
        $plainPassword = $request->password ?? '123456';

        $user = User::create([
            'name'            => $pegawai->nama,
            'username'        => $pegawai->email,
            'email'           => $pegawai->email,
            'password'        => Hash::make($plainPassword),
            'plain_password'  => $plainPassword,
            'role'            => 'Guru',
            'pegawai_id'      => $pegawai->id,
            'uuid'            => (string) Str::uuid(),
            'status'          => 'Aktif'
        ]);

        return redirect()->route('user.guru')
            ->with('notif', '✅ Akun untuk <strong>' . $pegawai->nama . '</strong> berhasil dibuat.<br>'
                . 'Username: <strong>' . $user->username . '</strong><br>'
                . 'Password: <strong>' . $plainPassword . '</strong>');
    }

    // ==================== SISWA ====================
    public function siswa()
    {
        // Ambil akun role siswa, hubungkan ke tabel siswas, lalu urutkan berdasarkan abjad nama
        $asiswa = User::where('users.role', 'Siswa')
                    ->with('siswa.kelas')
                    ->join('siswas', 'users.siswa_id', '=', 'siswas.id')
                    ->select('users.*') 
                    ->orderBy('siswas.nama', 'asc')
                    ->get();

        // Daftar siswa aktif yang belum punya akun untuk pilihan di modal tambah
        $siswa = Siswa::whereDoesntHave('user')
                    ->where('status', 'Aktif')
                    ->orderBy('nama')
                    ->get();

        return view('user.siswa', compact('siswa', 'asiswa'));
    }

    public function store_siswa(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id|unique:users,siswa_id',
            'password' => 'nullable|string|min:6',
        ], [
            'siswa_id.unique' => 'Siswa ini sudah memiliki akun.',
            'siswa_id.required' => 'Anda harus memilih siswa.',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);
        $plainPassword = $request->password ?? '123456';

        User::create([
            'name'            => $siswa->nama,
            'username'        => $siswa->nis,
            'password'        => Hash::make($plainPassword),
            'plain_password'  => $plainPassword,
            'role'            => 'Siswa',
            'siswa_id'        => $siswa->id,
            'uuid'            => (string) Str::uuid(),
            'status'          => 'Aktif'
        ]);

        return redirect()->route('user.siswa')
            ->with('notif', '✅ Akun untuk <strong>' . $siswa->nama . '</strong> berhasil dibuat.<br>'
                . 'Username: <strong>' . $siswa->nis . '</strong><br>'
                . 'Password: <strong>' . $plainPassword . '</strong>');
    }

    // ==================== RESET PASSWORD ====================
    public function reset_password(User $user)
    {
        $defaultPassword = '123456';
        $user->password = Hash::make($defaultPassword);
        $user->plain_password = $defaultPassword;
        $user->save();

        $redirectRoute = $user->role === 'Guru' ? 'user.guru' : 'user.siswa';

        return redirect()->route($redirectRoute)
            ->with('notif', '🔑 Password untuk user ' . $user->username . ' telah direset menjadi: <strong>' . $defaultPassword . '</strong>');
    }

    // ==================== HAPUS USER ====================
    public function destroy(User $user)
    {
        $username = $user->username;
        $role = $user->role;

        if ($user->foto && File::exists(public_path('foto_user/' . $user->foto))) {
            File::delete(public_path('foto_user/' . $user->foto));
        }
        
        $user->delete();

        $redirectRoute = $role === 'Guru' ? 'user.guru' : 'user.siswa';
        return redirect()->route($redirectRoute)
            ->with('notif', '🗑️ Akun <strong>' . $username . '</strong> berhasil dihapus.');
    }

    // ==================== EXPORT ====================
    public function export_guru()
    {
        return Excel::download(new GuruExport, 'akun_guru_' . date('Y-m-d') . '.xlsx');
    }

    public function exportSiswa()
    {
        return Excel::download(new SiswaUsersExport, 'akun_siswa_' . date('Y-m-d') . '.xlsx');
    }

    // ==================== GENERATE AKUN SISWA OTOMATIS ====================
    public function generateAkun()
    {
        $siswaTanpaAkun = Siswa::where('status', 'Aktif')->doesntHave('user')->get();

        if ($siswaTanpaAkun->isEmpty()) {
            return redirect()->back()->with('info', '✅ Semua siswa aktif sudah memiliki akun.');
        }

        $createdAccounts = [];

        foreach ($siswaTanpaAkun as $siswa) {
            $username = $siswa->nis ?? strtolower(str_replace(' ', '', $siswa->nama));
            $passwordPlain = $siswa->ttl ? date('dmY', strtotime($siswa->ttl)) : '123456';
            $passwordHash = Hash::make($passwordPlain);

            User::create([
                'name'            => $siswa->nama,
                'username'        => $username,
                'password'        => $passwordHash,
                'plain_password'  => $passwordPlain,
                'role'            => 'Siswa',
                'siswa_id'        => $siswa->id,
                'uuid'            => (string) Str::uuid(),
                'status'          => 'Aktif'
            ]);

            $createdAccounts[] = [
                'nama' => $siswa->nama,
                'username' => $username,
                'password' => $passwordPlain,
            ];
        }

        return redirect()->back()->with('notif_accounts', $createdAccounts);
    }

    // ==================== PROFIL USER ====================
    public function editProfil()
    {
        $user = Auth::user();
        return view('profil.edit', compact('user'));
    }

    public function updateProfil(Request $request)
    {
        $user = auth()->user(); 

        $rules = [
            'name' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'min:6|confirmed';
        }

        $request->validate($rules);

        $user->name = $request->name;

        if ($request->hasFile('foto')) {
            if ($user->foto && File::exists(public_path('foto_user/' . $user->foto))) {
                File::delete(public_path('foto_user/' . $user->foto));
            }

            $filename = time() . '_' . Str::random(6) . '.' . $request->foto->getClientOriginalExtension();
            $request->foto->move(public_path('foto_user'), $filename);
            $user->foto = $filename;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->plain_password = $request->password;
        }

        $user->save();

        return redirect()->route('profil.edit')->with('notif', 'Profil berhasil diperbarui!');
    }

    // ==================== LOGOUT ====================
    public function logout(Request $request)
    {
        $user = Auth::user();
        $userId = $user?->id;

        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($userId) {
                User::where('id', $userId)->update(['last_seen_at' => null]);
            }

            return redirect('/');
        } catch (\Exception $e) {
            \Log::error('Logout Error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Error saat logout');
        }
    }

    /**
     * MONITORING SESI & GRAFIK (Fix: Zero-Fill Logic & Server Compatibility)
     */
    public function activeSessions(Request $request)
    {
        // 1. Ambil SEMUA user yang pernah login untuk daftar tabel
        $users = User::whereNotNull('last_seen_at')
                    ->with(['pegawai', 'siswa'])
                    ->orderBy('last_seen_at', 'desc')
                    ->get();

        // 2. Hitung statistik dasar (Query kompatibel untuk shared hosting/server Linux)
        $stats = [
            'total_akses' => User::whereNotNull('last_seen_at')->count(),
            'siswa_login' => User::where('role', 'Siswa')->whereNotNull('last_seen_at')->count(),
            'guru_login'  => User::where('role', 'Guru')->whereNotNull('last_seen_at')->count(),
            // Distinct count yang lebih stabil di berbagai engine SQL
            'ip_unik'     => DB::table('users')->whereNotNull('ip_address')->distinct()->count('ip_address')
        ];

        // 3. LOGIKA GRAFIK 7 HARI TERAKHIR (Sinkronisasi Waktu Server Jakarta)
        $chartLabels = [];
        $chartCounts = [];

        Carbon::setLocale('id');

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now('Asia/Jakarta')->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M'); 
            
            // Menggunakan toDateString() untuk memastikan format pencarian di server Linux tepat YYYY-MM-DD
            $count = User::whereNotNull('last_seen_at')
                         ->whereDate('last_seen_at', $date->toDateString())
                         ->count();
            $chartCounts[] = $count;
        }

        return view('user.online', compact('users', 'stats', 'chartLabels', 'chartCounts'));
    }
}