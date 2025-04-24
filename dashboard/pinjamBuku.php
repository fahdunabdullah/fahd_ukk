<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Peminjaman Buku</title>
    <link rel="stylesheet" href="css/pinjamBuku.css">
    <script src="js/pinjamBuku-script.js"></script>
</head>

<body>
    <div class="container">
        <h2>Form Peminjaman Buku</h2>

        <?php
        require '../config/koneksi.php';

        if (!isset($_GET['id'])) {
            echo '<div class="error-message">ID Buku tidak ditemukan. Silakan pilih buku terlebih dahulu.</div>';
            echo '<script>window.location.href = "buku.php";</script>';
        } else {
            $id = $_GET['id'];

            $query = "SELECT * FROM buku WHERE id = '$id'";
            $result = $koneksi->query($query);

            if ($result->num_rows > 0) {
                $buku = $result->fetch_assoc();
                echo '<div class="book-info"><h3>' . $buku['judul'] . '</h3></div>';
                ?>

                <form action="prosesPinjamBuku.php" method="POST" onsubmit="return validateDates()">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <div class="form-group">
                        <label for="tanggal_pinjam">Tanggal Pinjam:</label>
                        <input type="date" name="tanggal_pinjam" value="<?php echo date('Y-m-d'); ?>"
                         min="<?php echo date('Y-m-d'); ?>" onchange="updateReturnDate()" required>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_kembali">Tanggal Kembali:</label>
                        <input type="date" id="tanggal_kembali" name="tanggal_kembali" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>"
                        min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                    </div>

                    <div class="info-box">
                        <p>Informasi Peminjaman:</p>
                        <ul>
                            <li>Tanggal kembali harus setelah tanggal pinjam</li>
                            <li>Durasi peminjaman yang disarankan adalah 7 hari</li>
                            <li>Keterlambatan pengembalian akan dikenakan denda Rp 5.000/hari</li>
                        </ul>
                    </div>

                    <button type="submit" name="submit">Pinjam Buku</button>
                </form>

                <?php
            } else {
                echo '<div class="error-message">Buku tidak ditemukan dalam database.</div>';
                echo '<script>window.location.href = "buku.php";</script>';
            }
        }
        ?>

        <div class="back-link">
            <a href="buku.php">Kembali ke Daftar Buku</a>
        </div>
    </div>
</body>

</html>

