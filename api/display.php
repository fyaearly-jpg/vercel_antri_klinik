<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrean Klinik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #0f172a; }
        .neon-text { text-shadow: 0 0 10px rgba(16, 185, 129, 0.5), 0 0 20px rgba(16, 185, 129, 0.3); }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="min-h-screen overflow-hidden flex flex-col items-center justify-center p-6 text-white">

    <?php if(isset($_COOKIE['user_session'])): ?>
    <div class="fixed top-6 left-6 z-50">
        <a href="/dashboard_petugas" class="bg-white/10 hover:bg-white/20 backdrop-blur-xl text-white px-6 py-3 rounded-2xl border border-white/20 transition-all flex items-center gap-3 shadow-2xl group">
            <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            <span class="font-bold text-sm">Kembali ke Dashboard</span>
        </a>
    </div>
    <?php endif; ?>

    <div class="w-full max-w-6xl">
        <div class="text-center mb-12">
            <h1 class="text-2xl font-bold text-emerald-500 tracking-[0.3em] uppercase mb-2 neon-text">Status Panggilan Klinik</h1>
            <p class="text-slate-500 font-medium tracking-widest uppercase text-sm">Harap perhatikan nomor yang tertera di layar</p>
        </div>

        <div class="glass-card rounded-[4rem] p-12 relative shadow-inner overflow-hidden">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/10 rounded-full blur-[80px]"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-blue-500/10 rounded-full blur-[80px]"></div>

            <div class="relative z-10 text-center">
                <div id="display-poli" class="inline-block px-8 py-3 bg-emerald-500/10 text-emerald-400 rounded-2xl text-3xl font-bold uppercase tracking-widest border border-emerald-500/20 mb-8">
                    --
                </div>
                
                <div id="display-nomor" class="text-[22rem] leading-none font-black text-white tracking-tighter transition-all duration-700 select-none">
                    --
                </div>

                <div class="mt-8 flex justify-center items-center gap-4 text-slate-400">
                    <i class="fas fa-bullhorn animate-bounce text-emerald-500"></i>
                    <p class="text-xl font-medium tracking-wide">Silakan menuju ke ruang poli tersebut</p>
                </div>
            </div>
        </div>

        <div class="mt-12 flex justify-between items-center px-10">
            <div class="flex items-center gap-4">
                <div class="w-3 h-3 bg-emerald-500 rounded-full animate-ping"></div>
                <span id="digital-clock" class="text-2xl font-bold text-slate-400">00:00:00</span>
            </div>
            <div class="text-slate-600 font-bold uppercase tracking-[0.2em] text-sm">Sistem Antrean Digital v2.0</div>
        </div>
    </div>

    <script>
        let nomorTerakhir = "";

        // Fungsi Suara (TTS)
        function panggilAntrean(nomor, poli) {
            const teks = `Nomor antrean ${nomor}, silakan menuju ke ${poli}`;
            const utterance = new SpeechSynthesisUtterance(teks);
            utterance.lang = 'id-ID';
            utterance.rate = 0.85;
            utterance.pitch = 1;
            window.speechSynthesis.speak(utterance);
        }

        // Ambil Data dari API
        function updateDisplay() {
            fetch('/get_antrian_sekarang') 
                .then(res => res.json())
                .then(data => {
                    if (data.nomor_antrean !== "--" && data.nomor_antrean !== nomorTerakhir) {
                        const elNomor = document.getElementById('display-nomor');
                        
                        // Animasi saat angka berubah
                        elNomor.style.opacity = "0";
                        elNomor.style.transform = "scale(0.9)";
                        
                        setTimeout(() => {
                            elNomor.innerText = data.nomor_antrean;
                            document.getElementById('display-poli').innerText = data.poli;
                            elNomor.style.opacity = "1";
                            elNomor.style.transform = "scale(1)";
                            
                            panggilAntrean(data.nomor_antrean, data.poli);
                        }, 500);
                        
                        nomorTerakhir = data.nomor_antrean;
                    }
                })
                .catch(err => console.warn("Koneksi terputus..."));
        }

        // Jam Digital
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            document.getElementById('digital-clock').innerText = timeStr;
        }

        setInterval(updateDisplay, 3000);
        setInterval(updateClock, 1000);
        updateDisplay();
    </script>
</body>
</html>