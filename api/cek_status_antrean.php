<?php
// api/cek_status_pasien.php
header('Content-Type: application/json');
include 'koneksi.php';

// Ambil data dari Cookie
$cookie_raw = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

if (!$cookie_data || $cookie_data['role'] !== 'pasien') {
    echo json_encode(['status' => 'none', 'nomor_antrian' => '-']);
    exit();
}

$id_pasien = $cookie_data['id'];
$hari_ini = date('Y-m-d');

// Perbaiki typo variabel (sebelumnya $query, sekarang didefinisikan dengan benar)
$query_antrian = mysqli_query($koneksi, "SELECT * FROM antrian WHERE id_pasien = '$id_pasien' AND DATE(created_at) = '$hari_ini' ORDER BY id DESC LIMIT 1");
$data = mysqli_fetch_assoc($query_antrian);

if ($data) {
    echo json_encode($data);
} else {
    echo json_encode(['status' => 'none', 'nomor_antrian' => '-']);
}
?>