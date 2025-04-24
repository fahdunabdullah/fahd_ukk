<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "fahd_ukk";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = trim($_POST['book_id']);
    $judul = trim($_POST['judul']);
    $penulis = trim($_POST['penulis']);
    $penerbit = trim($_POST['penerbit']);
    $tahun = trim($_POST['tahun']);
    $kategori = trim($_POST['kategori']);
    $stok = (int) $_POST['stok'];

    if (empty($book_id) || empty($judul) || empty($penulis) || empty($penerbit) || empty($tahun) || empty($kategori) || $stok <= 0) {
        $error_message = "Semua field harus diisi dengan benar!";
    } elseif (!is_numeric($tahun) || strlen($tahun) != 4) {
        $error_message = "Tahun harus berupa 4 digit angka!";
    } else {
        $target_dir = "gambar/";
        $file_name = "default_book.jpg";

        if (isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] == 0) {
            $file_name = time() . "_" . basename($_FILES["gambar"]["name"]);
            $target_file = $target_dir . $file_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["gambar"]["tmp_name"]);
            if ($check === false) {
                $error_message = "File yang diupload bukan gambar.";
            } elseif ($_FILES["gambar"]["size"] > 2000000) {
                $error_message = "Ukuran file terlalu besar. Maksimal 2MB.";
            } elseif ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                $error_message = "Hanya file JPG, JPEG, dan PNG yang diperbolehkan.";
            } else {
                if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                    $error_message = "Terjadi kesalahan saat mengupload file.";
                }
            }
        }

        if (empty($error_message)) {
            $sql = "INSERT INTO buku (book_id, judul, penulis, penerbit, tahun_terbit, kategori, stok, gambar) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssisis", $book_id, $judul, $penulis, $penerbit, $tahun, $kategori, $stok, $file_name);

            if ($stmt->execute()) {
                $books_file = file_get_contents('buku.php');

                preg_match('/\$books\s*=\s*\[(.*?)\];/s', $books_file, $matches);
                if (isset($matches[1])) {
                    $current_books = $matches[1];

                    preg_match_all('/(\d+)\s*=>\s*\[/', $current_books, $id_matches);
                    $last_id = !empty($id_matches[1]) ? max($id_matches[1]) : 0;
                    $new_id = $last_id + 1;

                    $new_book = "\n    $new_id => ['judul' => '$judul', 'gambar' => '$file_name', 'penulis' => '$penulis', 'penerbit' => '$penerbit', 'tahun' => '$tahun'],";

                    $updated_books = str_replace('];', $new_book . "\n];", $books_file);

                    file_put_contents('buku.php', $updated_books);
                }

                $success_message = "Buku berhasil ditambahkan!";

                $book_id = $judul = $penulis = $penerbit = $tahun = $kategori = "";
                $stok = 1;
            } else {
                $error_message = "Gagal menyimpan data buku ke database: " . $conn->error;
            }
        }
    }
}

$conn->close();
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku Baru</title>
    <link rel="stylesheet" href="css/tambahBuku.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="js/tambahBuku-script.js"></script>

</head>

<body>
    <h2>Tambah Buku Baru</h2>

    <div class="form-container">
        <div class="form-header">
            <h3 class="form-title">Form Tambah Buku</h3>
            <p class="form-subtitle">Silakan isi semua field berikut untuk menambahkan buku baru ke perpustakaan</p>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="tambahBuku.php" method="post" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="book_id">ID Buku (tidak akan ditampilkan):</label>
                    <input type="text" id="book_id" name="book_id"
                        value="<?php echo isset($book_id) ? htmlspecialchars($book_id) : 'BK-' . time(); ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="judul">Judul Buku:</label>
                    <input type="text" id="judul" name="judul"
                        value="<?php echo isset($judul) ? htmlspecialchars($judul) : ''; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="penulis">Penulis:</label>
                    <input type="text" id="penulis" name="penulis"
                        value="<?php echo isset($penulis) ? htmlspecialchars($penulis) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="penerbit">Penerbit:</label>
                    <input type="text" id="penerbit" name="penerbit"
                        value="<?php echo isset($penerbit) ? htmlspecialchars($penerbit) : ''; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="tahun">Tahun Terbit:</label>
                    <input type="number" id="tahun" name="tahun" min="1900" max="<?php echo date('Y'); ?>"
                        value="<?php echo isset($tahun) ? htmlspecialchars($tahun) : date('Y'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="kategori">Kategori:</label>
                    <select id="kategori" name="kategori" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Novel" <?php echo (isset($kategori) && $kategori == 'Novel') ? 'selected' : ''; ?>>
                            Novel</option>
                        <option value="Fiksi" <?php echo (isset($kategori) && $kategori == 'Fiksi') ? 'selected' : ''; ?>>
                            Fiksi</option>
                        <option value="Non Fiksi" <?php echo (isset($kategori) && $kategori == 'Non Fiksi') ? 'selected' : ''; ?>>Non Fiksi</option>
                        <option value="Pendidikan" <?php echo (isset($kategori) && $kategori == 'Pendidikan') ? 'selected' : ''; ?>>Pendidikan</option>
                        <option value="Komik" <?php echo (isset($kategori) && $kategori == 'Komik') ? 'selected' : ''; ?>>
                            Komik</option>
                        <option value="Biografi" <?php echo (isset($kategori) && $kategori == 'Biografi') ? 'selected' : ''; ?>>Biografi</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="stok">Stok Buku:</label>
                    <input type="number" id="stok" name="stok" min="1"
                        value="<?php echo isset($stok) ? htmlspecialchars($stok) : 1; ?>" required>
                </div>

                <div class="form-group">
                    <label>Gambar Sampul:</label>
                    <div class="file-upload-container">
                        <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                        <span class="file-upload-label">Klik atau seret gambar ke sini</span>
                        <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/jpg">
                    </div>
                    <div class="preview-container">
                        <img id="imagePreview" src="#" alt="Preview Gambar">
                    </div>
                </div>
            </div>

            <button type="submit" class="submit-btn"><i class="fas fa-plus"></i> Tambah Buku</button>

            <div class="actions-container">
                <a href="buku.php" class="kembali-btn"><i class="fas fa-arrow-left"></i> Kembali ke Galeri Buku</a>
            </div>
        </form>
    </div>

</body>

</html>