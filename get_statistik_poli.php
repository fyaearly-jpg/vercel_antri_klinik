<?php
include 'koneksi.php';
$hari_ini = date('Y-m-d');

// Menghitung jumlah pasien per poli hari ini
$query = mysqli_query($conn, "SELECT poli, COUNT(*) as jumlah 
                              FROM antrian 
                              WHERE DATE(created_at) = '$hari_ini' 
                              GROUP BY poli");

$labels = [];
$values = [];

while ($row = mysqli_fetch_assoc($query)) {
    $labels[] = "Poli " . $row['poli'];
    $values[] = (int)$row['jumlah'];
}

header('Content-Type: application/json');
echo json_encode([
    'labels' => $labels,
    'data' => $values
]);