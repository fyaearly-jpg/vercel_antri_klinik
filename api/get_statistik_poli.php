<?php
header("Content-Type: application/json");
include "koneksi.php";
 
$hari_ini = date('Y-m-d');
$q        = mysqli_query($koneksi, "SELECT poli, COUNT(*) as jumlah FROM antrian WHERE DATE(created_at)='$hari_ini' GROUP BY poli");
 
$labels = [];
$values = [];
while ($row = mysqli_fetch_assoc($q)) {
    $labels[] = "Poli " . $row['poli'];
    $values[] = (int)$row['jumlah'];
}
 
echo json_encode(["labels" => $labels, "data" => $values]);
?>