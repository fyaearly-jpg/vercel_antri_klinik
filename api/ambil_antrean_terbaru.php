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
 
// Validasi
if (empty($poli) || empty($id_pasien)) {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak lengkap",
        "debug"   => ["poli" => $poli, "id_pasien" => $id_pasien]
    ]);
    exit();
}
 
// Escape SETELAH validasi
$poli      = mysqli_real_escape_string($koneksi, $poli);
$id_pasien = mysqli_real_escape_string($koneksi, $id_pasien);
 
// Cek sudah punya antrean hari ini
$cek = mysqli_query($koneksi,
    "SELECT nomor_antrean, poli, status FROM antrian
     WHERE id_pasien='$id_pasien' AND DATE(created_at)='$tanggal' LIMIT 1"
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
 
// Hitung nomor urut per poli hari ini
$kode_poli  = strtoupper(substr($poli, 0, 1));
$q_max      = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM antrian WHERE poli='$poli' AND DATE(created_at)='$tanggal'");
$row_max    = mysqli_fetch_assoc($q_max);
$next_no    = (int)($row_max['total'] ?? 0) + 1;
$nomor_baru = $kode_poli . "-" . $next_no;
 
// Insert - gunakan waktu_daftar sesuai struktur tabel
$sql = "INSERT INTO antrian (id_pasien, nomor_antrean, poli, status, waktu_daftar, created_at)
        VALUES ('$id_pasien', '$nomor_baru', '$poli', 'menunggu', NOW(), NOW())";
 
if (mysqli_query($koneksi, $sql)) {
    echo json_encode([
        "success" => true,
        "nomor"   => $nomor_baru,
        "poli"    => $poli,
        "existed" => false
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Gagal simpan: " . mysqli_error($koneksi),
        "debug"   => ["nomor_baru" => $nomor_baru, "id_pasien" => $id_pasien, "poli" => $poli]
    ]);
}
?>