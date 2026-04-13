<?php
session_start();
// Proteksi: Jika tidak ada role atau rolenya pasien, tendang ke login
if (!isset($_SESSION['role']) || $_SESSION['role'] === 'pasien') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f1f5f9] min-h-screen p-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-10 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Selamat Datang, <?php echo $_SESSION['nama_lengkap']; ?>!</h1>
                <p class="text-slate-500">Role: <span class="uppercase font-bold"><?php echo $_SESSION['role']; ?></span></p>
            </div>
            <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-bold">LOGOUT</a>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <a href="MonitoringAntri.html" class="group bg-white p-8 rounded-[2rem] shadow-xl hover:ring-4 hover:ring-[#117a55] transition-all">
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6 text-[#117a55]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-800">Monitoring Antrian</h2>
                <p class="text-slate-500 mt-2">Panggil pasien dan kontrol nomor antrian secara live.</p>
                <span class="inline-block mt-4 text-[#117a55] font-semibold group-hover:translate-x-2 transition-transform">Buka Kontrol &rarr;</span>
            </a>

            <a href="DisplayAntri.html" class="group bg-white p-8 rounded-[2rem] shadow-xl hover:ring-4 hover:ring-blue-600 transition-all">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-800">Layar Display TV</h2>
                <p class="text-slate-500 mt-2">Halaman khusus untuk ditampilkan di TV Ruang Tunggu.</p>
                <span class="inline-block mt-4 text-blue-600 font-semibold group-hover:translate-x-2 transition-transform">Buka Display &rarr;</span>
            </a>
        </div>
    </div>
</body>
</html>