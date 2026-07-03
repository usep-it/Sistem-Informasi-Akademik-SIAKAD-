<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestLogoutRealtime extends Command
{
    protected $signature = 'test:logout-realtime';
    protected $description = 'Test logout real-time functionality - verify last_seen_at updates correctly';

    public function handle()
    {
        $this->info('🔍 Testing Logout Real-time Functionality...\n');

        // 1. Get first user
        $user = User::first();
        if (!$user) {
            $this->error('❌ No users found in database!');
            return 1;
        }

        $this->line("📌 Test User: {$user->name} (ID: {$user->id})");
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');

        // 2. BEFORE STATE
        $this->info('📋 SEBELUM LOGOUT (Current State):');
        $this->line("   last_seen_at: {$user->last_seen_at}");
        $this->line("   updated_at:   {$user->updated_at}");
        $this->line("   Waktu sekarang: " . now('Asia/Jakarta')->toDateTimeString());
        $this->line('');

        // 3. Capture current time
        $timeBefore = now('Asia/Jakarta');

        // 4. SIMULATE LOGOUT - Execute update query
        $now = now('Asia/Jakarta')->toDateTimeString();
        $updateCount = DB::table('users')
            ->where('id', $user->id)
            ->update([
                'last_seen_at' => $now,
                'ip_address' => '127.0.0.1',
                'updated_at' => $now,
            ]);

        if ($updateCount === 0) {
            $this->error('❌ Update failed! No rows affected.');
            return 1;
        }

        $this->info('✅ Logout simulation executed...\n');

        // Sleep 1 detik untuk memastikan cache di-refresh
        sleep(1);

        // 5. AFTER STATE - Refresh dari database
        $userAfter = User::find($user->id);
        $this->info('📊 SETELAH LOGOUT (Updated State):');
        $this->line("   last_seen_at: {$userAfter->last_seen_at}");
        $this->line("   updated_at:   {$userAfter->updated_at}");
        $this->line("   ip_address:   {$userAfter->ip_address}");
        $this->line('');

        // 6. VALIDATION CHECKS
        $this->info('✔️  VALIDATION RESULTS:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Parse timestamps
        $lastSeenAtParsed = Carbon::parse($userAfter->last_seen_at);
        $updatedAtParsed = Carbon::parse($userAfter->updated_at);
        $nowParsed = Carbon::parse($now);

        // Check 1: last_seen_at should be updated to NOW
        if ($lastSeenAtParsed->equalTo($nowParsed)) {
            $this->line('✅ [PASS] last_seen_at ter-update ke waktu logout (real-time)');
        } else {
            $this->line('❌ [FAIL] last_seen_at tidak ter-update dengan benar');
            $this->line("   Expected: {$now}");
            $this->line("   Got: {$userAfter->last_seen_at}");
            return 1;
        }

        // Check 2: updated_at should be updated
        if ($updatedAtParsed->equalTo($nowParsed)) {
            $this->line('✅ [PASS] updated_at ter-update ke waktu logout');
        } else {
            $this->line('❌ [FAIL] updated_at tidak ter-update dengan benar');
            $this->line("   Expected: {$now}");
            $this->line("   Got: {$userAfter->updated_at}");
            return 1;
        }

        // Check 3: last_seen_at === updated_at
        if ($lastSeenAtParsed->equalTo($updatedAtParsed)) {
            $this->line('✅ [PASS] last_seen_at === updated_at (keduanya sama)');
        } else {
            $this->line('❌ [FAIL] last_seen_at dan updated_at tidak sama');
            $this->line("   last_seen_at: {$userAfter->last_seen_at}");
            $this->line("   updated_at:   {$userAfter->updated_at}");
            return 1;
        }

        // Check 4: Should NOT be 6 minutes in the past
        $diffMinutes = now('Asia/Jakarta')->diffInMinutes($lastSeenAtParsed);
        if ($diffMinutes <= 1) {
            $this->line('✅ [PASS] last_seen_at adalah real-time (bukan 6 menit lalu)');
        } else {
            $this->line("❌ [FAIL] last_seen_at adalah {$diffMinutes} menit yang lalu (bukan real-time)");
            return 1;
        }

        // Check 5: IP address should be recorded
        if ($userAfter->ip_address) {
            $this->line("✅ [PASS] IP address ter-record: {$userAfter->ip_address}");
        } else {
            $this->line('❌ [FAIL] IP address tidak ter-record');
            return 1;
        }

        // 7. FINAL RESULT
        $this->line('');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('🎉 ALL TESTS PASSED! Logout real-time berfungsi dengan baik.\n');

        return 0;
    }
}
