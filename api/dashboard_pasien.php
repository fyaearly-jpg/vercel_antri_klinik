<?php
session_start();

// Validasi menggunakan terminologi standar sistem
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User - Klinik Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
    <nav class="bg-emerald-600 p-4 text-white flex justify-between shadow-md">
        <h1 class="font-bold tracking-wide">Klinik Sehat</h1>
        <div class="flex gap-4 items-center">
            <span class="font-medium">Halo, <?= $_SESSION['nama'] ?? 'User'; ?></span>
            <a href="logout.php" class="text-sm bg-emerald-700 hover:bg-emerald-800 px-3 py-1 rounded transition-colors">Keluar</a>
        </div>
    </nav>

    <div class="p-8 max-w-5xl mx-auto">
        <h2 class="text-2xl font-bold mb-6 text-slate-800">Selamat Datang di Layanan Digital</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col h-full">
                <h3 class="font-bold text-lg mb-2 text-slate-800">Ambil Antrean</h3>
                <p class="text-slate-500 mb-4 text-sm flex-grow">Dapatkan nomor antrean pemeriksaan hari ini secara online.</p>
                <button class="bg-emerald-600 text-white px-4 py-3 rounded-xl w-full font-bold hover:bg-emerald-700 transition-colors shadow-md">Ambil Nomor</button>
            </div>
            
            <div class="bg-emerald-50 border-2 border-emerald-400 p-6 rounded-2xl shadow-md text-center transform transition-all hover:scale-105 flex flex-col h-full justify-center">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <div class="w-3 h-3 bg-red-500 rounded-full animate-ping"></div>
                    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wide">Sedang Dilayani Saat Ini</h3>
                </div>
                <h1 id="nomor-aktif-sekarang" class="text-6xl font-black text-emerald-600 my-2 tracking-tighter drop-shadow-sm">--</h1>
                <p class="text-sm text-slate-500">Poli Tujuan: <span id="poli-aktif-sekarang" class="font-bold text-slate-700">Memuat...</span></p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col h-full">
                <h3 class="font-bold text-lg mb-2 text-slate-800">Kirim Feedback</h3>
                <p class="text-slate-500 mb-4 text-sm">Gimana pengalaman kamu hari ini?</p>
                
                <form action="proses_feedback.php" method="POST" class="space-y-3 flex-grow flex flex-col justify-end">
                    <div>
                        <label class="block mb-1 font-semibold text-slate-700 text-xs uppercase">Tingkat Kepuasan</label>
                        <select name="kepuasan" class="w-full p-2.5 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:ring-2 focus:ring-emerald-400 transition-all text-sm">
                            <option value="Sangat Puas">Sangat Puas 😍</option>
                            <option value="Puas">Puas 🙂</option>
                            <option value="Cukup">Cukup 😐</option>
                            <option value="Kurang">Kurang 🙁</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-semibold text-slate-700 text-xs uppercase">Saran</label>
                        <textarea name="saran" rows="2" class="w-full p-2.5 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:ring-2 focus:ring-emerald-400 transition-all text-sm" placeholder="Tulis saran..."></textarea>
                    </div>
                    <button type="submit" class="bg-slate-800 text-white px-4 py-2.5 rounded-xl w-full font-bold hover:bg-slate-900 transition-colors shadow-md mt-2">
                        KIRIM SARAN
                    </button>
                </form>
            </div>
            
        </div>
    </div>

    <script>
        function pantauAntreanRealTime() {
            fetch('cek_status_antrean.php')
                .then(response => response.json())
                .then(data => {
                    if(data && data.nomor_antrean) {
                        document.getElementById('nomor-aktif-sekarang').innerText = data.nomor_antrean;
                        document.getElementById('poli-aktif-sekarang').innerText = data.poli;
                    } else {
                        document.getElementById('nomor-aktif-sekarang').innerText = "KOSONG";
                        document.getElementById('poli-aktif-sekarang').innerText = "Belum ada antrean berjalan";
                    }
                })
                .catch(error => console.error('Gagal mengambil data real-time:', error));
        }

        pantauAntreanRealTime();
        setInterval(pantauAntreanRealTime, 3000);
    </script>
</body>
</html>