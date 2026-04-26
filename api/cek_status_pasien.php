<?php
session_start();
include 'koneksi.php';

$id_pasien = $_SESSION['id_pasien'];
$hari_ini = date('Y-m-d');

// Ambil antrean terakhir milik pasien login hari ini
$query = mysqli_query($conn, "SELECT nomor_antrian, status FROM antrian 
                              WHERE id_pasien = '$id_pasien' 
                              AND DATE(created_at) = '$hari_ini' 
                              ORDER BY id DESC LIMIT 1");

$data = mysqli_fetch_assoc($query);

header('Content-Type: application/json');
if ($data) {
    echo json_encode($data);
} else {
    echo json_encode(['status' => 'none', 'nomor_antrian' => '-']);
}