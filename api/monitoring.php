<?php
// api/monitoring.php
include 'koneksi.php';

// Pastikan hanya mengambil antrean hari ini
$hari_ini = date('Y-m-d');
$query = mysqli_query($koneksi, "SELECT * FROM antrian WHERE DATE(created_at) = '$hari_ini' ORDER BY id ASC");

// Jika ingin menggunakan AJAX untuk update otomatis, gunakan script berikut di bagian bawah file:
?>

<script>
    function refreshMonitoring() {
        fetch('monitoring.php')
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.querySelector('#tabel-antrian').innerHTML;
                document.querySelector('#tabel-antrian').innerHTML = newTable;
            });
    }
    // Refresh otomatis setiap 5 detik
    setInterval(refreshMonitoring, 5000);
</script>