<?php
header("Content-Type: application/json");
include "koneksi.php";
 
$hari_ini = date('Y-m-d');
 
$q_total   = mysqli_query($koneksi, "SELECT COUNT(*) as t FROM antrian WHERE DATE(created_at)='$hari_ini'");
$q_selesai = mysqli_query($koneksi, "SELECT COUNT(*) as t FROM antrian WHERE DATE(created_at)='$hari_ini' AND status='selesai'");
$q_tunggu  = mysqli_query($koneksi, "SELECT COUNT(*) as t FROM antrian WHERE DATE(created_at)='$hari_ini' AND status='menunggu'");
 
$total   = mysqli_fetch_assoc($q_total)['t'];
$selesai = mysqli_fetch_assoc($q_selesai)['t'];
$tunggu  = mysqli_fetch_assoc($q_tunggu)['t'];
 
$q_list = mysqli_query($koneksi,
    "SELECT a.id, a.nomor_antrian, a.poli, a.status, a.created_at, p.nama_pasien
     FROM antrian a
     LEFT JOIN pasien p ON a.id_pasien = p.id
     WHERE DATE(a.created_at)='$hari_ini'
     ORDER BY a.created_at ASC"
);
 
$list = [];
while ($row = mysqli_fetch_assoc($q_list)) {
    $list[] = $row;
}
 
echo json_encode(["total" => $total, "selesai" => $selesai, "tunggu" => $tunggu, "list" => $list]);
?>