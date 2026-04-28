<?php
// Gunakan function_exists agar tidak menyebabkan Fatal Error jika file dipanggil berkali-kali
if (!function_exists('fetchBpsData')) {
    function fetchBpsData() {
        // API URL untuk data JKN
        $url = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/2279/th/125/key/7788dbf0dae1b2bdeb03cdb04fbc23e8/";
        
        // Menggunakan stream context untuk timeout
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: PHP\r\n", // Tambahkan header agar tidak diblokir server BPS
                "timeout" => 5
            ]
        ];
        
        $context = stream_context_create($opts);
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
            foreach ($content as $key => $value) {
                if (strpos($key, $id) !== false) {
                    $val = $value;
                    break;
                }
            }
            
            // Hanya ambil data jika label (provinsi) tidak kosong
            if (!empty($label)) {
                $finalData[] = [
                    'provinsi' => $label,
                    'nilai' => $val
                ];
            }
        }
        return $finalData;
    }
}

// Panggil fungsi dengan aman
$data_tabel_bps = fetchBpsData();
?>

<!-- Modal Statistik JKN BPS -->
<div id="bpsModal" class="fixed inset-0 z-[1000] hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden relative">
            
            <div class="p-6 md:p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight uppercase">Statistik JKN Nasional</h2>
                    <p class="text-emerald-600 text-[10px] md:text-xs font-bold uppercase tracking-[0.2em] mt-1">
                        <i class="fas fa-sync-alt fa-spin mr-1" id="bpsSpinIcon"></i> Live Data via BPS API
                    </p>
                </div>
                <button onclick="closeBpsModal()" class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-2xl bg-white shadow-sm border border-slate-100 text-slate-400 hover:text-red-500 transition-all">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
 
            <div class="p-4 md:p-8 overflow-y-auto max-h-[65vh]">
                <!-- Loading state -->
                <div id="bpsLoading" class="flex flex-col items-center justify-center py-20 text-slate-400">
                    <i class="fas fa-circle-notch fa-spin text-4xl text-emerald-400 mb-4"></i>
                    <p class="font-bold text-sm">Memuat data dari BPS...</p>
                </div>
 
                <!-- Error state -->
                <div id="bpsError" class="hidden flex flex-col items-center justify-center py-20 text-slate-400">
                    <i class="fas fa-exclamation-triangle text-3xl mb-3 block text-amber-400"></i>
                    <p class="font-bold text-sm mb-3">Gagal memuat data BPS.</p>
                    <button onclick="loadBpsData()" class="bg-emerald-500 text-white text-xs font-bold px-5 py-2 rounded-xl hover:bg-emerald-600 transition-all">
                        <i class="fas fa-redo mr-1"></i> Coba Lagi
                    </button>
                </div>
 
                <!-- Tabel data -->
                <table id="bpsTable" class="w-full text-left border-separate border-spacing-y-2 hidden">
                    <thead class="sticky top-0 z-10 bg-white">
                        <tr class="bg-slate-800 text-white">
                            <th class="p-4 rounded-l-2xl font-bold uppercase text-[10px] text-center w-16">No</th>
                            <th class="p-4 font-bold uppercase text-[10px] text-center">Provinsi / Wilayah</th>
                            <th class="p-4 rounded-r-2xl font-bold uppercase text-[10px] text-center">Persentase (%)</th>
                        </tr>
                    </thead>
                    <tbody id="bpsTbody" class="bg-white">
                        <!-- Diisi oleh JavaScript -->
                    </tbody>
                </table>
            </div>
 
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-between items-center px-8">
                <p class="text-[9px] text-slate-400 font-bold uppercase">Source: BPS Indonesia</p>
                <p id="bpsUpdateTime" class="text-[9px] text-slate-300 italic">-</p>
            </div>
        </div>
    </div>
</div>
 
<script>
// ✅ Fetch BPS dari JavaScript (client-side) agar tidak diblokir Vercel
const BPS_URL = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/2279/th/125/key/7788dbf0dae1b2bdeb03cdb04fbc23e8/";
 
let bpsLoaded = false; // Cache agar tidak fetch ulang setiap buka modal
 
function openBpsModal() {
    document.getElementById('bpsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if (!bpsLoaded) loadBpsData();
}
 
function closeBpsModal() {
    document.getElementById('bpsModal').classList.add('hidden');
    document.body.style.overflow = '';
}
 
// Tutup modal jika klik backdrop
document.getElementById('bpsModal').addEventListener('click', function(e) {
    if (e.target === this) closeBpsModal();
});
 
async function loadBpsData() {
    // Tampilkan loading, sembunyikan error & tabel
    document.getElementById('bpsLoading').classList.remove('hidden');
    document.getElementById('bpsError').classList.add('hidden');
    document.getElementById('bpsTable').classList.add('hidden');
 
    try {
        const res = await fetch(BPS_URL, { signal: AbortSignal.timeout(8000) });
        if (!res.ok) throw new Error('HTTP error ' + res.status);
 
        const data = await res.json();
 
        if (data.status !== 'OK') throw new Error('BPS API status tidak OK');
 
        const vervar     = data.vervar     ?? [];
        const datacontent = data.datacontent ?? {};
 
        const tbody = document.getElementById('bpsTbody');
        tbody.innerHTML = '';
 
        let no = 1;
        vervar.forEach(v => {
            const id    = String(v.val);
            const label = v.label;
            if (!label) return;
 
            // Cari nilai di datacontent yang key-nya mengandung id wilayah
            let nilai = '0';
            for (const [key, val] of Object.entries(datacontent)) {
                if (key.includes(id)) { nilai = val; break; }
            }
 
            tbody.innerHTML += `
                <tr class="hover:bg-emerald-50/50 transition-all">
                    <td class="p-4 text-slate-400 font-bold text-xs text-center rounded-l-2xl border-y border-l border-slate-50">
                        ${no++}
                    </td>
                    <td class="p-4 font-bold text-slate-700 border-y border-slate-50 text-sm md:text-base">
                        ${label}
                    </td>
                    <td class="p-4 text-center rounded-r-2xl border-y border-r border-slate-50">
                        <span class="inline-block bg-emerald-100 text-emerald-700 px-4 py-1.5 rounded-xl font-black text-xs">
                            ${nilai}%
                        </span>
                    </td>
                </tr>`;
        });
 
        // Tampilkan tabel, sembunyikan loading
        document.getElementById('bpsLoading').classList.add('hidden');
        document.getElementById('bpsTable').classList.remove('hidden');
        document.getElementById('bpsSpinIcon').classList.remove('fa-spin');
        document.getElementById('bpsUpdateTime').textContent = 'Diperbarui: ' + new Date().toLocaleTimeString('id-ID');
        bpsLoaded = true;
 
    } catch (err) {
        console.error('BPS fetch error:', err);
        document.getElementById('bpsLoading').classList.add('hidden');
        document.getElementById('bpsError').classList.remove('hidden');
    }
}
</script>