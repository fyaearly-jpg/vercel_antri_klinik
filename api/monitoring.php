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