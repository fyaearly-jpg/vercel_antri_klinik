<?php 

function fetchBpsData() {
    $url = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/2279/th/125/key/7788dbf0dae1b2bdeb03cdb04fbc23e8/";
    
    $response = @file_get_contents($url);
    if ($response === FALSE) return [];

    $data = json_decode($response, true);
    if (!isset($data['status']) || $data['status'] !== 'OK') return [];

    $vervar = $data['vervar']; 
    $content = $data['datacontent']; 

    $finalData = [];
    foreach ($vervar as $v) {
        $id = $v['val'];
        $label = $v['label'];
        
        $val = "0";
        // BPS API Key matching logic
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antri Klinik Digital | Solusi Kesehatan Modern</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; scroll-behavior: smooth; }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .bg-gradient-hero { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #10b981; border-radius: 10px; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <header class="bg-gradient-hero min-h-[75vh] flex items-center justify-center p-6 rounded-b-[60px] shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
            <i class="fas fa-plus-circle text-[20rem] absolute -top-20 -left-20"></i>
        </div>

        <div class="text-center text-white max-w-4xl z-10"> 
            <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight">
                Antri Klinik Digital <br><span class="text-emerald-200">Tanpa Harus Nunggu Lama</span>
            </h1>
            <p class="text-lg md:text-2xl text-emerald-50 mb-10 opacity-90 max-w-2xl mx-auto font-light leading-relaxed">
                Solusi cerdas untuk mengelola antrean pasien secara real-time. Fokus pada kesembuhan, bukan menunggu di ruang tunggu.
            </p>
            <div class="flex flex-col md:flex-row gap-6 justify-center">
                <a href="login.php" class="bg-white text-emerald-700 px-10 py-5 rounded-2xl font-bold text-xl hover:bg-emerald-50 transition-all shadow-xl hover:-translate-y-1 flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-3"></i> Masuk Ke Sistem
                </a>
                <a href="register.php" class="bg-emerald-800/30 border-2 border-white/50 text-white px-10 py-5 rounded-2xl font-bold text-xl hover:bg-emerald-800/50 transition-all shadow-xl hover:-translate-y-1 flex items-center justify-center">
                    <i class="fas fa-user-plus mr-3"></i> Daftar Akun
                </a>
            </div>
        </div>
    </header>

    <section class="container mx-auto px-6 -mt-24 relative z-20 mb-20"> 
        <div class="grid md:grid-cols-3 gap-8">
            <div class="glass p-10 rounded-[40px] shadow-2xl border border-emerald-100 group hover:bg-emerald-600 transition-all duration-300">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-white group-hover:text-emerald-600 transition-all shadow-inner">
                    <i class="fas fa-clock text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 group-hover:text-white transition-colors">Efisiensi Waktu</h3>
                <p class="text-gray-500 leading-relaxed text-lg group-hover:text-emerald-50 transition-colors">
                    Pasien bisa mendaftar dari rumah dan datang tepat saat nomornya hampir dipanggil.
                </p>
            </div>

            <div class="glass p-10 rounded-[40px] shadow-2xl border border-emerald-100 group hover:bg-emerald-600 transition-all duration-300">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-white group-hover:text-emerald-600 transition-all shadow-inner">
                    <i class="fas fa-desktop text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 group-hover:text-white transition-colors">Monitoring Real-time</h3>
                <p class="text-gray-500 leading-relaxed text-lg group-hover:text-emerald-50 transition-colors">
                    Monitor antrean aktif secara langsung melalui layar display TV dan dashboard interaktif.
                </p>
            </div>

            <div class="glass p-10 rounded-[40px] shadow-2xl border border-blue-100 group hover:bg-blue-600 transition-all duration-300 cursor-pointer" onclick="openBpsModal()">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-white group-hover:text-blue-600 transition-all shadow-inner">
                    <i class="fas fa-chart-bar text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 group-hover:text-white transition-colors">Tahukah Anda?</h3>
                <p class="text-gray-500 leading-relaxed text-lg mb-4 group-hover:text-blue-50 transition-colors">
                    Persentase Penduduk yang Memiliki Jaminan Kesehatan Nasional (JKN) Menurut Provinsi Tahun 2025.
                </p>
                <span class="text-blue-500 font-black group-hover:text-white transition-colors uppercase tracking-widest text-xs">Klik untuk Detail &rarr;</span>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-6 mb-32">
        <div class="bg-white p-12 md:p-20 rounded-[60px] shadow-sm border border-gray-100 text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-8">Tentang E-Klinik Digital</h2>
            <p class="text-xl text-gray-600 leading-loose mb-12 max-w-3xl mx-auto">
                Sistem manajemen antrean terintegrasi yang dirancang untuk mempercepat alur pelayanan di fasilitas kesehatan modern.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                <div class="flex items-start gap-4 p-6 bg-emerald-50 rounded-3xl border border-emerald-100">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm"><i class="fas fa-user-shield"></i></div>
                    <div><p class="font-bold text-emerald-900">Admin</p><p class="text-sm text-emerald-600">Kelola master data sistem.</p></div>
                </div>
                <div class="flex items-start gap-4 p-6 bg-emerald-50 rounded-3xl border border-emerald-100">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm"><i class="fas fa-user-md"></i></div>
                    <div><p class="font-bold text-emerald-900">Petugas Medis</p><p class="text-sm text-emerald-600">Operasional panggil antrean.</p></div>
                </div>
                <div class="flex items-start gap-4 p-6 bg-emerald-50 rounded-3xl border border-emerald-100">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm"><i class="fas fa-users"></i></div>
                    <div><p class="font-bold text-emerald-900">Pasien</p><p class="text-sm text-emerald-600">Registrasi mandiri dari mana saja.</p></div>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center py-12 text-gray-400 bg-white border-t border-gray-50 font-medium">
        &copy; 2026 Antri Klinik Digital &bull; UNS Informatics Project
    </footer>

    <div id="bpsModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/60 backdrop-blur-sm transition-all">
        <div class="flex items-center justify-center min-h-screen px-4 py-10">
            <div class="bg-white w-full max-w-5xl rounded-[3rem] shadow-2xl overflow-hidden animate-fade-in-up relative">
                
                <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Data Live BPS</h2>
                        <p class="text-slate-500 text-sm">Monitoring Data Statistik Berdasarkan Wilayah Indonesia</p>
                    </div>
                    <button onclick="closeBpsModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl hover:bg-red-50 text-slate-400 hover:text-red-500 transition-all">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <div class="p-8 overflow-y-auto max-h-[60vh] custom-scrollbar">
                    <table class="w-full text-left border-separate border-spacing-y-2">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-emerald-600 text-white shadow-lg">
                                <th class="p-5 rounded-l-2xl font-bold uppercase text-xs tracking-widest">No</th>
                                <th class="p-5 font-bold uppercase text-xs tracking-widest">Provinsi / Wilayah</th>
                                <th class="p-5 rounded-r-2xl font-bold uppercase text-xs tracking-widest text-center">Nilai Statistik</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <?php if (!empty($data_tabel_bps)): ?>
                                <?php $no = 1; foreach ($data_tabel_bps as $row): ?>
                                <tr class="hover:bg-emerald-50 transition-all group">
                                    <td class="p-5 text-slate-400 font-medium text-sm rounded-l-2xl border-y border-l border-slate-100"><?= $no++ ?></td>
                                    <td class="p-5 font-bold text-slate-700 border-y border-slate-100"><?= $row['provinsi'] ?></td>
                                    <td class="p-5 text-center rounded-r-2xl border-y border-r border-slate-100">
                                        <span class="bg-emerald-100 text-emerald-700 px-6 py-2 rounded-xl font-black text-sm">
                                            <?= $row['nilai'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="p-20 text-center text-slate-400 italic">Gagal menyambungkan ke API BPS. Periksa koneksi atau API Key.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-between items-center px-10">
                    <p class="text-[10px] text-slate-400 font-bold uppercase italic">Source: webapi.bps.go.id</p>
                    <p class="text-[10px] text-emerald-600 font-bold uppercase">Data Updated Secara Real-time</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openBpsModal() {
            document.getElementById('bpsModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeBpsModal() {
            document.getElementById('bpsModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        window.onclick = (e) => { if(e.target == document.getElementById('bpsModal')) closeBpsModal(); }
    </script>
    
</body>
</html>