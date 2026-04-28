<?php
// api/monitoring.php
include 'koneksi.php';

// 1. Pengaturan Waktu & Query
$hari_ini = date('Y-m-d');

// Mengambil data antrean hari ini, diurutkan dari yang terbaru
$query = mysqli_query($koneksi, "SELECT * FROM antrian 
                                WHERE DATE(created_at) = '$hari_ini' 
                                ORDER BY id DESC");
?>

<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-slate-100">
                <th class="py-4 px-2 text-xs font-bold text-slate-400 uppercase tracking-wider">No. Antrean</th>
                <th class="py-4 px-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Poli Tujuan</th>
                <th class="py-4 px-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                <th class="py-4 px-2 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Jam</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            <?php if (mysqli_num_rows($query) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-2">
                            <span class="font-black text-slate-700"><?php echo $row['nomor_antrean']; ?></span>
                        </td>
                        <td class="py-4 px-2 text-sm text-slate-600">
                            <?php echo $row['poli']; ?>
                        </td>
                        <td class="py-4 px-2">
                            <?php 
                                // Logika Warna Status
                                $status = strtolower($row['status']);
                                $badge_class = "bg-slate-100 text-slate-500"; // Default (Menunggu)
                                
                                if ($status == 'dipanggil') {
                                    $badge_class = "bg-emerald-100 text-emerald-600 font-bold";
                                } elseif ($status == 'selesai') {
                                    $badge_class = "bg-blue-50 text-blue-500";
                                }
                            ?>
                            <span class="px-3 py-1 rounded-full text-[10px] uppercase font-bold <?php echo $badge_class; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td class="py-4 px-2 text-right text-xs text-slate-400 font-medium">
                            <?php echo date('H:i', strtotime($row['created_at'])); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="py-10 text-center text-slate-400 text-sm italic">
                        Belum ada aktivitas antrean hari ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>