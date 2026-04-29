<?php
header("Content-Type: application/json");
include 'koneksi.php';

$cookie_raw = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

if (!$cookie_data || $cookie_data['role'] !== 'pasien') {
    echo json_encode(["success" => false, "message" => "Sesi habis, silakan login ulang"]);
    exit();
}

$poli      = trim($_POST['poli'] ?? '');
$id_pasien = $cookie_data['id']; // Langsung ambil dari cookie agar lebih aman
$tanggal   = date('Y-m-d');

if (empty($poli)) { // Cukup cek poli saja
    echo json_encode([
        "success" => false,
        "message" => "Poli belum dipilih"
    ]);
    exit();
}

$poli_escaped      = mysqli_real_escape_string($koneksi, $poli);
$id_pasien_escaped = mysqli_real_escape_string($koneksi, $id_pasien);

// 1. Cek antrean existing
$cek = mysqli_query($koneksi,
    "SELECT nomor_antrean, poli, status FROM antrian 
     WHERE id_pasien='$id_pasien_escaped' AND DATE(created_at)='$tanggal' LIMIT 1"
);

if ($cek && mysqli_num_rows($cek) > 0) {
    $ex = mysqli_fetch_assoc($cek);
    mysqli_free_result($cek); // Bebaskan jika ada hasil
    echo json_encode([
        "success" => true,
        "nomor"   => $ex['nomor_antrean'],
        "poli"    => $ex['poli'],
        "existed" => true,
        "message" => "Anda sudah punya antrean hari ini"
    ]);
    exit();
}
if ($cek) mysqli_free_result($cek); // Bebaskan hanya jika query berhasil (meski 0 baris)

// 2. Hitung nomor berikutnya
$kode_poli = strtoupper(substr($poli, 0, 1));
$q_max = mysqli_query($koneksi,
    "SELECT nomor_antrean FROM antrian 
     WHERE poli='$poli_escaped' AND DATE(created_at)='$tanggal' 
     ORDER BY id DESC LIMIT 1"
);

$row_max = mysqli_fetch_assoc($q_max);
if ($q_max) mysqli_free_result($q_max); // Bebaskan sebelum masuk ke prepare

if ($row_max) {
    $last_no_parts = explode('-', $row_max['nomor_antrean']);
    $next_no = (int)end($last_no_parts) + 1;
} else {
    $next_no = 1;
}
$nomor_baru = $kode_poli . "-" . $next_no;

// 3. Simpan Antrean
mysqli_next_result($koneksi); // Bersihkan sisa result set

$stmt = $koneksi->prepare(
    "INSERT INTO antrian (id_pasien, nomor_antrean, poli, status, waktu_daftar, created_at) 
     VALUES (?, ?, ?, 'menunggu', NOW(), NOW())"
);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Server Error (Prepare): " . $koneksi->error
    ]);
    exit();
}

// "iss" -> i (id_pasien/int), s (nomor/string), s (poli/string)
$stmt->bind_param("iss", $id_pasien, $nomor_baru, $poli);

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
        "message" => "Database Error: " . $stmt->error
    ]);
}

$stmt->close();
mysqli_close($koneksi);
?>