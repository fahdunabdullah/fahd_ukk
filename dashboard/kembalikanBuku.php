<?php
session_start();

require '../config/koneksi.php';

function hitungDenda($tanggal_seharusnya, $tanggal_pengembalian)
{
    $tgl_seharusnya = new DateTime($tanggal_seharusnya);
    $tgl_pengembalian = new DateTime($tanggal_pengembalian);

    if ($tgl_pengembalian > $tgl_seharusnya) {
        $selisih = $tgl_pengembalian->diff($tgl_seharusnya);
        $hari_telat = $selisih->days;
        return $hari_telat * 5000;
    }

    return 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_peminjaman = $_POST['id_peminjaman'];
    $tanggal_kembali_actual = date("Y-m-d");
    $status = 1;

    $query_info = "SELECT tanggal_kembali, book_id FROM peminjaman_buku WHERE id = ?";
    $stmt_info = $koneksi->prepare($query_info);
    $stmt_info->bind_param("i", $id_peminjaman);
    $stmt_info->execute();
    $result_info = $stmt_info->get_result();
    $data = $result_info->fetch_assoc();

    $tanggal_kembali_seharusnya = $data['tanggal_kembali'];

    $denda = hitungDenda($tanggal_kembali_seharusnya, $tanggal_kembali_actual);

    $check_column = "SHOW COLUMNS FROM peminjaman_buku LIKE 'denda'";
    $column_result = $koneksi->query($check_column);

    if ($column_result->num_rows > 0) {
        $query = "UPDATE peminjaman_buku 
                  SET status = ?, 
                      tanggal_kembali = ?, 
                      denda = ? 
                  WHERE id = ? AND status = 0";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("isdi", $status, $tanggal_kembali_actual, $denda, $id_peminjaman);
    } else {
        $query = "UPDATE peminjaman_buku 
                  SET status = ?, 
                      tanggal_kembali = ? 
                  WHERE id = ? AND status = 0";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("isi", $status, $tanggal_kembali_actual, $id_peminjaman);
    }

    if (!$stmt) {
        die("Query error: " . $koneksi->error);
    }

    if ($stmt->execute()) {
        $pesan = "Buku berhasil dikembalikan!";

        if ($denda > 0) {
            $pesan .= " Anda dikenakan denda keterlambatan sebesar Rp " . number_format($denda, 0, ',', '.') . " karena terlambat mengembalikan buku.";
        }

        echo "<script>alert('$pesan'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal mengembalikan buku!');</script>";
    }
    $stmt->close();
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kembalikan Buku</title>
    <link rel="stylesheet" href="css/kembalikanBuku.css">
</head>

<body>

    <div class="container">
        <h2>Daftar Buku yang Sedang Dipinjam</h2>

        <div class="denda-info">
            <h3>Informasi Denda</h3>
            <p>Pengembalian buku yang melewati tanggal yang telah ditentukan akan dikenakan denda keterlambatan sebesar
                <strong>Rp 5.000 per hari</strong>.
            </p>
            <p>Harap mengembalikan buku tepat waktu untuk menghindari sanksi denda.</p>
        </div>

        <div class="gallery">
            <?php
            if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
                echo "<p>Silakan login terlebih dahulu</p>";
                echo "<script>window.location='../login.php';</script>";
                exit;
            }

            $user_id = $_SESSION['user']['id'];

            $query = "SELECT pb.id, pb.book_id, pb.tanggal_pinjam, pb.tanggal_kembali, b.judul, b.gambar 
                      FROM peminjaman_buku pb 
                      JOIN buku b ON pb.book_id = b.id
                      WHERE pb.user_id = ? AND pb.status = 0";

            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                echo "<p>Tidak ada buku yang sedang dipinjam</p>";
            }

            $today = new DateTime(date('Y-m-d'));

            while ($row = $result->fetch_assoc()):
                $tanggal_kembali = new DateTime($row['tanggal_kembali']);
                $selisih = $today->diff($tanggal_kembali);
                $selisih_hari = $selisih->days;
                $selisih_arah = ($today > $tanggal_kembali) ? -1 : 1;
                $selisih_hari = $selisih_hari * $selisih_arah;

                $status_class = 'normal';
                $status_text = '';
                $denda_estimasi = 0;

                if ($selisih_hari < 0) {
                    $status_class = 'late';
                    $hari_terlambat = abs($selisih_hari);
                    $denda_estimasi = $hari_terlambat * 5000;
                    $status_text = "Terlambat $hari_terlambat hari. Estimasi denda: Rp " . number_format($denda_estimasi, 0, ',', '.');
                } else if ($selisih_hari <= 2) {
                    $status_class = 'warning';
                    $status_text = "Batas waktu Dalam $selisih_hari hari.";
                } else {
                    $status_text = "Batas Waktu Dalam $selisih_hari hari.";
                }
                ?>

                <div class="book">
                    <div class="status-indicator status-<?php echo $status_class; ?>"></div>
                    <img src="gambar/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                    <p><?= htmlspecialchars($row['judul']) ?></p>
                    <div class="book-info <?php echo $status_class; ?>">
                        <p><strong>Tanggal Pinjam:</strong> <?= date('d-m-Y', strtotime($row['tanggal_pinjam'])) ?></p>
                        <p><strong>Tanggal Kembali:</strong> <?= date('d-m-Y', strtotime($row['tanggal_kembali'])) ?></p>
                        <p><strong>Status:</strong> <?= $status_text ?></p>
                    </div>
                    <form method="POST"
                        onsubmit="return confirm('Yakin ingin mengembalikan buku ini?<?= ($denda_estimasi > 0) ? ' Anda akan dikenakan denda Rp ' . number_format($denda_estimasi, 0, ',', '.') . '.' : '' ?>');">
                        <input type="hidden" name="id_peminjaman" value="<?= $row['id'] ?>">
                        <button type="submit">Kembalikan Buku</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>

        <a href="index.php" class="back-link">Kembali ke Beranda</a>
    </div>

</body>

</html>