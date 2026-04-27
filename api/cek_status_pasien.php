<?php
// api/cek_status_pasien.php
include 'koneksi.php';

$nomor = mysqli_real_escape_string($koneksi, $_GET['nomor'] ?? '');
$hari_ini = date('Y-m-d');

$query_antrian = mysqli_query($koneksi, "SELECT status, poli FROM antrian WHERE nomor_antrian = '$nomor' AND DATE(created_at) = '$hari_ini' LIMIT 1");
$data = mysqli_fetch_assoc($query_antrian);

header('Content-Type: application/json');
echo json_encode($data ?: ['status' => 'none']);