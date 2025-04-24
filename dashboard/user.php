<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}
$nama = $_SESSION['user']['nama'];
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital - Pengguna</title>
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="welcome-header">
                <h1>Selamat Datang, <?= $nama ?></h1>
                <span class="user-role"><i class="fas fa-user"></i> Anggota Perpustakaan</span>
            </div>

            <h2 class="menu-title">Menu Layanan Perpustakaan</h2>

            <div class="menu-container user-menu">
                <a href="buku.php" class="btn btn-success">
                    <i class="fas fa-book-open"></i>&nbsp; pinjam buku
                </a>
                <a href="kembalikanBuku.php" class="btn btn-warning">
                    <i class="fas fa-undo"></i>&nbsp; Kembalikan Buku
                </a>
                <a href="ulasan.php" class="btn btn-primary">
                    <i class="fas fa-star"></i>&nbsp; Beri Ulasan Buku
                </a>
                <a href="historiPeminjaman.php" class="btn btn-success">
                    <i class="fas fa-history"></i>
                    <span>Histori Peminjaman</span>
                </a>
                <a href="../auth/logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i>&nbsp; Logout
                </a>
            </div>

            <div class="footer">
                Sistem Perpustakaan Digital
            </div>
        </div>
    </div>

    <script src="js/user-script.js"></script>
</body>

</html>