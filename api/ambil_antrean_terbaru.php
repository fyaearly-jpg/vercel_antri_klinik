<?php
header("Content-Type: application/json");
include "koneksi.php";
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Method tidak valid"]);
    exit();
}
 
$id_pasien = mysqli_real_escape_string($koneksi, $_POST['id_pasien'] ?? '');
$poli      = mysqli_real_escape_string($koneksi, $_POST['poli'] ?? '');
$hari_ini  = date('Y-m-d');
 
if (empty($id_pasien) || empty($poli)) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    exit();
}
 
// Cek sudah punya antrean hari ini
$cek = mysqli_query($koneksi, "SELECT * FROM antrian WHERE id_pasien='$id_pasien' AND DATE(created_at)='$hari_ini' LIMIT 1");
if (mysqli_num_rows($cek) > 0) {
    $ex = mysqli_fetch_assoc($cek);
    echo json_encode(["success" => true, "nomor" => $ex['nomor_antrian'], "poli" => $ex['poli'], "existed" => true]);
    exit();
}
 
// Buat nomor baru
$q_count    = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM antrian WHERE DATE(created_at)='$hari_ini'");
$d_count    = mysqli_fetch_assoc($q_count);
$nomor_urut = $d_count['total'] + 1;
$prefix     = strtoupper(substr($poli, 0, 1));
$nomor_fix  = $prefix . "-" . $nomor_urut;
 
$ins = mysqli_query($koneksi,
    "INSERT INTO antrian (nomor_antrian, status, id_pasien, poli, created_at)
     VALUES ('$nomor_fix','menunggu','$id_pasien','$poli',NOW())"
);
 
if ($ins) {
    echo json_encode(["success" => true, "nomor" => $nomor_fix, "poli" => $poli, "existed" => false]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal membuat antrean: " . mysqli_error($koneksi)]);
}
?>