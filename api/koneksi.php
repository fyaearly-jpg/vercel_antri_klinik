<?php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$port = 4000;
$user = 'wG4jTPVkrLpurY1.root';
$pass = 'lb5JGw7uy0uZJ9yf';
$db   = 'klinik_db';
 
$koneksi = mysqli_init();
mysqli_ssl_set($koneksi, NULL, NULL, NULL, NULL, NULL);
$ok = mysqli_real_connect($koneksi, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);
 
if (!$ok) {
    http_response_code(500);
    die(json_encode(["success" => false, "message" => "Koneksi DB gagal: " . mysqli_connect_error()]));
}
mysqli_set_charset($koneksi, 'utf8mb4');
?>