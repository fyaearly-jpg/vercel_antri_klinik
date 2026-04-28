<?php
include "koneksi.php";
 
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /register");
    exit();
}
 
$email    = mysqli_real_escape_string($koneksi, $_POST['email']    ?? '');
$nama     = mysqli_real_escape_string($koneksi, $_POST['nama']     ?? '');
$role_raw = $_POST['role'] ?? 'pasien';
$password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
 
// ✅ FIX 1: Whitelist role agar tidak bisa diisi sembarangan dari luar
$allowed_roles = ['pasien', 'staff', 'admin'];
$role = in_array($role_raw, $allowed_roles) ? $role_raw : 'pasien';
 
if (empty($email) || empty($nama)) {
    header("Location: /register?error=data_kosong");
    exit();
}
 
// ✅ FIX 2: Cek email duplikat sebelum insert
$cek_email = mysqli_query($koneksi, "SELECT id FROM pasien WHERE email='$email' LIMIT 1");
if (mysqli_num_rows($cek_email) > 0) {
    header("Location: /register?error=email_exists");
    exit();
}
 
// ✅ FIX 3: $query_simpan tidak pernah dijalankan — query SQL tidak di-execute!
// Dulu: $sql = "INSERT ..." tapi tidak ada mysqli_query()
// Sekarang: langsung jalankan query-nya
if ($role === 'pasien') {
    // Pasien disimpan ke tabel pasien
    $query_simpan = mysqli_query($koneksi,
        "INSERT INTO pasien (email, nama_pasien, password, role) 
         VALUES ('$email', '$nama', '$password', '$role')"
    );
} else {
    // Staff/admin disimpan ke tabel petugas
    $query_simpan = mysqli_query($koneksi,
        "INSERT INTO petugas (email, nama_lengkap, password, role) 
         VALUES ('$email', '$nama', '$password', '$role')"
    );
}
 
if ($query_simpan) {
    // ✅ FIX 4: Ganti echo <script> dengan header redirect — lebih bersih dan pasti jalan
    header("Location: /login?success=register");
    exit();
} else {
    header("Location: /register?error=gagal_simpan");
    exit();
}
?>