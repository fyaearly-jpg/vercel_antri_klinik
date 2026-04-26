<?php
session_start();
include '../koneksi.php';

// Proteksi
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: ../login_petugas.php");
    exit;
}

// Logika Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    header("Location: kelola_petugas.php");
}

$petugas = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Petugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Manajemen Petugas Klinik</h3>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Jabatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($petugas)) : ?>
                        <tr>
                            <td><?= $row['nama_lengkap']; ?></td>
                            <td><?= $row['username']; ?></td>
                            <td><span class="badge bg-info text-dark"><?= strtoupper($row['role']); ?></span></td>
                            <td>
                                <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus petugas ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>