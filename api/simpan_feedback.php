<?php
// api/simpan_feedback.php
header("Content-Type: application/json");
include "koneksi.php";

// Gunakan zona waktu Indonesia
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_pasien'] ?? 'Anonim');
    $kepuasan = mysqli_real_escape_string($koneksi, $_POST['kepuasan'] ?? '');
    $saran    = mysqli_real_escape_string($koneksi, $_POST['saran'] ?? '');
    $waktu    = date('Y-m-d H:i:s');
    
    if (!empty($kepuasan)) {
        $sql = "INSERT INTO feedback (nama_pasien, kepuasan, saran, created_at) 
                VALUES ('$nama', '$kepuasan', '$saran', '$waktu')";
        $query = mysqli_query($koneksi, $sql);
        
        if ($query) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => mysqli_error($koneksi)]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Tingkat kepuasan kosong"]);
    }
    exit();
}
?>