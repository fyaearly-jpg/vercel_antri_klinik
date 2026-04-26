<?php
include 'koneksi.php';

// Ambil antrean hari ini yang statusnya masih 'menunggu'
// Urutkan berdasarkan waktu (ASC) agar yang daftar duluan muncul duluan
$hari_ini = date('Y-m-d');
$query = mysqli_query($conn, "SELECT nomor_antrian, poli FROM antrian 
                              WHERE DATE(created_at) = '$hari_ini' 
                              AND status = 'menunggu' 
                              ORDER BY created_at ASC LIMIT 1");

$data = mysqli_fetch_assoc($query);

header('Content-Type: application/json');

if ($data) {
    echo json_encode([
        'nomor' => $data['nomor_antrian'],
        'poli' => "Poli " . $data['poli']
    ]);
} else {
    echo json_encode([
        'nomor' => '--',
        'poli' => 'Belum Ada Antrean'
    ]);
}
?>