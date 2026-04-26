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
                
                <form onsubmit="doLogin(event)">
                    <div class="mb-3">
                        <label class="form-label small">Email</label>
                        <input type="email" id="email" class="form-control" placeholder="nama@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Password</label>
                        <input type="password" id="password" class="form-control" placeholder="********" required>
                    </div>
                    <div id="alert-box" class="alert alert-danger d-none small"></div>
                    <div class="d-grid">
                        <button type="submit" id="btn-login" class="btn btn-primary py-2 fw-bold">MASUK</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted">Belum punya akun? <a href="register.php" class="text-decoration-none">Daftar</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function doLogin(e) {
        e.preventDefault();

        const btn = document.getElementById('btn-login');
        const alert = document.getElementById('alert-box');
        btn.disabled = true;
        btn.textContent = 'Memproses...';
        alert.classList.add('d-none');

        const formData = new FormData();
        formData.append('email', document.getElementById('email').value);
        formData.append('password', document.getElementById('password').value);

        try {
            // Mengarah ke file cek_login.php yang ada di folder yang sama (api/)
            const res = await fetch('cek_login.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                // Simpan ke localStorage
                localStorage.setItem('role', data.role);
                localStorage.setItem('nama', data.nama);
                if (data.id_pasien) localStorage.setItem('id_pasien', data.id_pasien);

                window.location.href = data.redirect;
            } else {
                alert.classList.remove('d-none');
                alert.textContent = data.message;
                btn.disabled = false;
                btn.textContent = 'MASUK';
            }
        } catch (err) {
            alert.classList.remove('d-none');
            alert.textContent = 'Terjadi kesalahan, coba lagi.';
            btn.disabled = false;
            btn.textContent = 'MASUK';
        }
    }
</script>

</body>
</html>