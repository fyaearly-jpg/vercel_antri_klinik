<?php
// api/monitoring.php
include 'koneksi.php';

$hari_ini = date('Y-m-d');
// Ambil semua antrean hari ini, urutkan ID TERBESAR di atas (terbaru)
$query = mysqli_query($koneksi, "SELECT * FROM antrian WHERE DATE(created_at) = '$hari_ini' ORDER BY id DESC");

if (mysqli_num_rows($query) > 0) {
    echo '<table class="w-full text-left text-sm">';
    echo '<thead class="text-slate-400 border-b border-slate-50"><tr><th class="pb-3 px-2">NOMOR</th><th class="pb-3 px-2">POLI</th><th class="pb-3 px-2 text-right">STATUS</th></tr></thead>';
    echo '<tbody>';
    while ($row = mysqli_fetch_assoc($query)) {
        // Logika warna status
        $status = strtolower($row['status']);
        $warna = "text-slate-500";
        if ($status == 'dipanggil') $warna = "text-emerald-600 font-bold";
        if ($status == 'selesai') $warna = "text-blue-500";

        echo "<tr class='border-b border-slate-50 hover:bg-slate-50'>";
        echo "<td class='py-4 px-2 font-black text-slate-800'>{$row['nomor_antrean']}</td>";
        echo "<td class='py-4 px-2 text-slate-600'>{$row['poli']}</td>";
        echo "<td class='py-4 px-2 text-right'><span class='px-2 py-1 rounded-lg bg-slate-100 text-[10px] uppercase font-bold {$warna}'>{$status}</span></td>";
        echo "</tr>";
    }
    echo '</tbody></table>';
} else {
    echo '<p class="text-center text-slate-400 py-10">Belum ada antrean.</p>';
}
?>