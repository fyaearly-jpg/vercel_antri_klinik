<?php
session_start();
include 'koneksi.php';

// Cek Sesi Login
if (!isset($_SESSION['pasien_login'])) {
    header("Location: index.php");
    exit();
}

$nama_pasien = $_SESSION['nama_pasien'];
$id_pasien = $_SESSION['id_pasien'];

// Logika Ambil Nomor Antrean
if (isset($_POST['ambil_antrean'])) {
    $poli = mysqli_real_escape_string($conn, $_POST['poli']);
    $hari_ini = date('Y-m-d');
    
    $query_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM antrian WHERE DATE(created_at) = '$hari_ini'");
    $data_count = mysqli_fetch_assoc($query_count);
    $nomor_urut = $data_count['total'] + 1;
    
    $prefix = strtoupper(substr($poli, 0, 1));
    $nomor_fix = $prefix . "-" . $nomor_urut;
    
    $query_insert = "INSERT INTO antrian (nomor_antrian, status, id_pasien, poli, created_at) 
                     VALUES ('$nomor_fix', 'menunggu', '$id_pasien', '$poli', NOW())";
    
    if (mysqli_query($conn, $query_insert)) {
        $_SESSION['punya_antrean'] = $nomor_fix;
        $_SESSION['poli_terpilih'] = $poli;
        echo "<script>alert('Nomor Antrean Anda: $nomor_fix'); window.location.href='dashboard_pasien.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Klinik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f3f4f6; font-family: 'Arial', sans-serif; }
        .container-custom { display: flex; flex-direction: column; align-items: center; padding-top: 50px; }
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

    // Preload voices segera
    window.speechSynthesis.getVoices();
    window.speechSynthesis.onvoiceschanged = () => window.speechSynthesis.getVoices();

    function getVoiceWanita() {
        const voices = window.speechSynthesis.getVoices();

        // Prioritas 1: Google Bahasa Indonesia
        let v = voices.find(v => v.lang === 'id-ID' && v.name.toLowerCase().includes('google'));
        if (v) return v;

        // Prioritas 2: Suara id-ID apapun
        v = voices.find(v => v.lang === 'id-ID');
        if (v) return v;

        // Prioritas 3: Suara wanita berbahasa Melayu (ms-MY) sebagai fallback
        v = voices.find(v => v.lang.startsWith('ms'));
        if (v) return v;

        // Prioritas 4: Suara wanita berbahasa Inggris (lebih jelas dari kosong)
        v = voices.find(v => v.lang.startsWith('en') && 
            (v.name.includes('Female') || v.name.includes('Samantha') || v.name.includes('Google US')));
        return v || null;
    }

    function ejaNomorAntrean(nomor) {
        // Ubah "U-3" → "U 3" → ucapkan sebagai kata wajar
        // Huruf depan dieja, angka dibaca sebagai angka
        const bagian = nomor.split('-');
        if (bagian.length !== 2) return nomor;

        const huruf = bagian[0].toUpperCase();
        const angka = parseInt(bagian[1], 10);

        const namaHuruf = {
            'A': 'A', 'B': 'Be', 'C': 'Ce', 'D': 'De', 'E': 'E',
            'F': 'Ef', 'G': 'Ge', 'H': 'Ha', 'I': 'I', 'J': 'Je',
            'K': 'Ka', 'L': 'El', 'M': 'Em', 'N': 'En', 'O': 'O',
            'P': 'Pe', 'Q': 'Qi', 'R': 'Er', 'S': 'Es', 'T': 'Te',
            'U': 'U', 'V': 'Fe', 'W': 'We', 'X': 'Eks', 'Y': 'Ye', 'Z': 'Zet'
        };

        const ejaHuruf = namaHuruf[huruf] || huruf;
        return `${ejaHuruf} ${angka}`;
    }

    function ucapkan(teks, onSelesai) {
        const ssu = new SpeechSynthesisUtterance(teks);
        ssu.lang = 'id-ID';
        ssu.rate = 0.85;
        ssu.pitch = 1.3;
        ssu.volume = 1.0;

        const voice = getVoiceWanita();
        if (voice) ssu.voice = voice;

        if (onSelesai) ssu.onend = onSelesai;

        window.speechSynthesis.speak(ssu);
    }

    function panggilSuara(nomor, poli) {
        window.speechSynthesis.cancel();

        const nomorEja = ejaNomorAntrean(nomor);
        const teks = `Nomor antrean ${nomorEja}, silakan masuk ke Poli ${poli}.`;

        // Panggilan pertama, lalu setelah selesai langsung panggil kedua
        ucapkan(teks, () => {
            setTimeout(() => ucapkan(teks, null), 1200);
        });
    }

    // LOGIKA CEK STATUS OTOMATIS
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
            .catch(err => console.error("Error cek status:", err));
    }

    if (nomorAntrean) {
        setInterval(cekStatus, 3000);
    }

    </script>
</body>
</html>