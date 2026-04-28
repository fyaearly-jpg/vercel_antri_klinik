<?php
header("Content-Type: application/json");
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Method tidak valid"]);
    exit();
}

// Ambil dan bersihkan input
$poli      = trim($_POST['poli'] ?? '');
$id_pasien = trim($_POST['id_pasien'] ?? '');
$tanggal   = date('Y-m-d');

// Validasi data kosong
if (empty($poli) || empty($id_pasien)) {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak lengkap",
        "debug"   => ["poli" => $poli, "id_pasien" => $id_pasien]
    ]);
    exit();
}

// Escape string untuk query manual
$poli_escaped      = mysqli_real_escape_string($koneksi, $poli);
$id_pasien_escaped = mysqli_real_escape_string($koneksi, $id_pasien);

// 1. Cek apakah user sudah punya antrean hari ini
$cek = mysqli_query($koneksi,
    "SELECT nomor_antrean, poli, status FROM antrian 
     WHERE id_pasien='$id_pasien_escaped' AND DATE(created_at)='$tanggal' LIMIT 1"
);

if ($cek && mysqli_num_rows($cek) > 0) {
    $ex = mysqli_fetch_assoc($cek);
    echo json_encode([
        "success" => true,
        "nomor"   => $ex['nomor_antrean'],
        "poli"    => $ex['poli'],
        "existed" => true,
        "message" => "Anda sudah punya antrean hari ini"
    ]);
    exit();
}

// 2. Hitung nomor urut berikutnya (Gunakan MAX agar lebih akurat)
$kode_poli = strtoupper(substr($poli, 0, 1)); // Ambil inisial poli (contoh: U, G, K)
$q_max = mysqli_query($koneksi, "SELECT nomor_antrean FROM antrian WHERE poli='$poli_escaped' AND DATE(created_at)='$tanggal' ORDER BY id DESC LIMIT 1");
$row_max = mysqli_fetch_assoc($q_max);

if ($row_max) {
    // Ambil angka setelah tanda "-" (contoh U-5 -> ambil 5)
    $last_no = explode('-', $row_max['nomor_antrean']);
    $next_no = (int)end($last_no) + 1;
} else {
    $next_no = 1;
}
$nomor_baru = $kode_poli . "-" . $next_no;

// 3. Simpan ke database menggunakan PREPARED STATEMENT (Mengatasi Error Incorrect Value)
$stmt = $koneksi->prepare("INSERT INTO antrian (id_pasien, nomor_antrean, poli, status, waktu_daftar, created_at) VALUES (?, ?, ?, 'menunggu', NOW(), NOW())");

// Gunakan "iss" jika id_pasien adalah angka (integer), atau "sss" jika teks/string
$stmt->bind_param("sss", $id_pasien, $nomor_baru, $poli);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "nomor"   => $nomor_baru,
        "poli"    => $poli,
        "existed" => false
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Gagal simpan: " . $stmt->error
    ]);
}

$stmt->close();
?>