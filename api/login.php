<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'pasien') {
        header("Location: dashboard_pasien.php");
        exit();
    } else {
        header("Location: dashboard_petugas.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem - Klinik Sehat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(13, 110, 253, 0.8), rgba(13, 110, 253, 0.8)), 
                        url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card-login { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card card-login p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-primary">KLINIK SEHAT</h3>
                    <p class="text-muted small">Silakan masuk ke akun Anda</p>
                </div>
                
                <form action="cek_login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="********" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">MASUK</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted">Belum punya akun? <a href="register.php" class="text-decoration-none">Daftar</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>