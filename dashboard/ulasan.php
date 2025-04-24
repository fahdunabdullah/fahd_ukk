<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$host = "localhost";
$username = "root"; 
$password = ""; 
$database = "fahd_ukk"; 

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$books = [];
$query = "SELECT id, judul FROM buku ORDER BY judul ASC";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

$nama = $_SESSION['user']['nama'];
$success = false;
$ulasan_file = 'ulasan_data.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul_buku = $_POST['judul_buku'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $komentar = $_POST['komentar'] ?? '';

    if ($judul_buku && $rating) {
        $data = "$nama|$judul_buku|$rating|$komentar" . PHP_EOL;
        file_put_contents($ulasan_file, $data, FILE_APPEND);
        $success = true;
    }
}

$ulasan_saya = [];
$search = $_GET['search'] ?? '';

if (file_exists($ulasan_file)) {
    $lines = file($ulasan_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        [$user, $judul, $rate, $komen] = explode('|', $line);
        if ($user === $nama) {
            if (!$search || stripos($judul, $search) !== false) {
                $ulasan_saya[] = [
                    'judul' => $judul,
                    'rating' => $rate,
                    'komentar' => $komen
                ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Ulasan Buku</title>
    <link rel="stylesheet" href="css/ulasan.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <h2>Berikan Ulasan Buku</h2>

            <?php if ($success): ?>
                <p style="color: green;">✅ Ulasan berhasil disimpan!</p>
            <?php endif; ?>

            <form method="POST">
                <label for="judul_buku">Judul Buku:</label>
                <select id="judul_buku" name="judul_buku" required>
                    <option value="">-- Pilih Buku --</option>
                    <?php foreach ($books as $book): ?>
                        <option value="<?= htmlspecialchars($book['judul']) ?>">
                            <?= htmlspecialchars($book['judul']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="rating">Rating (1 - 100):</label>
                <input type="number" id="rating" name="rating" min="1" max="100" required>

                <label for="komentar">Komentar (Opsional):</label>
                <textarea id="komentar" name="komentar" rows="3"></textarea>

                <button type="submit" class="btn">Kirim Ulasan</button>
                <a href="user.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>

        <div class="card search-box">
            <h2>Ulasan Saya</h2>
            <form method="GET">
                <input type="text" name="search" placeholder="Cari judul..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn">Cari</button>
            </form>

            <br>

            <?php if (count($ulasan_saya) === 0): ?>
                <p>Tidak ada ulasan ditemukan.</p>
            <?php else: ?>
                <?php foreach ($ulasan_saya as $ulasan): ?>
                    <div class="ulasan-item">
                        <h4><?= htmlspecialchars($ulasan['judul']) ?></h4>
                        <small>Rating: <?= htmlspecialchars($ulasan['rating']) ?>/100</small>
                        <?php if (!empty($ulasan['komentar'])): ?>
                            <p><?= nl2br(htmlspecialchars($ulasan['komentar'])) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>