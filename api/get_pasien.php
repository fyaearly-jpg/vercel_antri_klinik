<?php
header("Content-Type: application/json");
include "koneksi.php";
 
$q    = mysqli_query($koneksi, "SELECT id, nama_pasien, email, created_at FROM pasien ORDER BY nama_pasien ASC");
$list = [];
while ($row = mysqli_fetch_assoc($q)) {
    $list[] = $row;
}
echo json_encode($list);
?>