<?php
header("Content-Type: application/json");
include "koneksi.php";

// Gunakan zona waktu Indonesia
date_default_timezone_set('Asia/Jakarta');

// Ambil ID dari cookie yang sedang login
$cookie_raw = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

if (!$cookie_data || $cookie_data['role'] !== 'pasien') {
    echo json_encode(["success" => false, "message" => "Sesi habis"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pasien = (int)$cookie_data['id']; // Ambil ID Pasien dari Sesi
    $kepuasan  = mysqli_real_escape_string($koneksi, $_POST['kepuasan'] ?? '');
    $saran     = mysqli_real_escape_string($koneksi, $_POST['saran'] ?? '');
    $waktu     = date('Y-m-d H:i:s');
    
    if (!empty($kepuasan)) {
        // SESUAIKAN DENGAN NAMA KOLOM DI DATABASE: id_pasien, kepuasan, saran, tgl_kirim
        $sql = "INSERT INTO feedback (id_pasien, kepuasan, saran, tgl_kirim) 
                VALUES ('$id_pasien', '$kepuasan', '$saran', '$waktu')";
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