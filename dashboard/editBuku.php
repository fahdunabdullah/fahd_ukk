<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'petugas')) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: buku.php");
    exit;
}

$id = $_GET['id'];
$success_message = '';
$error_message = '';

$query = "SELECT * FROM buku WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: buku.php");
    exit;
}

$book = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];

    if (empty($judul) || empty($penulis) || empty($penerbit) || empty($tahun_terbit) || empty($kategori) || !is_numeric($stok)) {
        $error_message = "Semua field harus diisi dengan benar!";
    } else {
        $gambar = $book['gambar'];

        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            $file_type = $_FILES['gambar']['type'];

            if (in_array($file_type, $allowed_types)) {
                $new_filename = uniqid() . '_' . $_FILES['gambar']['name'];
                $upload_dir = 'gambar/';

                if ($gambar !== 'default.jpg' && file_exists($upload_dir . $gambar)) {
                    unlink($upload_dir . $gambar);
                }

                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $new_filename)) {
                    $gambar = $new_filename;
                } else {
                    $error_message = "Gagal mengupload gambar. Silakan coba lagi.";
                }
            } else {
                $error_message = "Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.";
            }
        }

        if (empty($error_message)) {
            $update_query = "UPDATE buku SET 
                judul = ?, 
                penulis = ?, 
                penerbit = ?, 
                tahun_terbit = ?, 
                kategori = ?, 
                stok = ?, 
                gambar = ? 
                WHERE id = ?";

            $update_stmt = $koneksi->prepare($update_query);
            $update_stmt->bind_param("sssssssi", $judul, $penulis, $penerbit, $tahun_terbit, $kategori, $stok, $gambar, $id);

            if ($update_stmt->execute()) {
                $success_message = "Buku berhasil diperbarui!";

                $stmt->execute();
                $result = $stmt->get_result();
                $book = $result->fetch_assoc();
            } else {
                $error_message = "Gagal memperbarui data buku: " . $koneksi->error;
            }
        }
    }
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku</title>
    <link rel="stylesheet" href="css/editBuku.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <h2>Edit Buku</h2>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <div class="book-preview">
            <img src="gambar/<?= htmlspecialchars($book['gambar']) ?>" alt="<?= htmlspecialchars($book['judul']) ?>"
                class="book-image">
            <div class="book-details">
                <h3><?= htmlspecialchars($book['judul']) ?></h3>
                <p><strong>Penulis:</strong> <?= htmlspecialchars($book['penulis']) ?></p>
                <p><strong>Penerbit:</strong> <?= htmlspecialchars($book['penerbit']) ?></p>
                <p><strong>Tahun Terbit:</strong>
                    <?= htmlspecialchars($book['tahun_terbit'] ?? $book['tahun_terbit'] ?? '') ?></p>
                <p><strong>Kategori:</strong> <?= htmlspecialchars($book['kategori']) ?></p>
                <p><strong>Stok:</strong> <?= htmlspecialchars($book['stok']) ?></p>
            </div>
        </div>

        <form action="editBuku.php?id=<?= $id ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="judul">Judul Buku</label>
                <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($book['judul']) ?>" required>
            </div>

            <div class="form-group">
                <label for="penulis">Penulis</label>
                <input type="text" id="penulis" name="penulis" value="<?= htmlspecialchars($book['penulis']) ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="penerbit">Penerbit</label>
                <input type="text" id="penerbit" name="penerbit" value="<?= htmlspecialchars($book['penerbit']) ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="tahun_terbit">Tahun Terbit</label>
                <input type="text" id="tahun_terbit" name="tahun_terbit"
                    value="<?= htmlspecialchars($book['tahun_terbit'] ?? $book['tahun_terbit'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>
                <input type="text" id="kategori" name="kategori" value="<?= htmlspecialchars($book['kategori']) ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" id="stok" name="stok" value="<?= htmlspecialchars($book['stok']) ?>" min="0"
                    required>
            </div>

            <div class="form-group">
                <label for="gambar">Gambar Buku</label>
                <input type="file" id="gambar" name="gambar" accept="image/jpeg, image/png, image/jpg">
                <div class="image-preview">
                    <p>Gambar Saat Ini:</p>
                    <img id="preview-image" src="gambar/<?= htmlspecialchars($book['gambar']) ?>" alt="Preview">
                </div>
            </div>

            <div class="btn-container">
                <button type="submit" class="save-btn">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="kelolaBuku.php" class="cancel-btn">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('gambar').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('preview-image').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>