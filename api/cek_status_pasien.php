<?php
session_start();
include 'koneksi.php';

$id_pasien = $_SESSION['id_pasien'];
$hari_ini = date('Y-m-d');

// Ganti baris 13 menjadi:
$query_antrian = mysqli_query($koneksi, "SELECT * FROM antrian WHERE id_pasien = '$id_pasien' ORDER BY id DESC LIMIT 1");

$data = mysqli_fetch_assoc($query);

header('Content-Type: application/json');
if ($data) {
    echo json_encode($data);
} else {
    echo json_encode(['status' => 'none', 'nomor_antrian' => '-']);
}