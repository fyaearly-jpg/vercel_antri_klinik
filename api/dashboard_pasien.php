<?php
// api/dashboard_pasien.php
include 'koneksi.php';
include 'auth_helper.php';

$user = get_user_session();

if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'Belum login']);
    exit();
}

$id_pasien = $user['id']; // Ambil ID dari Cookie

// Jalankan query seperti biasa
$sql = "SELECT * FROM antrian WHERE id_pasien = '$id_pasien' ORDER BY id DESC LIMIT 1";
$query_antrian = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($query_antrian);

header('Content-Type: application/json');
echo json_encode($data ?: ['status' => 'none', 'nomor_antrian' => '-']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pasien - Klinik Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
    <nav class="bg-white shadow-md p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold text-emerald-600">Klinik Sehat</h1>
        <div class="flex items-center gap-4">
            <span class="text-slate-600">Halo, <strong><?php echo htmlspecialchars($nama_user); ?></strong></span>
            <a href="/logout" class="text-red-500 font-semibold">Keluar</a>
        </div>
    </nav>
    <main class="container mx-auto p-6">
        <div class="bg-white rounded-3xl shadow-xl p-8 max-w-2xl mx-auto">
            <h2 class="text-2xl font-bold mb-6 text-slate-800">Status Antrean Anda</h2>
            <?php if ($data_antrian): ?>
                <div class="bg-emerald-50 border-2 border-emerald-100 rounded-2xl p-6 text-center">
                    <p class="text-emerald-600 font-medium uppercase tracking-wider text-sm">Nomor Antrean</p>
                    <h3 class="text-6xl font-black text-emerald-700 my-2"><?php echo $data_antrian['nomor_antrian']; ?></h3>
                    <p class="text-slate-500">Poli: <?php echo $data_antrian['poli']; ?></p>
                    <div class="mt-4 inline-block px-4 py-1 rounded-full text-sm font-bold 
                        <?php echo $data_antrian['status'] == 'menunggu' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600'; ?>">
                        <?php echo strtoupper($data_antrian['status']); ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-10">
                    <p class="text-slate-400">Anda belum memiliki antrean aktif hari ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>