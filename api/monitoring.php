<?php
// api/monitoring.php
include 'koneksi.php';

$hari_ini = date('Y-m-d');
$query = mysqli_query($koneksi, "SELECT * FROM antrian WHERE DATE(created_at) = '$hari_ini' ORDER BY id DESC");
?>

<div class="overflow-x-auto">
    <?php if (mysqli_num_rows($query) > 0): ?>
        <table class="w-full text-left text-sm">
            <thead class="text-slate-400 border-b border-slate-50">
                <tr>
                    <th class="pb-3 px-2">NOMOR</th>
                    <th class="pb-3 px-2">POLI</th>
                    <th class="pb-3 px-2 text-right">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($query)): 
                    $status = strtolower($row['status']);
                    $warna = "text-slate-500";
                    if ($status == 'dipanggil') $warna = "text-emerald-600 font-bold";
                    if ($status == 'selesai') $warna = "text-blue-500";
                ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="py-4 px-2 font-black text-slate-800"><?php echo $row['nomor_antrean']; ?></td>
                        <td class="py-4 px-2 text-slate-600"><?php echo $row['poli']; ?></td>
                        <td class="py-4 px-2 text-right">
                            <span class="px-2 py-1 rounded-lg bg-slate-100 text-[10px] uppercase font-bold <?php echo $warna; ?>">
                                <?php echo $status; ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center text-slate-400 py-10">Belum ada antrean.</p>
    <?php endif; ?>
</div>

<script>
    // Fungsi ini akan dijalankan setiap 5 detik
    if (typeof refreshMonitoring === 'undefined') { // Mencegah redeclare fungsi
        function refreshMonitoring() {
            fetch('/monitoring') // Memanggil rute dari vercel.json
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.querySelector('table'); 
                    const container = document.querySelector('.overflow-x-auto');
                    
                    if(newTable && container) {
                        container.innerHTML = newTable.outerHTML;
                    }
                })
                .catch(err => console.warn("Monitoring refresh failed (Normal during navigation)"));
        }
        setInterval(refreshMonitoring, 5000);
    }
</script>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Kontrol Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 p-4 md:p-8">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Panel Petugas</h1>
                <p class="text-slate-500">Kelola antrean real-time hari ini</p>
            </div>
            <div class="flex gap-3">
                <a href="?reset=true" onclick="return confirm('Yakin ingin mereset semua antrean hari ini?')" class="bg-red-50 text-red-600 px-6 py-2 rounded-xl border border-red-100 font-semibold hover:bg-red-100 transition-all">
                    <i class="fas fa-sync-alt mr-2"></i>Reset Hari Ini
                </a>
                <a href="dashboard_petugas.php" class="bg-white px-6 py-2 rounded-xl shadow-sm border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
                    <div class="p-6 border-b border-slate-50 bg-slate-800 text-white">
                        <h2 class="font-bold flex items-center"><i class="fas fa-list-ol mr-2"></i> Antrean Berjalan</h2>
                    </div>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 text-xs uppercase tracking-wider">
                                <th class="p-5 font-semibold">No.</th>
                                <th class="p-5 font-semibold">Nama Pasien</th>
                                <th class="p-5 font-semibold">Poli</th>
                                <th class="p-5 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if(mysqli_num_rows($sql_aktif) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($sql_aktif)): ?>
                                <tr class="hover:bg-slate-50/80 transition-all">
                                    <td class="p-5">
                                        <span class="text-3xl font-black <?php echo ($row['status'] == 'dipanggil') ? 'text-orange-500' : 'text-emerald-600'; ?>">
                                            <?php echo $row['nomor_antrian']; ?>
                                        </span>
                                    </td>
                                    <td class="p-5">
                                        <div class="font-bold text-slate-700"><?php echo htmlspecialchars($row['nama_pasien']); ?></div>
                                        <?php if($row['status'] == 'dipanggil'): ?>
                                            <span class="text-xs text-orange-500 font-bold animate-pulse"><i class="fas fa-bullhorn"></i> SEDANG DIPANGGIL</span>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400">Status: Menunggu</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-5">
                                        <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg text-sm font-medium"><?php echo $row['poli']; ?></span>
                                    </td>
                                    <td class="p-5 text-center">
                                        <?php if($row['status'] == 'menunggu'): ?>
                                            <a href="?panggil=<?php echo $row['id']; ?>" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2 rounded-xl font-bold transition-all shadow-lg shadow-emerald-100">PANGGIL</a>
                                        <?php else: ?>
                                            <a href="?selesai=<?php echo $row['id']; ?>" class="bg-slate-800 hover:bg-black text-white px-6 py-2 rounded-xl font-bold transition-all shadow-lg shadow-slate-200">SELESAI</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="p-20 text-center text-slate-400">Tidak ada antrean aktif.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
                    <div class="p-6 border-b border-slate-50 bg-blue-600 text-white">
                        <h2 class="font-bold flex items-center"><i class="fas fa-history mr-2"></i> Riwayat Selesai</h2>
                    </div>
                    <div class="overflow-y-auto max-h-[600px]">
                        <table class="w-full text-left">
                            <tbody class="divide-y divide-slate-100">
                                <?php if(mysqli_num_rows($sql_riwayat) > 0): ?>
                                    <?php while($row_r = mysqli_fetch_assoc($sql_riwayat)): ?>
                                    <tr class="p-4 block hover:bg-slate-50">
                                        <td class="p-4 block">
                                            <div class="flex justify-between items-start mb-1">
                                                <span class="font-black text-slate-400">#<?php echo $row_r['nomor_antrian']; ?></span>
                                                <span class="text-[10px] text-slate-400 uppercase font-bold"><?php echo date('H:i', strtotime($row_r['updated_at'])); ?></span>
                                            </div>
                                            <div class="font-bold text-slate-700"><?php echo htmlspecialchars($row_r['nama_pasien']); ?></div>
                                            <div class="text-xs text-slate-400"><?php echo $row_r['poli']; ?> • <span class="text-blue-500 italic">Selesai</span></div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="p-10 text-center text-slate-400 italic text-sm">Belum ada riwayat pelayanan.</div>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>