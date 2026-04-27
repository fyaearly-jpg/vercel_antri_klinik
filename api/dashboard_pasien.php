<?php
session_start();
// Proteksi halaman: kalau bukan pasien, tendang ke login
if ($_SESSION['role'] !== 'pasien') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pasien - Klinik Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
    <nav class="bg-emerald-600 p-4 text-white flex justify-between">
        <h1 class="font-bold">Klinik Sehat</h1>
        <div class="flex gap-4">
            <span>Halo, <?= $_SESSION['nama_pasien']; ?></span>
            <a href="logout.php" class="underline">Keluar</a>
        </div>
    </nav>

    <div class="p-8 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">Selamat Datang di Layanan Digital</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h3 class="font-bold text-lg mb-2">Ambil Antrean</h3>
                <p class="text-slate-500 mb-4">Dapatkan nomor antrean pemeriksaan hari ini secara online.</p>
                <button class="bg-emerald-600 text-white px-4 py-2 rounded-lg w-full">Ambil Nomor</button>
            </div>

           <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
    <h3 class="font-bold text-lg mb-2">Kirim Feedback</h3>
    <p class="text-slate-500 mb-4 text-sm">Gimana pengalaman kamu hari ini?</p>
    
    <form action="proses_feedback.php" method="POST" class="space-y-3">
        <div>
            <label class="block mb-1 font-semibold text-slate-700 text-xs uppercase">Tingkat Kepuasan</label>
            <select name="kepuasan" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:ring-2 focus:ring-emerald-400 transition-all">
                <option value="Sangat Puas">Sangat Puas 😍</option>
                <option value="Puas">Puas 🙂</option>
                <option value="Cukup">Cukup 😐</option>
                <option value="Kurang">Kurang 🙁</option>
            </select>
        </div>
        
        <div>
            <label class="block mb-1 font-semibold text-slate-700 text-xs uppercase">Saran</label>
            <textarea name="saran" rows="2" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:ring-2 focus:ring-emerald-400 transition-all" placeholder="Tulis saran kamu di sini..."></textarea>
        </div>

        <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg w-full font-bold hover:bg-slate-700 transition-colors">
            KIRIM SARAN
        </button>
        </form>
        </div>
    </div>
</body>
</html>