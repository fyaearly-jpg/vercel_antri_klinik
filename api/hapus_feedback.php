<?php
include 'koneksi.php';
 
// ✅ FIX: Ganti $_SESSION ke cookie, konsisten dengan sistem autentikasi yang dipakai
$cookie_raw  = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;
 
// Proteksi: hanya admin/petugas/staff yang boleh akses
$role = isset($cookie_data['role']) ? strtolower(trim($cookie_data['role'])) : '';
$allowed = ['admin', 'petugas', 'staff'];
 
if (!$cookie_data || !in_array($role, $allowed)) {
    header("Location: /login");
    exit();
}
 
// Hapus satu feedback berdasarkan ID
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    mysqli_query($koneksi, "DELETE FROM feedback WHERE id = '$id'");
}
 
// Reset semua feedback
if (isset($_GET['aksi']) && $_GET['aksi'] === 'reset_semua') {
    mysqli_query($koneksi, "TRUNCATE TABLE feedback");
}
 
header("Location: /api/dashboard_admin.php");
exit();
?>