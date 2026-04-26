<?php
header("Content-Type: application/json");
include "koneksi.php";
 
$q    = mysqli_query($koneksi, "SELECT id, nama_lengkap, email, role, created_at FROM petugas ORDER BY role ASC");
$list = [];
while ($row = mysqli_fetch_assoc($q)) {
    $list[] = $row;
}
echo json_encode($list);
?>