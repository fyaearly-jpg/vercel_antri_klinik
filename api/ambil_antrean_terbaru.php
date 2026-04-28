<?php
// api/ambil_antrean_terbaru.php
include 'koneksi.php';

// 1. Ambil data dari Cookie agar ID Pasien aman (Solusi Error Line 7)
$cookie_raw = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

if (!$cookie_data) {
    die("Error: Sesi login tidak ditemukan. Silakan login ulang.");
}

$id_pasien = $cookie_data['id']; // Mengambil ID langsung dari cookie session
$poli = mysqli_real_escape_string($koneksi, $_POST['poli'] ?? 'Umum');
$tanggal = date('Y-m-d');

// 2. AUTO-DETECT NAMA KOLOM (Solusi Error Line 15)
// Kita cek apakah kolomnya bernama 'nomor_antrian' atau 'nomor'
$res_check = mysqli_query($koneksi, "SHOW COLUMNS FROM antrian LIKE 'nomor_antrian'");
$col = (mysqli_num_rows($res_check) > 0) ? "nomor_antrian" : "nomor";

// 3. AMBIL NOMOR TERAKHIR
$query_max = "SELECT MAX(CAST(SUBSTRING($col, 3) AS UNSIGNED)) as max_no 
              FROM antrian 
              WHERE poli = '$poli' AND DATE(created_at) = '$tanggal'";

$res_max = mysqli_query($koneksi, $query_max);
$row = mysqli_fetch_assoc($res_max);
$next_no = ($row['max_no'] ?? 0) + 1;

// 4. FORMAT NOMOR (Contoh: U-1, G-5)
$kode_poli = strtoupper(substr($poli, 0, 1));
$nomor_baru = $kode_poli . "-" . $next_no;

// 5. INSERT KE DATABASE
$sql_insert = "INSERT INTO antrian ($col, status, id_pasien, poli, created_at, updated_at) 
               VALUES ('$nomor_baru', 'menunggu', '$id_pasien', '$poli', NOW(), NOW())";

if (mysqli_query($koneksi, $sql_insert)) {
    // Berhasil, balik ke dashboard
    header("Location: /dashboard_pasien?status=sukses");
    exit();
} else {
    die("Gagal menyimpan antrean: " . mysqli_error($koneksi));
}