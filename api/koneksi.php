<?php
// api/koneksi.php

// 1. Set Zona Waktu PHP ke Waktu Indonesia Barat (WIB)
date_default_timezone_set('Asia/Jakarta');

$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = 'wG4jTPVkrLpurY1.root';
$pass = 'lb5JGw7uy0uZJ9yf';
$db   = 'klinik_db';

$koneksi = mysqli_init();
mysqli_ssl_set($koneksi, NULL, NULL, NULL, NULL, NULL);

$real_koneksi = mysqli_real_connect(
    $koneksi, 
    $host, 
    $user, 
    $pass, 
    $db, 
    $port, 
    NULL, 
    MYSQLI_CLIENT_SSL
);

if (!$real_koneksi) {
    die("Koneksi ke TiDB Cloud gagal: " . mysqli_connect_error());
}

// 2. FIX UTAMA: Paksa TiDB Cloud menggunakan zona waktu Indonesia (+07:00)
mysqli_query($koneksi, "SET time_zone = '+07:00'");
?>