<?php
// Data dari TiDB Cloud
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = 'wG4jTPVkrLpurY1.root';
$pass = 'lb5JGw7uy0uZJ9yf';
$db   = 'klinik_db';

// Inisialisasi mysqli
$koneksi = mysqli_init();

// Menambahkan pengaturan SSL (Wajib untuk TiDB Serverless)
mysqli_ssl_set($koneksi, NULL, NULL, NULL, NULL, NULL);

// Melakukan koneksi
$real_koenksi = mysqli_real_koneksi(
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
    die("Koneksi ke TiDB Cloud gagal: " . mysqli_koneksi_error());
}
?>