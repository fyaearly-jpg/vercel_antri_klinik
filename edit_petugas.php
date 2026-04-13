<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM petugas WHERE id='$id'");
$p = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $role = $_POST['role'];
    
    $update = mysqli_query($conn, "UPDATE petugas SET nama_lengkap='$nama', role='$role' WHERE id='$id'");
    if ($update) {
        echo "<script>alert('Data Berhasil Diupdate!'); window.location.href='kelola_petugas.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Petugas - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden">
        <div class="bg-indigo-600 p-6 text-white text-center">
            <h3 class="text-xl font-bold">Edit Profil Petugas</h3>
            <p class="text-indigo-100 text-sm mt-1"><?php echo $p['email']; ?></p>
        </div>

        <form method="POST" class="p-8 space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="nama" 
                       value="<?php echo $p['nama_lengkap']; ?>" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                       required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jabatan / Role</label>
                <select name="role" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none transition-all bg-white cursor-pointer">
                    <option value="staff" <?php if($p['role']=='staff') echo 'selected'; ?>>Staff</option>
                    <option value="dokter" <?php if($p['role']=='dokter') echo 'selected'; ?>>Dokter</option>
                    <option value="nurse" <?php if($p['role']=='nurse') echo 'selected'; ?>>Nurse (Perawat)</option>
                    <option value="admin" <?php if($p['role']=='admin') echo 'selected'; ?>>Admin</option>
                </select>
            </div>

            <div class="pt-4 flex flex-col gap-3">
                <button type="submit" name="update" 
                        class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">
                    Simpan Perubahan
                </button>
                
                <a href="kelola_petugas.php" 
                   class="w-full text-center text-gray-500 text-sm font-medium hover:text-gray-700 py-2">
                    Batal & Kembali
                </a>
            </div>
        </form>
    </div>

</body>
</html>