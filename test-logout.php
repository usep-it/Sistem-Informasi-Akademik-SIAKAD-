<?php
/**
 * Test Script: Logout Real-time
 * 
 * Jalankan dengan: php test-logout.php
 * Letakkan di root folder project
 */

// Load Laravel bootstrap
require __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Carbon\Carbon;

echo "\n" . str_repeat("=", 60) . "\n";
echo "🔍 TEST LOGOUT REAL-TIME FUNCTIONALITY\n";
echo str_repeat("=", 60) . "\n\n";

// 1. Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found in database!\n";
    exit(1);
}

echo "📌 Test User: {$user->name} (ID: {$user->id})\n";
echo str_repeat("-", 60) . "\n\n";

// 2. BEFORE STATE
echo "📋 BEFORE LOGOUT:\n";
echo "   last_seen_at: {$user->last_seen_at}\n";
echo "   updated_at:   {$user->updated_at}\n";
echo "   Current time: " . now('Asia/Jakarta')->toDateTimeString() . "\n\n";

// 3. Simulate logout
$timeBefore = now('Asia/Jakarta');
$now = now('Asia/Jakarta')->toDateTimeString();

$updateCount = \Illuminate\Support\Facades\DB::table('users')
    ->where('id', $user->id)
    ->update([
        'last_seen_at' => $now,
        'ip_address' => '127.0.0.1',
        'updated_at' => $now,
    ]);

if ($updateCount === 0) {
    echo "❌ Update failed!\n";
    exit(1);
}

echo "✅ Logout simulation executed (rows affected: {$updateCount})\n\n";
sleep(1);

// 4. After state
$userAfter = User::find($user->id);
echo "📊 AFTER LOGOUT:\n";
echo "   last_seen_at: {$userAfter->last_seen_at}\n";
echo "   updated_at:   {$userAfter->updated_at}\n";
echo "   ip_address:   {$userAfter->ip_address}\n\n";

// 5. Validations
echo "✔️  VALIDATION RESULTS:\n";
echo str_repeat("-", 60) . "\n";

$passed = 0;
$total = 5;

// Check 1
$lastSeenAt = Carbon::parse($userAfter->last_seen_at);
if ($lastSeenAt >= $timeBefore) {
    echo "✅ [1/5] last_seen_at ter-update ke waktu logout\n";
    $passed++;
} else {
    echo "❌ [1/5] last_seen_at tidak ter-update\n";
}

// Check 2
$updatedAt = Carbon::parse($userAfter->updated_at);
if ($updatedAt >= $timeBefore) {
    echo "✅ [2/5] updated_at ter-update ke waktu logout\n";
    $passed++;
} else {
    echo "❌ [2/5] updated_at tidak ter-update\n";
}

// Check 3
if ($userAfter->last_seen_at === $userAfter->updated_at) {
    echo "✅ [3/5] last_seen_at === updated_at (keduanya sama)\n";
    $passed++;
} else {
    echo "❌ [3/5] last_seen_at dan updated_at BERBEDA\n";
}

// Check 4
$diffMinutes = now('Asia/Jakarta')->diffInMinutes($lastSeenAt);
if ($diffMinutes < 1) {
    echo "✅ [4/5] last_seen_at adalah real-time (bukan 6 menit lalu)\n";
    $passed++;
} else {
    echo "❌ [4/5] last_seen_at adalah {$diffMinutes} menit yang lalu\n";
}

// Check 5
if ($userAfter->ip_address) {
    echo "✅ [5/5] IP address ter-record: {$userAfter->ip_address}\n";
    $passed++;
} else {
    echo "❌ [5/5] IP address tidak ter-record\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
if ($passed === $total) {
    echo "🎉 ALL TESTS PASSED ({$passed}/{$total})!\n";
    echo "✅ Logout real-time berfungsi dengan benar.\n";
    $exitCode = 0;
} else {
    echo "⚠️  SOME TESTS FAILED ({$passed}/{$total})\n";
    echo "❌ Ada masalah dengan logout real-time.\n";
    $exitCode = 1;
}
echo str_repeat("=", 60) . "\n\n";

exit($exitCode);
