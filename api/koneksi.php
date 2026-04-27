<?php
// api/koneksi.php

// Data dari TiDB Cloud
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = 'wG4jTPVkrLpurY1.root';
$pass = 'lb5JGw7uy0uZJ9yf';
$db   = 'klinik_db';

// 1. Inisialisasi mysqli
$koneksi = mysqli_init();

// 2. Menambahkan pengaturan SSL (Wajib untuk TiDB Serverless)
mysqli_ssl_set($koneksi, NULL, NULL, NULL, NULL, NULL);

// 3. Melakukan koneksi - PERBAIKAN: Gunakan mysqli_real_connect
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

// 4. Cek Koneksi - PERBAIKAN: Gunakan mysqli_connect_error
if (!$real_koneksi) {
    die("Koneksi ke TiDB Cloud gagal: " . mysqli_connect_error());
}
?>