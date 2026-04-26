<?php
header("Content-Type: application/json");
include "koneksi.php";
 
$nomor    = mysqli_real_escape_string($koneksi, $_GET['nomor'] ?? '');
$hari_ini = date('Y-m-d');
 
if (empty($nomor)) {
    echo json_encode(["status" => "none"]);
    exit();
}
 
$q    = mysqli_query($koneksi, "SELECT status, nomor_antrian, poli FROM antrian WHERE nomor_antrian='$nomor' AND DATE(created_at)='$hari_ini' LIMIT 1");
$data = mysqli_fetch_assoc($q);
 
echo json_encode($data ?: ["status" => "none", "nomor_antrian" => $nomor]);
?>