<?php
header("Content-Type: application/json");
include "koneksi.php";
 
$hari_ini = date('Y-m-d');
$q        = mysqli_query($koneksi,
    "SELECT nomor_antrian, poli FROM antrian
     WHERE status='dipanggil' AND DATE(created_at)='$hari_ini'
     ORDER BY updated_at DESC LIMIT 1"
);
$data = mysqli_fetch_assoc($q);
 
echo json_encode($data ?: ["nomor_antrian" => "--", "poli" => "Menunggu Panggilan"]);
?>