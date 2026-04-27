<?php
session_start();
include 'koneksi.php';

// Pastikan session id_pasien ada
if (!isset($_SESSION['id_pasien'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session habis']);
    exit();
}

$id_pasien = $_SESSION['id_pasien'];

// Perbaikan 1: Gunakan ORDER BY id jika created_at belum ada di DB
$sql = "SELECT * FROM antrian WHERE id_pasien = '$id_pasien' ORDER BY id DESC LIMIT 1";
$query_antrian = mysqli_query($koneksi, $sql);

// Perbaikan 2: Nama variabel harus sama ($query_antrian)
$data = mysqli_fetch_assoc($query_antrian);

header('Content-Type: application/json');
if ($data) {
    echo json_encode($data);
} else {
    echo json_encode(['status' => 'none', 'nomor_antrian' => '-']);
}