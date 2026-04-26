<?php
header("Content-Type: application/json");
include "koneksi.php";
 
// Aksi via GET (panggil, selesai, reset)
$aksi = $_GET['aksi'] ?? '';
$id   = mysqli_real_escape_string($koneksi, $_GET['id'] ?? '');
$hari_ini = date('Y-m-d');
 
if ($aksi === 'panggil' && $id) {
    $ok = mysqli_query($koneksi, "UPDATE antrian SET status='dipanggil', updated_at=NOW() WHERE id='$id'");
    echo json_encode(["success" => (bool)$ok]);
    exit();
}
 
if ($aksi === 'selesai' && $id) {
    $ok = mysqli_query($koneksi, "UPDATE antrian SET status='selesai', updated_at=NOW() WHERE id='$id'");
    echo json_encode(["success" => (bool)$ok]);
    exit();
}
 
if ($aksi === 'reset') {
    $ok = mysqli_query($koneksi, "DELETE FROM antrian WHERE DATE(created_at)='$hari_ini'");
    echo json_encode(["success" => (bool)$ok]);
    exit();
}
 
echo json_encode(["success" => false, "message" => "Aksi tidak dikenali"]);
?>