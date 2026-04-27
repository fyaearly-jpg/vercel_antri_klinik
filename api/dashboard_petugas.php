<?php
// api/dashboard_petugas.php
include 'koneksi.php';

// 1. Ambil data dari Cookie
$cookie_raw = $_COOKIE['user_session'] ?? null;

// Jika tidak ada cookie, langsung pental ke login
if (!$cookie_raw) {
    header("Location: /login");
    exit();
}

$cookie_data = json_decode(base64_decode($cookie_raw), true);
$role = isset($cookie_data['role']) ? strtolower(trim($cookie_data['role'])) : '';

// 2. LOGIKA PROTEKSI (Kunci utama agar tidak pental)
// Izinkan 'petugas', 'admin', DAN 'staff' untuk melihat halaman ini
$allowed_roles = ['petugas', 'admin', 'staff'];

if (!in_array($role, $allowed_roles)) {
    // Jika role tidak terdaftar di atas, pental!
    header("Location: /login?error=unauthorized");
    exit();
}

$nama_petugas = $cookie_data['nama'];

// ... (lanjutkan ke query statistik dan HTML UI Anda)

// 2. Persiapan Data Grafik (PERBAIKAN ERROR)
$hari_ini = date('Y-m-d');
$labels = [];
$data_grafik = [];

// Query untuk menghitung jumlah antrean per poli hari ini
$query_grafik = "SELECT poli, COUNT(*) as total 
                 FROM antrian 
                 WHERE DATE(created_at) = '$hari_ini' 
                 GROUP BY poli";

$res = mysqli_query($koneksi, $query_grafik);

if ($res && mysqli_num_rows($res) > 0) {
    while($row = mysqli_fetch_assoc($res)) {
        $labels[] = $row['poli'] ?: 'Umum'; // Fallback jika poli kosong
        $data_grafik[] = (int)$row['total'];
    }
} else {
    // Data default jika belum ada antrean hari ini
    $labels = ['Belum ada antrean'];
    $data_grafik = [0];
}

// 3. Ambil total antrean hari ini untuk widget statistik
$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM antrian WHERE DATE(created_at) = '$hari_ini'");
$total_hari_ini = mysqli_fetch_assoc($query_total)['total'] ?? 0;
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
            <p class="text-gray-500">Berikut adalah ringkasan antrean pasien untuk hari ini.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-100 text-blue-600 p-4 rounded-xl">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Antrean Hari Ini</p>
                            <h3 class="text-3xl font-bold text-gray-900"><?php echo $total_hari_ini; ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-600 to-blue-700 p-6 rounded-2xl shadow-lg text-white">
                    <h4 class="font-bold mb-2">Panggil Antrean?</h4>
                    <p class="text-blue-100 text-sm mb-4">Mulai memproses antrean pasien yang sedang menunggu.</p>
                    <a href="/monitoring" class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-bold inline-block hover:bg-blue-50 transition-colors">
                        Buka Monitoring <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-full">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800"><i class="fas fa-chart-bar text-blue-600 mr-2"></i> Distribusi Pasien Per Poli</h3>
                        <span class="text-xs font-medium bg-gray-100 text-gray-500 px-3 py-1 rounded-full"><?php echo date('d M Y'); ?></span>
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
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { 
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { borderDash: [5, 5] }
                    }
                }
            }
        });
    </script>
</body>
</html>