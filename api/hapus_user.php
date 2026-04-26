<?php
header("Content-Type: application/json");
include "koneksi.php";
 
$id    = mysqli_real_escape_string($koneksi, $_GET['id'] ?? '');
$tabel = $_GET['tabel'] ?? '';
 
if (empty($id)) {
    echo json_encode(["success" => false, "message" => "ID tidak valid"]);
    exit();
}
 
if ($tabel === 'petugas') {
    $ok = mysqli_query($koneksi, "DELETE FROM petugas WHERE id='$id'");
} elseif ($tabel === 'pasien') {
    $ok = mysqli_query($koneksi, "DELETE FROM pasien WHERE id='$id'");
} else {
    echo json_encode(["success" => false, "message" => "Tabel tidak valid"]);
    exit();
}
 
echo json_encode(["success" => (bool)$ok]);
?>