<?php
session_start();
include 'koneksi.php';

// 1. Cek apakah ada feedback yang dikirim (hanya dari Pasien)
if (isset($_POST['kirim_feedback'])) {
    $kepuasan = mysqli_real_escape_string($conn, $_POST['kepuasan']);
    $saran = mysqli_real_escape_string($conn, $_POST['saran']);
    
    // Ambil nama dari session pasien sebelum dihancurkan
    $nama = isset($_SESSION['nama_pasien']) ? $_SESSION['nama_pasien'] : 'Anonim';

    $query_fb = "INSERT INTO feedback (nama_pasien, kepuasan, saran) VALUES ('$nama', '$kepuasan', '$saran')";
    mysqli_query($conn, $query_fb);
}

// 2. Hapus semua data session (untuk Pasien maupun Petugas)
$_SESSION = array(); // Kosongkan array session
session_destroy();   // Hancurkan session di server

// 3. Bersihkan cookie session jika ada (opsional, biar makin bersih)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Lempar balik ke pintu utama
echo "<script>
    alert('Anda telah keluar dari sistem.');
    window.location.href = 'index.php';
</script>";
exit();
?>