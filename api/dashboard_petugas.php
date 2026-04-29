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
$role_user = $cookie_data['role'] ?? 'Petugas';
$hari_ini = date('Y-m-d'); // Variabel wajib untuk filter data hari ini

// 3. AMBIL DATA STATISTIK UNTUK WIDGET
// Total antrean hari ini
$q_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM antrian WHERE DATE(created_at) = '$hari_ini'");
$total_antrean = mysqli_fetch_assoc($q_total)['total'] ?? 0;

$q_selesai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM antrian WHERE status = 'selesai' AND DATE(created_at) = '$hari_ini'");
$total_selesai = mysqli_fetch_assoc($q_selesai)['total'] ?? 0;

$q_current = mysqli_query($koneksi, "SELECT nomor_antrean, poli FROM antrian WHERE status = 'dipanggil' AND DATE(created_at) = '$hari_ini' ORDER BY updated_at DESC LIMIT 1");
$current = mysqli_fetch_assoc($q_current);
$nomor_sekarang = $current['nomor_antrean'] ?? '--';
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

// Menghitung jumlah antrean per hari selama 7 hari terakhir
$data_kunjungan = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $res = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM antrian WHERE DATE(created_at) = '$tgl'");
    $row = mysqli_fetch_assoc($res);
    $data_kunjungan[] = (int)$row['total'];
}
$data_grafik_json = json_encode($data_kunjungan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas | Digital Clinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="min-h-screen p-4 md:p-10">

    <div class="max-w-6xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200 gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-emerald-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                    <i class="fas fa-user-md text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Halo, <?php echo htmlspecialchars($nama_petugas); ?>!</h1>
                    <p class="text-slate-500">Selamat bertugas di <span class="text-emerald-600 font-bold uppercase tracking-wider text-sm"><?php echo $role_user; ?> Panel</span></p>
                </div>
            </div>
            <a href="/api/logout.php" class="w-full md:w-auto bg-red-500 hover:bg-red-600 text-white px-8 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-red-100 flex items-center justify-center gap-2 active:scale-95">
                <i class="fas fa-sign-out-alt"></i> Keluar Sistem
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Total Antrean</p>
                <h3 class="text-3xl font-black text-slate-800"><?php echo $total_antrean; ?></h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Sudah Dilayani</p>
                <h3 class="text-3xl font-black text-emerald-500"><?php echo $total_selesai; ?></h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Tanggal</p>
                <h3 class="text-lg font-bold text-slate-700"><?php echo date('d M Y'); ?></h3>
            </div>
            <div class="bg-emerald-600 p-6 rounded-3xl shadow-lg shadow-emerald-100 text-white">
                <p class="text-emerald-200 text-xs font-bold uppercase tracking-widest mb-1">Status Server</p>
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <span class="w-2 h-2 bg-white rounded-full animate-ping"></span> Online
                </h3>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 mb-10">
            
            <a href="/api/monitoring.php" class="group bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 hover:shadow-emerald-200/50 transition-all border border-slate-100 hover:-translate-y-2">
                <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                    <i class="fas fa-chalkboard-user text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800">Monitoring Antrean</h2>
                <p class="text-slate-500 mt-3 leading-relaxed">Panggil nomor antrean, kelola status pelayanan, dan lihat daftar tunggu pasien.</p>
                <div class="mt-8 flex items-center text-emerald-600 font-bold group-hover:gap-4 gap-2 transition-all">
                    <span>Kelola Sekarang</span>
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="/api/display.php" target="_blank" class="group bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 hover:shadow-blue-200/50 transition-all border border-slate-100 hover:-translate-y-2">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                    <i class="fas fa-desktop text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800">Layar Display TV</h2>
                <p class="text-slate-500 mt-3 leading-relaxed">Tampilkan nomor antrean secara real-time untuk layar monitor di ruang tunggu.</p>
                <div class="mt-8 flex items-center text-blue-600 font-bold group-hover:gap-4 gap-2 transition-all">
                    <span>Buka Display</span>
                    <i class="fas fa-external-link-alt"></i>
                </div>
            </a>

        </div>

        <div class="bg-white p-8 md:p-12 rounded-[3rem] shadow-xl border border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Statistik Kunjungan Poli</h2>
                    <p class="text-slate-500">Distribusi pasien per departemen hari ini</p>
                </div>
                <button onclick="updateChart()" class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 hover:text-emerald-500 hover:bg-emerald-50 transition-all">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            
            <div class="h-[350px]">
                <canvas id="chartPoli"></canvas>
            </div>
        </div>

        <p class="text-center text-slate-400 mt-12 text-sm">
            &copy; 2026 Digital Clinic System &bull; Universitas Sebelas Maret Project
        </p>

    </div>

    <script>
        const ctx = document.getElementById('statistikChart').getContext('2d');
        
        // Pastikan data ini diambil dari query database Anda
        // Contoh data dummy jika data asli belum ditarik:
        const dataKunjungan = <?php echo json_encode($data_grafik_anda ?? [0,0,0,0,0,0,0]); ?>;
        const labelHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labelHari,
                datasets: [{
                    label: 'Jumlah Pengunjung',
                    data: dataKunjungan,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#10b981'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>

    <script>
       function refreshMonitoring() {
        fetch('/monitoring') // Sesuaikan dengan rute vercel.json
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('table'); // Ambil tabelnya saja
                if(newContent) {
                    document.querySelector('.overflow-x-auto').innerHTML = newContent.outerHTML;
                }
            });
        }
        setInterval(refreshMonitoring, 5000);
    </script>

</body>
</html>