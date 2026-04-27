<?php
// api/dashboard_pasien.php
include 'koneksi.php';

// 1. Ambil data dari Cookie
$cookie_data = isset($_COOKIE['user_session']) ? json_decode(base64_decode($_COOKIE['user_session']), true) : null;

// 2. Jika tidak ada cookie, tendang ke login
if (!$cookie_data || $cookie_data['role'] !== 'pasien') {
    header("Location: /login");
    exit();
}

$id_user = $cookie_data['id'];
$nama_user = $cookie_data['nama'];

// 3. Ambil data antrean terbaru pasien ini
$query = mysqli_query($koneksi, "SELECT * FROM antrian WHERE id_pasien = '$id_user' ORDER BY id DESC LIMIT 1");
$data_antrian = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Klinik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f3f4f6; font-family: 'Arial', sans-serif; }
        .container-custom { display: flex; flex-direction: column; align-items: center; padding-top: 50px; padding-bottom: 50px; }
        .card { background: white; width: 350px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 20px; }
        .content-wrapper { padding: 20px; text-align: center; }
        .nomor-besar { font-size: 64px; color: #059669; margin: 10px 0; font-weight: bold; }
    </style>
</head>
<body>

    <div class="container-custom">
        <div class="card">
            <div class="content-wrapper">
                
                <?php if (!isset($_SESSION['punya_antrean'])) : ?>
                    <h2 class="text-xl font-bold text-emerald-600 mb-4">Pendaftaran</h2>
                    <p class="mb-4">Halo <strong><?php echo htmlspecialchars($nama_pasien); ?></strong></p>
                    <form method="POST">
                        <select name="poli" required class="w-full p-3 mb-4 border rounded-xl bg-slate-50 outline-none">
                            <option value="">Pilih Poli</option>
                            <option value="Umum">Poli Umum</option>
                            <option value="Gigi">Poli Gigi</option>
                            <option value="Anak">Poli Anak</option>
                        </select>
                        <button type="submit" name="ambil_antrean" class="w-full bg-emerald-600 text-white py-3 rounded-xl font-bold hover:bg-emerald-700 transition-all">
                            Ambil Antrean
                        </button>
                    </form>

                <?php else : ?>
                    <h2 class="text-xl font-bold text-slate-800 mb-2">Antrean Anda</h2>
                    <div id="notif-panggil" class="hidden text-white p-2 rounded-lg mb-2 font-bold text-sm"></div>
                    <div class="nomor-besar">
                        <?php echo $_SESSION['punya_antrean']; ?>
                    </div>
                    <p class="text-slate-600">Poli: <strong><?php echo $_SESSION['poli_terpilih']; ?></strong></p>
                    <div class="bg-emerald-50 p-3 rounded-xl my-4 text-emerald-700">
                        Estimasi: <strong>15 Menit</strong>
                    </div>
                    
                    <button onclick="bukaModal()" class="w-full bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-black transition-all">
                        Selesai
                    </button>
                <?php endif; ?>

            </div>
        </div>

        <button onclick="openBpsModal()" class="flex items-center gap-3 bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-200 hover:bg-emerald-50 transition-all group">
            <i class="fas fa-chart-bar text-emerald-500 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm font-bold text-slate-700">Lihat Info Kesehatan Nasional</span>
        </button>
    </div>

    <div class="space-y-6">
                <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl shadow-lg p-6 text-white text-center">
                    <p class="text-sm opacity-80 mb-1 font-bold italic uppercase tracking-wider">Panggilan Terkini</p>
                    <h4 id="no-terbaru" class="text-5xl font-black mb-2">--</h4>
                    <p id="poli-terbaru" class="text-xs bg-white/20 inline-block px-4 py-1 rounded-full backdrop-blur-sm">Menghubungkan...</p>
                </div>

    <div id="modal-feedback" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[999]">
        <div class="bg-white p-8 rounded-3xl max-w-md w-full m-4 shadow-2xl">
            <h2 class="text-xl font-bold mb-4 text-slate-800">Selesai Berobat?</h2>
            <form action="logout.php" method="POST">
                <div class="mb-4 text-left">
                    <label class="block mb-2 font-bold text-slate-700">Tingkat Kepuasan</label>
                    <select name="kepuasan" required class="w-full p-3 border rounded-xl bg-slate-50">
                        <option value="Sangat Puas">Sangat Puas 😍</option>
                        <option value="Puas">Puas 🙂</option>
                        <option value="Cukup">Cukup 😐</option>
                        <option value="Kurang">Kurang 🙁</option>
                    </select>
                </div>
                <div class="mb-6 text-left">
                    <label class="block mb-2 font-bold text-slate-700">Saran</label>
                    <textarea name="saran" class="w-full p-3 border rounded-xl bg-slate-50" rows="3" placeholder="Masukkan saran anda..."></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="tutupModal()" class="flex-1 bg-slate-100 py-3 rounded-xl font-bold text-slate-600">Batal</button>
                    <button type="submit" name="kirim_feedback" class="flex-1 bg-emerald-500 text-white py-3 rounded-xl font-bold hover:bg-emerald-600">Kirim & Selesai</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // JS MODAL FEEDBACK
    function bukaModal() {
        const modal = document.getElementById('modal-feedback');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function tutupModal() {
        const modal = document.getElementById('modal-feedback');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    window.speechSynthesis.getVoices();
    window.speechSynthesis.onvoiceschanged = () => window.speechSynthesis.getVoices();

    function getVoiceWanita() {
        const voices = window.speechSynthesis.getVoices();
        let v = voices.find(v => v.lang === 'id-ID' && v.name.toLowerCase().includes('google'));
        if (!v) v = voices.find(v => v.lang === 'id-ID');
        return v || null;
    }

    function ejaNomorAntrean(nomor) {
        const bagian = nomor.split('-');
        if (bagian.length !== 2) return nomor;
        const huruf = bagian[0].toUpperCase();
        const angka = parseInt(bagian[1], 10);
        const namaHuruf = { 'A': 'A', 'B': 'Be', 'C': 'Ce', 'D': 'De', 'E': 'E', 'F': 'Ef', 'G': 'Ge', 'H': 'Ha', 'I': 'I', 'J': 'Je', 'K': 'Ka', 'L': 'El', 'M': 'Em', 'N': 'En', 'O': 'O', 'P': 'Pe', 'Q': 'Qi', 'R': 'Er', 'S': 'Es', 'T': 'Te', 'U': 'U', 'V': 'Fe', 'W': 'We', 'X': 'Eks', 'Y': 'Ye', 'Z': 'Zet' };
        return `${namaHuruf[huruf] || huruf} ${angka}`;
    }

    function ucapkan(teks, onSelesai) {
        const ssu = new SpeechSynthesisUtterance(teks);
        ssu.lang = 'id-ID';
        ssu.rate = 0.85;
        ssu.pitch = 1.3;
        const voice = getVoiceWanita();
        if (voice) ssu.voice = voice;
        if (onSelesai) ssu.onend = onSelesai;
        window.speechSynthesis.speak(ssu);
    }

    function panggilSuara(nomor, poli) {
        window.speechSynthesis.cancel();
        const nomorEja = ejaNomorAntrean(nomor);
        const teks = `Nomor antrean ${nomorEja}, silakan masuk ke Poli ${poli}.`;
        ucapkan(teks, () => {
            setTimeout(() => ucapkan(teks, null), 1200);
        });
    }

    let statusTerakhir = 'menunggu';
    const nomorAntrean = "<?php echo $_SESSION['punya_antrean'] ?? ''; ?>";
    const poliAntrean = "<?php echo $_SESSION['poli_terpilih'] ?? ''; ?>";

    function cekStatus() {
        if (!nomorAntrean) return;
        fetch(`cek_status_pasien.php?nomor=${nomorAntrean}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'dipanggil' && statusTerakhir !== 'dipanggil') {
                    panggilSuara(nomorAntrean, poliAntrean);
                    statusTerakhir = 'dipanggil';
                    const notif = document.getElementById('notif-panggil');
                    if (notif) {
                        notif.innerText = "SILAKAN MASUK KE POLI!";
                        notif.classList.remove('hidden');
                        notif.classList.add('bg-emerald-600', 'animate-bounce');
                    }
                }
            })
            .catch(err => console.error("Error:", err));
    }

    if (nomorAntrean) {
        setInterval(cekStatus, 3000);
    }
    </script>

    <?php include 'tabel_bps.php'; ?>
</body>
</html>