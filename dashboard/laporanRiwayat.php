<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'petugas')) {
    header("Location: ../auth/login.php");
    exit;
}

$nama = $_SESSION['user']['nama'];
$role = $_SESSION['user']['role'];
$tanggal_mulai = $_GET['tanggal_mulai'] ?? date('Y-m-01');
$tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d');
$jenis_laporan = $_GET['jenis_laporan'] ?? 'peminjaman';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $koneksi->query("DELETE FROM peminjaman_buku WHERE id = $id");
    header("Location: laporanRiwayat.php?jenis_laporan=peminjaman&tanggal_mulai={$tanggal_mulai}&tanggal_akhir={$tanggal_akhir}");
    exit;
}

$query_peminjaman = "
    SELECT pb.id, u.nama AS nama_user, b.judul, pb.tanggal_pinjam, pb.tanggal_kembali,
    CASE WHEN pb.status = 1 THEN 'Sudah Dikembalikan' ELSE 'Masih Dipinjam' END AS status_text
    FROM peminjaman_buku pb
    JOIN buku b ON pb.book_id = b.id
    JOIN user u ON pb.user_id = u.id
    WHERE pb.tanggal_pinjam BETWEEN ? AND ?
    ORDER BY pb.tanggal_pinjam DESC
";

if ($jenis_laporan === 'peminjaman') {
    $stmt = $koneksi->prepare($query_peminjaman);
    $stmt->bind_param("ss", $tanggal_mulai, $tanggal_akhir);
    $stmt->execute();
    $result = $stmt->get_result();
}

if ($jenis_laporan === 'populer') {
    $result_populer = $koneksi->query(
        "
        SELECT b.judul, COUNT(pb.id) AS total_dipinjam
        FROM peminjaman_buku pb
        JOIN buku b ON pb.book_id = b.id
        GROUP BY pb.book_id
        ORDER BY total_dipinjam DESC
        "
    );
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan</title>
    <link rel="stylesheet" href="css/laporanRiwayat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="header-container">
        <h2 class="report-title">Laporan dan Riwayat</h2>
    </div>

    <div class="laporan-select">
        <form method="GET" class="filter-form">
            <label for="jenis_laporan">Pilih Laporan</label>
            <select id="jenis_laporan" name="jenis_laporan" onchange="this.form.submit()">
                <option value="peminjaman" <?= $jenis_laporan === 'peminjaman' ? 'selected' : '' ?>>Riwayat Peminjaman
                </option>
                <option value="populer" <?= $jenis_laporan === 'populer' ? 'selected' : '' ?>>Buku Terpopuler</option>
            </select>
        </form>
    </div>

    <?php if ($jenis_laporan === 'peminjaman'): ?>
        <?php
        $total_peminjaman = $result->num_rows;
        $masih_dipinjam = 0;
        $sudah_kembali = 0;
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            if ($row['status_text'] === 'Masih Dipinjam')
                $masih_dipinjam++;
            else
                $sudah_kembali++;
        }
        $result->data_seek(0);
        ?>

        <div class="report-summary">
            <p><strong>Periode:</strong> <?= date('d/m/Y', strtotime($tanggal_mulai)) ?> –
                <?= date('d/m/Y', strtotime($tanggal_akhir)) ?>
            </p>
            <p><strong>Total Peminjaman:</strong> <?= $total_peminjaman ?> | <strong>Masih Dipinjam:</strong>
                <?= $masih_dipinjam ?> | <strong>Sudah Dikembalikan:</strong> <?= $sudah_kembali ?></p>
        </div>

        <div class="search-print-container">
            <div id="search-container">
                <input type="text" id="search-input" placeholder="Cari judul buku...">
                <i class="fas fa-search search-icon"></i>
            </div>

            <div class="table-actions">
                <a href="javascript:window.print()" class="btn-action btn-print">
                    <i class="fas fa-print"></i> Cetak Laporan
                </a>
            </div>
        </div>
        
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_user']) ?></td>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td><?= $row['tanggal_pinjam'] ?></td>
                            <td><?= $row['tanggal_kembali'] ?></td>
                            <td><?= $row['status_text'] ?></td>
                            <td>
                                <a href="?jenis_laporan=peminjaman&delete=<?= $row['id'] ?>&tanggal_mulai=<?= $tanggal_mulai ?>&tanggal_akhir=<?= $tanggal_akhir ?>"
                                    onclick="return confirm('Yakin ingin menghapus?')" class="btn-action btn-delete">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-message">
                <i class="fas fa-info-circle"></i> Tidak ada data peminjaman pada periode ini.
            </div>
        <?php endif; ?>

    <?php elseif ($jenis_laporan === 'populer'): ?>
        <section class="populer-header">
            <h3 class="report-title">Laporan Buku Terpopuler</h3>
        </section>
        <div class="search-print-container">
            <div id="search-container">
                <input type="text" id="search-input" placeholder="Cari judul buku...">
                <i class="fas fa-search search-icon"></i>
            </div>

            <div class="table-actions">
                <a href="javascript:window.print()" class="btn-action btn-print">
                    <i class="fas fa-print"></i> Cetak Laporan
                </a>
            </div>
        </div>
        
        <?php if ($result_populer->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Buku</th>
                        <th>Jumlah Dipinjam</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($row = $result_populer->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td><?= $row['total_dipinjam'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-message">
                <i class="fas fa-info-circle"></i> Tidak ada data peminjaman untuk ditampilkan.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <footer class="footer-nav">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Panel Admin
        </a>
    </footer>
    <script src="js/laporanRiwayat-script.js"></script>
</body>

</html>