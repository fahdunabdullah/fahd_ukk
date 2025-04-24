<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
$nama = $_SESSION['user']['nama'];

require_once '../config/koneksi.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$totalBooks = 0;
$borrowedBooks = 0;
$overdueBooks = 0;

try {
    $resultTotal = $koneksi->query("SELECT COUNT(*) as total FROM buku");
    if ($resultTotal && $row = $resultTotal->fetch_assoc()) {
        $totalBooks = $row['total'];
    }

    $resultBorrowed = $koneksi->query("SELECT COUNT(*) as borrowed FROM peminjaman_buku WHERE status = 0");
    if ($resultBorrowed && $row = $resultBorrowed->fetch_assoc()) {
        $borrowedBooks = $row['borrowed'];
    }

    $resultOverdue = $koneksi->query("SELECT COUNT(*) as overdue FROM peminjaman_buku 
                            WHERE status = 0 
                            AND tanggal_kembali < CURDATE()");
    if ($resultOverdue && $row = $resultOverdue->fetch_assoc()) {
        $overdueBooks = $row['overdue'];
    }

    error_log("Total buku: " . $totalBooks);
    error_log("Buku dipinjam: " . $borrowedBooks);
    error_log("Buku terlambat: " . $overdueBooks);

} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Error mengambil data: " . $error);
    $totalBooks = 0;
    $borrowedBooks = 0;
    $overdueBooks = 0;
}

$totalBooks = (int) $totalBooks;
$borrowedBooks = (int) $borrowedBooks;
$overdueBooks = (int) $overdueBooks;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Perpustakaan</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <div>
                <h1>Selamat Datang, <?= htmlspecialchars($nama) ?></h1>
                <span class="user-role"><i class="fas fa-user-shield"></i> Admin</span>
            </div>

            <div class="live-info">
                <div id="current-time"></div>
            </div>

            <h2 class="menu-title">Panel Administrasi Perpustakaan</h2>

            <div class="menu-container admin-menu">
                <a href="tambahBuku.php" class="btn btn-primary menu-item">
                    <i class="fas fa-plus-circle"></i>&nbsp; Tambah Buku
                </a>
                <a href="kelolaBuku.php" class="btn btn-success menu-item">
                    <i class="fas fa-book-open"></i>&nbsp; Kelola buku
                </a>
                <a href="laporanRiwayat.php" class="btn btn-warning menu-item">
                    <i class="fas fa-file-alt"></i>&nbsp; Laporan & Riwayat
                </a>
                <a href="#" class="btn btn-danger menu-item" id="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>&nbsp; Logout
                </a>
            </div>

            <div class="quick-stats">
                <div class="stat-box" id="total-books">
                    <i class="fas fa-book"></i>
                    <span class="stat-count"><?= $totalBooks ?></span>
                    <span class="stat-label">Total Buku</span>
                </div>
                <div class="stat-box" id="borrowed-books">
                    <i class="fas fa-hand-holding-book"></i>
                    <span class="stat-count"><?= $borrowedBooks ?>📚</span>
                    <span class="stat-label">Dipinjam</span>
                </div>
                <div class="stat-box" id="overdue-books">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="stat-count"><?= $overdueBooks ?></span>
                    <span class="stat-label">Terlambat</span>
                </div>
            </div>

            <div class="footer">
                Sistem Perpustakaan Digital - Panel Admin
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
    <script src="js/admin-script.js"></script>
</body>

</html>