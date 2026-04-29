<?php
// api/simpan_feedback.php
header("Content-Type: application/json");
include "koneksi.php";

// Set waktu Indonesia
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_pasien'] ?? 'Anonim');
    $kepuasan = mysqli_real_escape_string($koneksi, $_POST['kepuasan'] ?? '');
    $saran    = mysqli_real_escape_string($koneksi, $_POST['saran'] ?? '');
    $waktu    = date('Y-m-d H:i:s');
    
    // Simpan ke database jika tingkat kepuasan diisi
    if (!empty($kepuasan)) {
        $query = mysqli_query($koneksi, "INSERT INTO feedback (nama_pasien, kepuasan, saran, created_at) VALUES ('$nama', '$kepuasan', '$saran', '$waktu')");
        
        if ($query) {
            echo json_encode(["success" => true, "message" => "Feedback tersimpan"]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal simpan ke DB"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    }
    exit();
}
?>