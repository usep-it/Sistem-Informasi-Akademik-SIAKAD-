<?php
// up.php - Membuka kembali situs secara paksa dari luar Laravel
$downFile = __DIR__ . '/../storage/framework/down';

if (file_exists($downFile)) {
    unlink($downFile);
    echo "<h1 style='color: green; font-family: sans-serif;'>✅ SIAKAD SDN Pasiripis Berhasil Online Kembali!</h1>";
} else {
    echo "<h1 style='color: blue; font-family: sans-serif;'>ℹ️ Situs memang sudah online (tidak sedang maintenance).</h1>";
}