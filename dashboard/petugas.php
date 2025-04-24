<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    header("Location: ../auth/login.php");
    exit;
}
$nama = $_SESSION['user']['nama'];
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas Perpustakaan</title>
    <link rel="stylesheet" href="css/petugas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   
</head>

<body>
    <div class="container">
        <div class="card">
            <div>
                <h1>Selamat Datang, <?= htmlspecialchars($nama) ?></h1>
                <span class="user-role"><i class="fas fa-user-tie"></i> Petugas</span>
            </div>

            <h2 class="menu-title">Panel Petugas Perpustakaan</h2>

            <div class="menu-container petugas-menu">
                <a href="tambahBuku.php" class="btn btn-primary">
                    <i class="fas fa-book-open"></i>&nbsp; Tambah Buku
                </a>
                <a href="kelolaBuku.php" class="btn btn-success">
                    <i class="fas fa-book-open"></i>&nbsp; Kelola Buku
                </a>
                <a href="laporanRiwayat.php" class="btn btn-warning">
                    <i class="fas fa-file-alt"></i>&nbsp; Laporan & Riwayat
                </a>
                <a href="#" id="logout-btn" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i>&nbsp; Logout
                </a>
            </div>

            <div class="footer">
                Sistem Perpustakaan Digital - Panel Petugas
            </div>
        </div>
    </div>

    <div id="logout-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Konfirmasi Logout</h2>
            <p>Apakah Anda yakin ingin keluar dari sistem?</p>
            <div class="modal-buttons">
                <button id="confirm-logout" class="btn btn-danger">Ya, Logout</button>
                <button id="cancel-logout" class="btn btn-secondary">Batal</button>
            </div>
        </div>
    </div>
    <script src="js/petugas-script.js"></script>
</body>

</html>
