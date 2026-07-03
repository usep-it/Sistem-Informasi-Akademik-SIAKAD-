<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Logout real-time - last_seen_at harus update ke waktu sekarang
     * Bukan ke 6 menit yang lalu
     */
    public function test_logout_updates_last_seen_at_to_current_time()
    {
        // 1. CREATE TEST USER
        $user = User::factory()->create([
            'role' => 'Guru',
            'last_seen_at' => now('Asia/Jakarta')->subHours(2), // Set 2 jam lalu
            'updated_at' => now('Asia/Jakarta')->subHours(2),
        ]);

        // 2. LOGIN SEBAGAI USER
        $this->actingAs($user);

        // 3. CAPTURE WAKTU SEBELUM LOGOUT
        $timeBefore = now('Asia/Jakarta');

        // 4. LOGOUT
        $response = $this->post(route('logout'));

        // 5. CAPTURE WAKTU SETELAH LOGOUT
        $timeAfter = now('Asia/Jakarta');

        // 6. REFRESH USER DARI DATABASE
        $userAfter = User::find($user->id);

        // 7. ASSERTIONS - VALIDASI HASIL
        
        // ✅ Redirect ke /login setelah logout
        $response->assertRedirect('/login');
        
        // ✅ last_seen_at harus dalam rentang timeBefore - timeAfter (bukan 6 menit lalu)
        $lastSeenAt = Carbon::parse($userAfter->last_seen_at);
        $this->assertGreaterThanOrEqual($timeBefore, $lastSeenAt);
        $this->assertLessThanOrEqual($timeAfter, $lastSeenAt);
        
        // ✅ updated_at juga harus update (bukan tetap lama)
        $this->assertGreaterThanOrEqual($timeBefore, Carbon::parse($userAfter->updated_at));
        
        // ✅ last_seen_at === updated_at (keduanya update di waktu sama)
        $this->assertEquals(
            $userAfter->last_seen_at,
            $userAfter->updated_at,
            'last_seen_at dan updated_at harus sama saat logout'
        );

        // ✅ last_seen_at BUKAN 6 menit yang lalu
        $differenceMinutes = now('Asia/Jakarta')->diffInMinutes($lastSeenAt);
        $this->assertLessThan(5, $differenceMinutes, 'last_seen_at seharusnya real-time, bukan 6 menit lalu');
    }

    /**
     * Test: User seharusnya tidak muncul di activeSessions() setelah logout
     */
    public function test_user_disappears_from_active_sessions_after_logout()
    {
        // 1. CREATE TEST USER
        $user = User::factory()->create([
            'role' => 'Guru',
            'last_seen_at' => now('Asia/Jakarta'),
        ]);

        // 2. VERIFY USER MUNCUL DI ACTIVE SESSIONS SEBELUM LOGOUT
        $activeSessionsBefore = User::where('last_seen_at', '>=', now('Asia/Jakarta')->subMinutes(15))
                                     ->pluck('id')
                                     ->toArray();
        $this->assertContains($user->id, $activeSessionsBefore, 'User seharusnya aktif sebelum logout');

        // 3. LOGIN DAN LOGOUT
        $this->actingAs($user);
        $this->post(route('logout'));

        // 4. VERIFY USER TIDAK MUNCUL DI ACTIVE SESSIONS SETELAH LOGOUT
        $activeSessionsAfter = User::where('last_seen_at', '>=', now('Asia/Jakarta')->subMinutes(15))
                                    ->pluck('id')
                                    ->toArray();
        $this->assertNotContains($user->id, $activeSessionsAfter, 'User seharusnya tidak aktif setelah logout');
    }

    /**
     * Test: IP address ter-record saat logout
     */
    public function test_logout_records_ip_address()
    {
        // 1. CREATE & LOGIN USER
        $user = User::factory()->create(['role' => 'Guru']);
        $this->actingAs($user);

        // 2. LOGOUT
        $this->post(route('logout'));

        // 3. CHECK: IP address harus terupdate
        $userAfter = User::find($user->id);
        $this->assertNotNull($userAfter->ip_address, 'IP address harus ter-record saat logout');
        $this->assertEquals('127.0.0.1', $userAfter->ip_address, 'IP address harus 127.0.0.1 (local test)');
    }
}
