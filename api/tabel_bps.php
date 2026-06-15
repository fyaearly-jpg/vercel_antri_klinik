<?php /* tabel_bps.php — data diambil via JavaScript (client-side fetch) */ ?>
 
<div id="bpsModal" class="fixed inset-0 z-[1000] hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
 
            <!-- Header -->
            <div class="p-6 md:p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight uppercase">
                        Statistik JKN Nasional
                    </h2>
                    <p class="text-emerald-600 text-[10px] md:text-xs font-bold uppercase tracking-[0.2em] mt-1">
                        <i class="fas fa-sync-alt fa-spin mr-1" id="bpsSpinIcon"></i> Live Data via BPS API
                    </p>
                </div>
                <button onclick="closeBpsModal()"
                    class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-2xl bg-white shadow-sm border border-slate-100 text-slate-400 hover:text-red-500 transition-all">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
 
            <!-- Body -->
            <div class="p-4 md:p-8 overflow-y-auto max-h-[65vh]">
 
                <!-- Loading -->
                <div id="bpsLoading" class="flex flex-col items-center justify-center py-20 text-slate-400">
                    <i class="fas fa-circle-notch fa-spin text-4xl text-emerald-400 mb-4"></i>
                    <p class="font-bold text-sm">Memuat data dari BPS...</p>
                </div>
 
                <!-- Error -->
                <div id="bpsError" class="hidden flex-col items-center justify-center py-20 text-slate-400 text-center">
                    <i class="fas fa-exclamation-triangle text-3xl mb-3 text-amber-400"></i>
                    <p class="font-bold text-sm mt-3 mb-4">Gagal memuat data BPS.</p>
                    <button onclick="loadBpsData()"
                        class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold px-5 py-2 rounded-xl transition-all">
                        <i class="fas fa-redo mr-1"></i> Coba Lagi
                    </button>
                </div>
 
                <!-- Tabel -->
                <table id="bpsTable" class="w-full text-left border-separate border-spacing-y-2 hidden">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-slate-800 text-white">
                            <th class="p-4 rounded-l-2xl font-bold uppercase text-[10px] text-center w-16">No</th>
                            <th class="p-4 font-bold uppercase text-[10px]">Provinsi / Wilayah</th>
                            <th class="p-4 rounded-r-2xl font-bold uppercase text-[10px] text-center">Persentase (%)</th>
                        </tr>
                    </thead>
                    <tbody id="bpsTbody"></tbody>
                </table>
 
            </div>
 
            <!-- Footer -->
            <div class="p-4 px-8 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                <p class="text-[9px] text-slate-400 font-bold uppercase">Source: BPS Indonesia</p>
                <p id="bpsUpdateTime" class="text-[9px] text-slate-300 italic">-</p>
            </div>
 
        </div>
    </div>
</div>
 
<script>
const BPS_URL = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/2279/th/125/key/7788dbf0dae1b2bdeb03cdb04fbc23e8/";
let bpsLoaded = false;
 
function openBpsModal() {
    document.getElementById('bpsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if (!bpsLoaded) loadBpsData();
}
 
function closeBpsModal() {
    document.getElementById('bpsModal').classList.add('hidden');
    document.body.style.overflow = '';
}
 
document.getElementById('bpsModal').addEventListener('click', function(e) {
    if (e.target === this) closeBpsModal();
});
 
async function loadBpsData() {
    document.getElementById('bpsLoading').classList.remove('hidden');
    document.getElementById('bpsError').classList.replace('flex', 'hidden');
    document.getElementById('bpsTable').classList.add('hidden');
 
    try {
        const res = await fetch(BPS_URL, { signal: AbortSignal.timeout(8000) });
        if (!res.ok) throw new Error('HTTP ' + res.status);
 
        const data = await res.json();
        if (data.status !== 'OK') throw new Error('BPS status tidak OK');
 
        const vervar      = data.vervar      ?? [];
        const datacontent = data.datacontent ?? {};
        const tbody       = document.getElementById('bpsTbody');
        tbody.innerHTML   = '';
 
        let no = 1;
        vervar.forEach(v => {
            const id    = String(v.val);
            const label = v.label;
            if (!label) return;
 
            let nilai = '0';
            for (const [key, val] of Object.entries(datacontent)) {
                if (key.includes(id)) { nilai = val; break; }
            }
 
            tbody.innerHTML += `
                <tr class="hover:bg-emerald-50/50 transition-all">
                    <td class="p-4 text-slate-400 font-bold text-xs text-center rounded-l-2xl border-y border-l border-slate-100">${no++}</td>
                    <td class="p-4 font-bold text-slate-700 border-y border-slate-100 text-sm">${label}</td>
                    <td class="p-4 text-center rounded-r-2xl border-y border-r border-slate-100">
                        <span class="inline-block bg-emerald-100 text-emerald-700 px-4 py-1.5 rounded-xl font-black text-xs">${nilai}%</span>
                    </td>
                </tr>`;
        });
 
        document.getElementById('bpsLoading').classList.add('hidden');
        document.getElementById('bpsTable').classList.remove('hidden');
        document.getElementById('bpsSpinIcon').classList.remove('fa-spin');
        document.getElementById('bpsUpdateTime').textContent = 'Diperbarui: ' + new Date().toLocaleTimeString('id-ID');
        bpsLoaded = true;
 
    } catch (err) {
        console.error('BPS fetch error:', err);
        document.getElementById('bpsLoading').classList.add('hidden');
        document.getElementById('bpsError').classList.replace('hidden', 'flex');
    }
}
</script>