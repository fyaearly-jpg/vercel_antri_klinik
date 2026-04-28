<?php
header("Content-Type: application/json");
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Method tidak valid"]);
    exit();
}

$poli      = trim($_POST['poli'] ?? '');
$id_pasien = trim($_POST['id_pasien'] ?? '');
$tanggal   = date('Y-m-d');

if (empty($poli) || empty($id_pasien)) {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak lengkap",
        "debug"   => ["poli" => $poli, "id_pasien" => $id_pasien]
    ]);
    exit();
}

$poli_escaped      = mysqli_real_escape_string($koneksi, $poli);
$id_pasien_escaped = mysqli_real_escape_string($koneksi, $id_pasien);

// Cek antrean existing
$cek = mysqli_query($koneksi,
    "SELECT nomor_antrean, poli, status FROM antrian 
     WHERE id_pasien='$id_pasien_escaped' AND DATE(created_at)='$tanggal' LIMIT 1"
);

if ($cek && mysqli_num_rows($cek) > 0) {
    $ex = mysqli_fetch_assoc($cek);
    mysqli_free_result($cek); // ✅ FIX: Bebaskan result set
    echo json_encode([
        "success" => true,
        "nomor"   => $ex['nomor_antrean'],
        "poli"    => $ex['poli'],
        "existed" => true,
        "message" => "Anda sudah punya antrean hari ini"
    ]);
    exit();
}
mysqli_free_result($cek); // ✅ FIX: Bebaskan result set meski kosong

// Hitung nomor berikutnya
$kode_poli = strtoupper(substr($poli, 0, 1));
$q_max = mysqli_query($koneksi,
    "SELECT nomor_antrean FROM antrian 
     WHERE poli='$poli_escaped' AND DATE(created_at)='$tanggal' 
     ORDER BY id DESC LIMIT 1"
);
$row_max = mysqli_fetch_assoc($q_max);
mysqli_free_result($q_max); // ✅ FIX: Wajib free sebelum prepare()

if ($row_max) {
    $last_no = explode('-', $row_max['nomor_antrean']);
    $next_no = (int)end($last_no) + 1;
} else {
    $next_no = 1;
}
$nomor_baru = $kode_poli . "-" . $next_no;

// ✅ FIX: Pastikan koneksi bersih sebelum prepare
mysqli_next_result($koneksi); // flush sisa result jika ada

$stmt = $koneksi->prepare(
    "INSERT INTO antrian (id_pasien, nomor_antrean, poli, status, waktu_daftar, created_at) 
     VALUES (?, ?, ?, 'menunggu', NOW(), NOW())"
);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare gagal: " . $koneksi->error
    ]);
    exit();
}

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
mysqli_close($koneksi); // ✅ Tutup koneksi di akhir
?>