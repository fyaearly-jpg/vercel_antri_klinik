<?php
// api/dashboard_pasien.php
include 'koneksi.php';

// 1. Ambil data dari Cookie
$cookie_data = isset($_COOKIE['user_session']) ? json_decode(base64_decode($_COOKIE['user_session']), true) : null;

// 2. Jika tidak ada cookie, tendang ke login
if (!$cookie_data || $cookie_data['role'] !== 'pasien') {
    header("Location: /login");
    exit();
}

$id_user = $cookie_data['id'];
$nama_user = $cookie_data['nama'];

// 3. Ambil data antrean terbaru pasien ini
$query = mysqli_query($koneksi, "SELECT * FROM antrian WHERE id_pasien = '$id_user' ORDER BY id DESC LIMIT 1");
$data_antrian = mysqli_fetch_assoc($query);
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
            <a href="logout.php" class="text-red-500 font-semibold">Keluar</a>
        </div>
    </nav>
    
    <main class="container mx-auto p-6">
        <div class="bg-white rounded-3xl shadow-xl p-8 max-w-2xl mx-auto">
            <h2 class="text-2xl font-bold mb-6 text-slate-800">Status Antrean Anda</h2>
            
            <?php if ($data_antrian): ?>
                <div class="text-center bg-emerald-50 p-6 rounded-2xl border-2 border-emerald-100">
                    <p class="text-emerald-600 font-medium">Nomor Antrean:</p>
                    <h3 class="text-5xl font-black text-emerald-700 my-2">
                        <?php echo $data_antrian['nomor_antrian']; ?>
                    </h3>
                    <p class="text-slate-500">Poli: <?php echo $data_antrian['poli']; ?></p>
                    <span class="inline-block mt-4 px-4 py-1 rounded-full text-sm font-bold 
                        <?php echo $data_antrian['status'] == 'dipanggil' ? 'bg-orange-500 text-white' : 'bg-emerald-500 text-white'; ?>">
                        <?php echo strtoupper($data_antrian['status']); ?>
                    </span>
                </div>
            <?php else: ?>
                <div class="text-center py-10">
                    <p class="text-slate-400">Anda belum memiliki antrean aktif hari ini.</p>
                    <a href="/ambil_antrean" class="mt-4 inline-block bg-emerald-600 text-white px-6 py-2 rounded-xl">Ambil Antrean</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>