<?php
include 'koneksi.php';
$hari_ini = date('Y-m-d');

// Ambil antrean yang statusnya 'dipanggil' paling baru
$query = mysqli_query($conn, "SELECT nomor_antrian, poli FROM antrian 
                              WHERE status = 'dipanggil' 
                              AND DATE(created_at) = '$hari_ini' 
                              ORDER BY updated_at DESC LIMIT 1");

$data = mysqli_fetch_assoc($query);

header('Content-Type: application/json');
if ($data) {
    echo json_encode($data);
} else {
    echo json_encode(['nomor_antrian' => '--', 'poli' => 'Menunggu Panggilan']);
}