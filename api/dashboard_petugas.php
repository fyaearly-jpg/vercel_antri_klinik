<?php
include "session_config.php";
include "koneksi.php";
 
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: /login");
    exit();
}
 
$nama_petugas  = $_SESSION['nama'];
$query_antrian = mysqli_query($koneksi, "SELECT a.*, p.nama_pasien FROM antrian a 
                 JOIN pasien p ON a.id_pasien = p.id 
                 WHERE a.status = 'menunggu' 
                 ORDER BY a.created_at ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Petugas - Antrian Klinik</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
    <nav class="bg-emerald-700 text-white p-4 flex justify-between">
        <h1 class="font-bold">Layanan Antrean Klinik</h1>
        <div class="flex items-center gap-4">
            <span>Petugas: <?php echo htmlspecialchars($nama_petugas); ?></span>
            <a href="/logout" class="bg-white text-emerald-700 px-3 py-1 rounded text-sm font-bold">Logout</a>
        </div>
    </nav>
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="p-4">No. Antrian</th>
                        <th class="p-4">Nama Pasien</th>
                        <th class="p-4">Poli</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($query_antrian)): ?>
                    <tr class="border-t">
                        <td class="p-4 font-bold text-emerald-600"><?php echo $row['nomor_antrian']; ?></td>
                        <td class="p-4"><?php echo $row['nama_pasien']; ?></td>
                        <td class="p-4"><?php echo $row['poli']; ?></td>
                        <td class="p-4 flex gap-2">
                            <button onclick="panggil('<?php echo $row['nomor_antrian']; ?>')" class="bg-blue-500 text-white px-3 py-1 rounded">Panggil</button>
                            <a href="/update_status?id=<?php echo $row['id']; ?>&status=selesai" class="bg-emerald-500 text-white px-3 py-1 rounded">Selesai</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function panggil(nomor) {
            const msg = new SpeechSynthesisUtterance("Nomor antrian " + nomor + ", silakan menuju ruang pemeriksaan");
            msg.lang = 'id-ID';
            window.speechSynthesis.speak(msg);
        }
    </script>
</body>
</html>