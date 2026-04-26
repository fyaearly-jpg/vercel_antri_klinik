<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Display Antrean | Klinik Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .nomor-display { font-size: clamp(6rem, 25vw, 18rem); font-weight: 900; line-height: 1; }
        @keyframes flash { 0%,100%{opacity:1} 50%{opacity:0.5} }
        .flash { animation: flash 0.8s ease-in-out 3; }
    </style>
</head>
<body class="bg-slate-900 text-white h-screen overflow-hidden flex flex-col items-center justify-center">
 
    <h1 class="text-3xl md:text-4xl mb-10 text-slate-400 tracking-widest uppercase font-bold">
        Panggilan Antrean
    </h1>
 
    <div class="bg-white text-slate-900 rounded-[4rem] px-16 py-12 shadow-2xl shadow-emerald-500/20 border-b-8 border-emerald-500 text-center w-full max-w-2xl mx-4">
        <div id="display-poli" class="text-2xl md:text-3xl font-bold text-emerald-600 mb-4 uppercase tracking-wide">
            --
        </div>
        <div id="display-nomor" class="nomor-display text-slate-800 transition-all duration-500">
            --
        </div>
    </div>
 
    <p class="mt-16 text-slate-500 text-lg text-center px-4">
        Silakan menuju ke ruang poli yang tertera di atas.
    </p>
 
    <div class="absolute bottom-6 right-8 text-slate-700 text-xs font-bold uppercase tracking-widest">
        Klinik Sehat &bull; Live Display
    </div>
 
<script>
let nomorLama = "";
 
async function updateDisplay() {
    try {
        const res  = await fetch('/api/get_antrian_sekarang.php');
        const data = await res.json();
 
        const elNomor = document.getElementById('display-nomor');
        const elPoli  = document.getElementById('display-poli');
 
        if (data.nomor_antrian !== nomorLama) {
            elNomor.textContent = data.nomor_antrian;
            elPoli.textContent  = data.poli ? 'Poli ' + data.poli : data.poli;
 
            // Efek flash saat ganti nomor
            if (nomorLama !== "" && data.nomor_antrian !== '--') {
                elNomor.classList.add('flash', 'text-emerald-500');
                setTimeout(() => elNomor.classList.remove('flash', 'text-emerald-500'), 2500);
            }
 
            nomorLama = data.nomor_antrian;
        }
    } catch (e) {}
}
 
updateDisplay();
setInterval(updateDisplay, 2000);
</script>
 
</body>
</html>