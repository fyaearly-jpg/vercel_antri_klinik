<?php
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - Klinik Sehat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #198754; font-family: 'Poppins', sans-serif; }
        .card-register { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-register p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-success">Daftar Akun</h2>
                        <p class="text-muted">Lengkapi data untuk akses sistem</p>
                    </div>

                    <form action="proses_register.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama sesuai identitas" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Username unik" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="******" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Daftar Sebagai:</label>
                                <select name="role" class="form-select" required>
                                <option value="pasien">Pasien</option>
                                <option value="staff">Petugas (Staff)</option>
                                <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" name="daftar" class="btn btn-success p-2 fw-bold">DAFTAR SEKARANG</button>
                            <a href="index.php" class="text-center text-muted text-decoration-none mt-2">Sudah punya akun? Login di sini</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>