<?php
session_start();
include '../config/koneksi.php';

$query = "SELECT * FROM buku ORDER BY judul ASC";
$result = $koneksi->query($query);

while ($row = $result->fetch_assoc()) {
    $books[$row['id']] = $row;
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Buku</title>
    <link rel="stylesheet" href="css/buku.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="js/buku-script.js"></script>
</head>

<body>
<h1 class="header-title-centered">Buku - Buku</h1>

<div class="header-container">
    <a href="index.php" class="kembali-btn-top">
         Kembali ke Beranda<i class="fas fa-arrow-right"></i>
    </a>
</div>

    <div id="search-container">
        <input type="text" id="search-input" placeholder="Cari judul buku...">
        <i class="fas fa-search search-icon"></i>
    </div>

    <div class="controls">
        <button id="view-grid" class="active"><i class="fas fa-th"></i> Grid</button>
        <button id="view-list"><i class="fas fa-list"></i> List</button>
        <select id="sort-books">
            <option value="default">Urutan Default</option>
            <option value="asc">A-Z</option>
            <option value="desc">Z-A</option>
        </select>
    </div>

    <div class="gallery" id="gallery">
        <?php foreach ($books as $id => $book): ?>
            <div class="book" data-title="<?= strtolower(htmlspecialchars($book['judul'] ?? '')) ?>"
                data-author="<?= htmlspecialchars($book['penulis'] ?? '') ?>"
                data-publisher="<?= htmlspecialchars($book['penerbit'] ?? '') ?>"
                data-year="<?= htmlspecialchars($book['tahun'] ?? $book['tahun_terbit'] ?? '') ?>"
                data-category="<?= htmlspecialchars($book['kategori'] ?? '') ?>"
                data-stock="<?= htmlspecialchars($book['stok'] ?? '0') ?>">
                <div class="book-image-container">
                    <img src="gambar/<?= htmlspecialchars($book['gambar']) ?>" loading="lazy">
                    <div class="quick-view">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </div>
                </div>
                <p class="book-title"><?= htmlspecialchars($book['judul']) ?></p>
                <a href="pinjamBuku.php?id=<?= $id ?>" class="pinjam-btn">Pinjam</a>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="no-results">
        <i class="fas fa-exclamation-circle"></i>
        <p>Tidak ada buku yang sesuai dengan pencarian Anda.</p>
    </div>

    <div id="book-detail-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-body">
                <img id="modal-image" src="" alt="Book Cover">
                <div class="modal-info">
                    <h3 id="modal-title"></h3>
                    <div class="book-details">
                        <p><strong>Penulis:</strong> <span id="modal-author"></span></p>
                        <p><strong>Penerbit:</strong> <span id="modal-publisher"></span></p>
                        <p><strong>Tahun Terbit:</strong> <span id="modal-year"></span></p>
                        <p><strong>Kategori:</strong> <span id="modal-category"></span></p>
                        <p><strong>Stok:</strong> <span id="modal-stock"></span></p>
                    </div>
                    <a id="modal-pinjam" href="#" class="modal-pinjam-btn">Pinjam Buku</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>