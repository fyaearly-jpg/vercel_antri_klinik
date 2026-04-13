<?php
// 1. Letakkan session_start di baris paling atas tanpa spasi sebelumnya
session_start();
include 'koneksi.php';

// Cek Sesi Login
if (!isset($_SESSION['pasien_login'])) {
    header("Location: index.php");
    exit();
}

$nama_pasien = $_SESSION['nama_pasien'];
$id_pasien = $_SESSION['id_pasien'];

// 2. Logika Ambil Nomor Antrean
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
        
        // Gunakan redirect script yang lebih kuat
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
    <link rel="stylesheet" href="Antri.css"> 
    <style>
        /* CSS Tambahan agar pasti kelihatan */
        body { background-color: #f3f4f6; font-family: Arial, sans-serif; }
        .container { display: flex; justify-content: center; padding-top: 50px; }
        .card { background: white; width: 350px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow: hidden; }
        .content-wrapper { padding: 20px; text-align: center; }
        .btn { padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; width: 100%; font-weight: bold; }
        .btn-tambah { background: #059669; color: white; }
        .btn-selesai { background: #ef4444; color: white; margin-top: 10px; }
        .nomor-besar { font-size: 64px; color: #059669; margin: 10px 0; font-weight: bold; }
        #modal-feedback { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center; }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <div class="content-wrapper">
                
                <?php if (!isset($_SESSION['punya_antrean'])) : ?>
                    <h2>Pendaftaran</h2>
                    <p>Halo <strong><?php echo htmlspecialchars($nama_pasien); ?></strong></p>
                    <form method="POST">
                        <select name="poli" required style="width:100%; padding:10px; margin-bottom:15px;">
                            <option value="">Pilih Poli</option>
                            <option value="Umum">Poli Umum</option>
                            <option value="Gigi">Poli Gigi</option>
                            <option value="Anak">Poli Anak</option>
                        </select>
                        <button type="submit" name="ambil_antrean" class="btn btn-tambah">Ambil Antrean</button>
                    </form>

                <?php else : ?>
                    <h2>Antrean Anda</h2>
                    <div class="nomor-besar">
                        <?php echo $_SESSION['punya_antrean']; ?>
                    </div>
                    <p>Poli: <strong><?php echo $_SESSION['poli_terpilih']; ?></strong></p>
                    <div style="background:#ecfdf5; padding:10px; border-radius:8px; margin:15px 0;">
                        Estimasi: 15 Menit
                    </div>
                    <button type="button" class="btn btn-selesai" onclick="bukaModal()">Selesai</button>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div id="modal-feedback">
        <div style="background:white; padding:20px; border-radius:15px; width:300px;">
            <h3>Feedback</h3>
            <form action="logout.php" method="POST">
                <select name="kepuasan" style="width:100%; margin-bottom:10px;">
                    <option>Puas</option>
                    <option>Cukup</option>
                </select>
                <button type="submit" name="kirim_feedback" class="btn btn-tambah">Kirim & Keluar</button>
            </form>
        </div>
    </div>

    <script>
        function bukaModal() { document.getElementById('modal-feedback').style.display = 'flex'; }
    </script>
</body>
</html>