<?php
// api/dashboard_petugas.php
include 'koneksi.php';

// 1. Ambil data dari Cookie
$cookie_raw = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

// 2. LOGIKA PROTEKSI (DIPERBAIKI: Menambahkan role 'staff')
$role = isset($cookie_data['role']) ? strtolower(trim($cookie_data['role'])) : '';
$allowed_roles = ['petugas', 'staff'];

if (!$cookie_data || !in_array($role, $allowed_roles)) {
    header("Location: /login");
    exit();
}

$nama_petugas = $cookie_data['nama'];
$hari_ini = date('Y-m-d'); // Variabel wajib untuk filter data hari ini

// 3. AMBIL DATA STATISTIK UNTUK WIDGET
// Total antrean hari ini
$q_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM antrian WHERE DATE(created_at) = '$hari_ini'");
$total_antrean = mysqli_fetch_assoc($q_total)['total'] ?? 0;

// Antrean yang sedang dipanggil (agar sinkron dengan monitoring)
$q_current = mysqli_query($koneksi, "SELECT nomor_antrean, poli FROM antrian WHERE status = 'dipanggil' AND DATE(created_at) = '$hari_ini' ORDER BY updated_at DESC LIMIT 1");
$current = mysqli_fetch_assoc($q_current);
$nomor_sekarang = $current['nomor_antrian'] ?? '--';
$poli_sekarang = $current['poli'] ?? 'Tidak ada';

// 4. DATA UNTUK GRAFIK (SINKRON DENGAN TIDB)
$labels = [];
$data_grafik = [];
$res = mysqli_query($koneksi, "SELECT poli, COUNT(*) as total FROM antrian WHERE DATE(created_at) = '$hari_ini' GROUP BY poli");

if ($res && mysqli_num_rows($res) > 0) {
    while($row = mysqli_fetch_assoc($res)) {
        $labels[] = $row['poli'];
        $data_grafik[] = (int)$row['total'];
    }
} else {
    $labels = ['Belum Ada Data'];
    $data_grafik = [0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - Klinik Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">

    <nav class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <div class="bg-blue-600 p-2 rounded-lg">
                        <i class="fas fa-clinic-medical text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-800 tracking-tight">Klinik<span class="text-blue-600">Digital</span></span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($nama_petugas); ?></p>
                        <p class="text-xs text-gray-500 capitalize"><?php echo $role; ?></p>
                    </div>
                    <a href="/logout" class="bg-gray-100 hover:bg-red-50 text-gray-600 hover:text-red-600 p-2 rounded-full transition-all">
                        <i class="fas fa-power-off"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Selamat Datang, <?php echo htmlspecialchars($nama_petugas); ?> 👋</h1>
            <p class="text-gray-500">Ringkasan aktivitas klinik tanggal <?php echo date('d/m/Y'); ?></p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-100 text-blue-600 p-4 rounded-xl">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Pasien Hari Ini</p>
                            <h3 class="text-3xl font-bold text-gray-900"><?php echo $total_antrean; ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="bg-emerald-100 text-emerald-600 p-4 rounded-xl">
                            <i class="fas fa-bullhorn text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Sekarang di <?php echo htmlspecialchars($poli_sekarang); ?></p>
                            <h3 class="text-2xl font-bold text-gray-900"><?php echo $nomor_sekarang; ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-6 rounded-2xl shadow-lg text-white">
                    <h4 class="font-bold mb-2">Mulai Panggil Antrean?</h4>
                    <p class="text-blue-100 text-sm mb-4">Masuk ke halaman monitoring untuk mengelola antrean masuk.</p>
                    <a href="/monitoring" class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-bold inline-block hover:bg-blue-50 transition-all">
                        Buka Monitoring <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-full">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800"><i class="fas fa-chart-pie text-blue-600 mr-2"></i> Kunjungan Per Poli</h3>
                        <span class="text-xs font-medium bg-gray-100 text-gray-500 px-3 py-1 rounded-full">Real-time</span>
                    </div>
                    <div class="relative" style="height: 300px;">
                        <canvas id="antrianChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const ctx = document.getElementById('antrianChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Jumlah Pasien',
                    data: <?php echo json_encode($data_grafik); ?>,
                    backgroundColor: 'rgba(37, 99, 235, 0.7)',
                    hoverBackgroundColor: 'rgba(37, 99, 235, 1)',
                    borderRadius: 10,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { borderDash: [5, 5] } }
                }
            }
        });
    </script>
</body>
</html>