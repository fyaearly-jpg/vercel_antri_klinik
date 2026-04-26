<?php


function fetchBpsData() {
    // API URL untuk data JKN 2025
    $url = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/2279/th/125/key/7788dbf0dae1b2bdeb03cdb04fbc23e8/";
    
    // Ambil data dengan timeout 5 detik agar tidak membuat loading dashboard lama
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === FALSE) return [];

    $data = json_decode($response, true);
    if (!isset($data['status']) || $data['status'] !== 'OK') return [];

    $vervar = $data['vervar'] ?? []; 
    $content = $data['datacontent'] ?? []; 

    $finalData = [];
    foreach ($vervar as $v) {
        $id = $v['val'];
        $label = $v['label'];
        
        $val = "0";
        // Mencocokkan ID wilayah dengan data konten BPS
        foreach ($content as $key => $value) {
            if (strpos($key, $id) !== false) {
                $val = $value;
                break;
            }
        }
        
        $finalData[] = [
            'provinsi' => $label,
            'nilai' => $val
        ];
    }
    return $finalData;
}

$data_tabel_bps = fetchBpsData();
?>

<div id="bpsModal" class="fixed inset-0 z-[999] hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm transition-all p-4">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden relative animate-in fade-in zoom-in duration-300">
            
            <div class="p-6 md:p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight uppercase">Statistik JKN Nasional</h2>
                    <p class="text-emerald-600 text-[10px] md:text-xs font-bold uppercase tracking-[0.2em] mt-1">
                        <i class="fas fa-sync-alt fa-spin mr-1"></i> Live Data via BPS API
                    </p>
                </div>
                <button onclick="closeBpsModal()" class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-2xl bg-white shadow-sm border border-slate-100 text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-4 md:p-8 overflow-y-auto max-h-[65vh] custom-scrollbar">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead class="sticky top-0 z-10 bg-white">
                        <tr class="bg-slate-800 text-white shadow-lg">
                            <th class="p-4 rounded-l-2xl font-bold uppercase text-[10px] tracking-widest text-center w-16">No</th>
                            <th class="p-4 font-bold uppercase text-[10px] tracking-widest text-center">Provinsi / Wilayah</th>
                            <th class="p-4 rounded-r-2xl font-bold uppercase text-[10px] tracking-widest text-center">Persentase (%)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <?php if (!empty($data_tabel_bps)): ?>
                            <?php $no = 1; foreach ($data_tabel_bps as $row): ?>
                            <tr class="hover:bg-emerald-50/50 transition-all group">
                                <td class="p-4 text-slate-400 font-bold text-xs text-center rounded-l-2xl border-y border-l border-slate-50 italic">
                                    <?= $no++ ?>
                                </td>
                                <td class="p-4 font-bold text-slate-700 border-y border-slate-50 text-sm md:text-base">
                                    <?= htmlspecialchars($row['provinsi']) ?>
                                </td>
                                <td class="p-4 text-center rounded-r-2xl border-y border-r border-slate-50">
                                    <span class="inline-block bg-emerald-100 text-emerald-700 px-4 py-1.5 rounded-xl font-black text-xs md:text-sm shadow-sm">
                                        <?= $row['nilai'] ?>%
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="p-20 text-center text-slate-400 italic">
                                    <i class="fas fa-exclamation-triangle text-3xl mb-3 block"></i>
                                    Gagal memuat data. Silakan coba lagi nanti.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-between items-center px-8">
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">Source: webapi.bps.go.id</p>
                <p class="text-[9px] text-slate-300 font-medium italic">Data Otomatis Terupdate</p>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Logika untuk mematikan scroll body saat modal aktif
     */
    function openBpsModal() {
        const modal = document.getElementById('bpsModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeBpsModal() {
        const modal = document.getElementById('bpsModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Menutup modal jika user mengklik area backdrop (di luar kotak modal)
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('bpsModal');
        if (e.target === modal) {
            closeBpsModal();
        }
    });
</script>

<style>
    /* Styling Scrollbar Khusus Modal */
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>