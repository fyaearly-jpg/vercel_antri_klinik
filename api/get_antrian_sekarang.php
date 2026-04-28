<?php
// api/get_antrian_sekarang.php
header('Content-Type: application/json');
include 'koneksi.php';

$hari_ini = date('Y-m-d');

// Ambil 1 antrean terbaru yang statusnya sedang dipanggil hari ini
$query = mysqli_query($koneksi, "SELECT nomor_antrean, poli FROM antrian 
                                 WHERE status = 'dipanggil' 
                                 AND DATE(created_at) = '$hari_ini' 
                                 ORDER BY id DESC LIMIT 1");

$data = mysqli_fetch_assoc($query);

if ($data) {
    echo json_encode($data);
} else {
    // Jika belum ada yang dipanggil, kasih balasan kosong
    echo json_encode([
        'nomor_antrean' => '--',
        'poli' => 'Belum ada panggilan'
    ]);
}
?>