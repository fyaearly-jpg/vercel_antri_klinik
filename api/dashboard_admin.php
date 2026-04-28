<?php
// api/dashboard_admin.php
include 'koneksi.php';
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
// 1. Ambil data dari Cookie (Sesuai dengan cek_login.php)
$cookie_raw = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

// 2. Proteksi: Wajib Admin (Mencegah terpental jika role benar)
$role = isset($cookie_data['role']) ? strtolower(trim($cookie_data['role'])) : '';
if (!$cookie_data || $role !== 'admin') {
    header("Location: /login");
    exit();
}

$nama_admin = $cookie_data['nama'];
$hari_ini = date('Y-m-d');

// 3. FITUR STATISTIK (Sinkron dengan data asli)
// Total Antrean Hari Ini
$q_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM antrian WHERE DATE(created_at) = '$hari_ini'");
$total_antrean = mysqli_fetch_assoc($q_total)['total'] ?? 0;

// Total Pasien Terdaftar (Sinkron dengan tabel pasien)
$q_pasien = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pasien");
$total_pasien = mysqli_fetch_assoc($q_pasien)['total'] ?? 0;

// Feedback Terbaru (Sinkron dengan simpan_feedback.php)
$q_feedback = mysqli_query($koneksi, "SELECT * FROM feedback ORDER BY id DESC LIMIT 5");
$total_feedback = mysqli_fetch_assoc($q_feedback)['total'] ?? 0;

// 4. DATA UNTUK GRAFIK (Perbaikan Unknown Column nomor_antrian)
$labels = [];
$data_grafik = [];
$res = mysqli_query($koneksi, "SELECT poli, COUNT(*) as total FROM antrian WHERE DATE(created_at) = '$hari_ini' GROUP BY poli");
while($row = mysqli_fetch_assoc($res)) {
    $labels[] = $row['poli'];
    $data_grafik[] = (int)$row['total'];
}

// Placeholder jika data kosong agar Chart.js tidak error
if(empty($labels)) { $labels = ['Belum Ada Data']; $data_grafik = [0]; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Panel Kendali</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 50px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
</head>
<body class="bg-slate-50 p-4 md:p-8">
    <div class="max-w-5xl mx-auto">
        
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Panel Kendali Admin</h1>
                <p class="text-slate-500">Selamat datang kembali, Pengelola Sistem.</p>
            </div>
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-red-100 transition-all flex items-center">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-md transition-all">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-user-md text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 mb-2">Manajemen Petugas</h2>
                <p class="text-slate-400 text-sm mb-6 leading-relaxed">Kelola akun Dokter, Perawat, dan Staff klinik Anda di sini.</p>
                <a href="kelola_petugas.php" class="inline-block text-emerald-500 font-bold hover:translate-x-2 transition-transform">Kelola Petugas <i class="fas fa-arrow-right ml-1"></i></a>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-md transition-all">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 mb-2">Manajemen Pasien</h2>
                <p class="text-slate-400 text-sm mb-6 leading-relaxed">Edit profil pasien atau reset akun pasien yang terdaftar.</p>
                <a href="kelola_pasien.php" class="inline-block text-blue-500 font-bold hover:translate-x-2 transition-transform">Kelola Pasien <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-800">Suara Pasien</h2>
                    <p class="text-slate-400 text-sm">Masukan terbaru untuk evaluasi pelayanan</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden md:block bg-slate-50 text-slate-400 text-[10px] font-bold px-3 py-1.5 rounded-full uppercase tracking-wider">
                        Scroll <i class="fas fa-arrow-down ml-1"></i>
                    </span>
                    <a href="hapus_feedback.php?aksi=reset_semua" 
                       onclick="return confirm('Yakin ingin menghapus SEMUA feedback?')"
                       class="bg-red-50 text-red-600 px-5 py-2 rounded-xl text-xs font-bold border border-red-100 hover:bg-red-100 transition-all flex items-center">
                       <i class="fas fa-trash-alt mr-2"></i> RESET SEMUA
                    </a>
                </div>
            </div>

            <div class="overflow-y-auto pr-2 space-y-3 custom-scrollbar" style="max-height: 450px;">
                <?php
                
                if (mysqli_num_rows($q_feedback) > 0) {
                    while ($f = mysqli_fetch_assoc($q_feedback)) {
                        // Logika Warna Badge
                        $kepuasan = strtoupper($f['kepuasan']);
                        $badgeClass = "bg-slate-100 text-slate-500"; // Default
                        
                        if ($kepuasan == 'SANGAT PUAS') $badgeClass = "bg-emerald-100 text-emerald-600";
                        elseif ($kepuasan == 'PUAS') $badgeClass = "bg-blue-100 text-blue-600";
                        elseif ($kepuasan == 'KURANG') $badgeClass = "bg-red-100 text-red-600";
                ?>
                
                <div class="group flex flex-col md:flex-row md:items-center justify-between p-5 rounded-3xl border border-slate-50 hover:bg-slate-50/80 hover:border-slate-100 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-white shadow-sm rounded-2xl flex items-center justify-center text-slate-300 group-hover:text-emerald-500 transition-colors">
                            <i class="fas fa-comment-dots text-xl"></i>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="font-bold text-slate-700"><?php echo htmlspecialchars($f['nama_pasien']); ?></span>
                                <span class="text-[9px] <?php echo $badgeClass; ?> px-2.5 py-0.5 rounded-full font-black tracking-wider uppercase">
                                    <?php echo $f['kepuasan']; ?>
                                </span>
                            </div>
                            <p class="text-sm text-slate-500 leading-relaxed italic">"<?php echo htmlspecialchars($f['saran']); ?>"</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between md:justify-end gap-6 mt-4 md:mt-0">
                        <div class="text-left md:text-right">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter"><?php echo date('d M Y', strtotime($f['created_at'])); ?></p>
                            <p class="text-[10px] text-slate-300 font-medium"><?php echo date('H:i', strtotime($f['created_at'])); ?> WIB</p>
                        </div>
                        <a href="hapus_feedback.php?id=<?php echo $f['id']; ?>" 
                           onclick="return confirm('Hapus feedback dari <?php echo $row['nama_pasien']; ?>?')"
                           class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-slate-300 hover:bg-red-50 hover:text-red-500 shadow-sm transition-all">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </a>
                    </div>
                </div>

                <?php 
                    }
                } else {
                    echo "
                    <div class='flex flex-col items-center justify-center py-20 text-slate-300'>
                        <i class='fas fa-inbox text-5xl mb-4'></i>
                        <p class='italic font-medium'>Belum ada masukan dari pasien.</p>
                    </div>";
                }
                ?>
            </div> 
        </div>

    </div>
</body>
</html>