<?php
header("Content-Type: application/json");
include "koneksi.php";
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Method tidak valid"]);
    exit();
}
 
$nama     = mysqli_real_escape_string($koneksi, $_POST['nama_pasien'] ?? 'Anonim');
$kepuasan = mysqli_real_escape_string($koneksi, $_POST['kepuasan'] ?? '');
$saran    = mysqli_real_escape_string($koneksi, $_POST['saran'] ?? '');
 
if (empty($kepuasan)) {
    echo json_encode(["success" => false, "message" => "Pilih tingkat kepuasan"]);
    exit();
}
 
$q = mysqli_query($koneksi, "INSERT INTO feedback (nama_pasien, kepuasan, saran) VALUES ('$nama','$kepuasan','$saran')");
echo json_encode(["success" => (bool)$q]);
?>