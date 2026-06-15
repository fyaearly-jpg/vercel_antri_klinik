<?php
header("Content-Type: application/json");
include 'koneksi.php';

// Paksa PHP menggunakan waktu Jakarta
date_default_timezone_set('Asia/Jakarta');
$hari_ini = date('Y-m-d');
$waktu_sekarang = date('Y-m-d H:i:s'); 

$cookie_raw = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

// Cek sesi
if (!$cookie_data || $cookie_data['role'] !== 'pasien') {
    echo json_encode(["success" => false, "message" => "Sesi habis, silakan login ulang."]);
    exit();
}

// Ambil data
$id_pasien = (int)$cookie_data['id'];
$poli = trim($_POST['poli'] ?? '');

if (empty($poli)) {
    echo json_encode(["success" => false, "message" => "Poli belum dipilih!"]);
    exit();
}

$poli_escaped = mysqli_real_escape_string($koneksi, $poli);

// 1. CEK: Apakah pasien masih punya tiket yang belum 'selesai'?
$cek = mysqli_query($koneksi, "SELECT nomor_antrean, poli FROM antrian WHERE id_pasien='$id_pasien' AND status != 'selesai' LIMIT 1");
if ($cek && mysqli_num_rows($cek) > 0) {
    echo json_encode(["success" => true]); // Punya tiket, suruh dashboard refresh
    exit();
}

// 2. HITUNG NOMOR BARU
$kode_poli = strtoupper(substr($poli, 0, 1));
// Cari nomor terakhir berdasarkan poli di hari ini (pakai LIKE agar bebas bug timezone)
$q_max = mysqli_query($koneksi, "SELECT nomor_antrean FROM antrian WHERE poli='$poli_escaped' AND created_at LIKE '$hari_ini%' ORDER BY id DESC LIMIT 1");

$next_no = 1;
if ($q_max && mysqli_num_rows($q_max) > 0) {
    $row_max = mysqli_fetch_assoc($q_max);
    $parts = explode('-', $row_max['nomor_antrean']);
    $next_no = (int)end($parts) + 1;
}
$nomor_baru = $kode_poli . "-" . $next_no;

// 3. SIMPAN KE DATABASE (Gunakan waktu dari PHP langsung)
$sql_insert = "INSERT INTO antrian (id_pasien, nomor_antrean, poli, status, waktu_daftar, created_at, updated_at) 
               VALUES ('$id_pasien', '$nomor_baru', '$poli_escaped', 'menunggu', '$waktu_sekarang', '$waktu_sekarang', '$waktu_sekarang')";
               
if (mysqli_query($koneksi, $sql_insert)) {
    echo json_encode(["success" => true, "nomor" => $nomor_baru, "poli" => $poli]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan ke database."]);
}
?>