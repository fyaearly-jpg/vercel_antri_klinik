<?php
header("Content-Type: application/json");
include "koneksi.php";
 
$q    = mysqli_query($koneksi, "SELECT * FROM feedback ORDER BY created_at DESC");
$list = [];
while ($row = mysqli_fetch_assoc($q)) {
    $list[] = $row;
}
echo json_encode($list);
?>