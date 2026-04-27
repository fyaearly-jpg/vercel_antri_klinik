<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Display Antrean Klinik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-900 text-white h-screen overflow-hidden flex items-center justify-center">

    <div class="container max-w-5xl text-center">
        <h1 class="text-4xl mb-12 text-slate-400 tracking-widest uppercase font-bold">Panggilan Antrean Saat Ini</h1>
        
        <div class="bg-white text-slate-900 rounded-[4rem] p-16 shadow-2xl shadow-emerald-500/20 border-b-8 border-emerald-500 relative">
            <div id="display-poli" class="text-3xl font-bold text-emerald-600 mb-4 uppercase tracking-wide">
                --
            </div>
            
            <div id="display-nomor" class="text-[18rem] leading-none font-black text-slate-800 transition-all duration-500">
                --
            </div>
        </div>

        <div class="mt-16 text-slate-500 text-xl">
            <p>Silakan menuju ke ruang poli yang tertera di atas.</p>
        </div>
    </div>

    <script>
        let nomorLama = "";

        function updateDisplay() {
            fetch('get_antrean_sekarang.php')
                .then(response => response.json())
                .then(data => {
                    const elNomor = document.getElementById('display-nomor');
                    const elPoli = document.getElementById('display-poli');

                    // Jika ada perubahan nomor, beri efek animasi dikit
                    if (data.nomor_antrian !== nomorLama) {
                        elNomor.innerText = data.nomor_antrian;
                        elPoli.innerText = data.poli;
                        
                        // Efek kedip pas ganti nomor
                        elNomor.classList.add('scale-110', 'text-emerald-500');
                        setTimeout(() => {
                            elNomor.classList.remove('scale-110', 'text-emerald-500');
                        }, 1000);
                        
                        nomorLama = data.nomor_antrian;
                    }
                });
        }

        // Cek data setiap 2 detik
        setInterval(updateDisplay, 2000);
    </script>
</body>
</html>