<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'petugas')) {
    header("Location: ../auth/login.php");
    exit;
}

$query = "SELECT * FROM buku ORDER BY judul ASC";
$result = $koneksi->query($query);

$books = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Buku</title>
    <link rel="stylesheet" href="css/buku.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="js/kelola-script.js"></script>
    <style>
        .book-actions .edit-btn,
        .book-actions .delete-btn,
        .modal-actions .edit-btn,
        .modal-actions .delete-btn {
            display: inline-block;
            width: 120px;
            padding: 6px 0;
            text-align: center;
            font-size: 14px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
        }

        .book-actions .edit-btn,
        .modal-actions .edit-btn {
            background-color: #4CAF50;
        }

        .book-actions .delete-btn,
        .modal-actions .delete-btn {
            background-color: #f44336;
        }

        .quick-view {
            margin-top: 8px;
            cursor: pointer;
            color: #007BFF;
            font-size: 14px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 6px;
            width: 60%;
            max-width: 500px;
        }

        .close {
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="header-container">
        <h1 class="header-title-centered">Buku - Buku</h1>
        <a href="index.php" class="kembali-btn-top">
            Kembali ke Beranda <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="top-controls-wrapper">
        <div id="search-container">
            <input type="text" id="search-input" placeholder="Cari judul buku...">
            <i class="fas fa-search search-icon"></i>
        </div>

        <div class="controls-and-actions">

            <div class="controls">
                <button id="view-grid" class="active"><i class="fas fa-th"></i> Grid</button>
                <button id="view-list"><i class="fas fa-list"></i> List</button>
                <select id="sort-books">
                    <option value="default">Urutan Default</option>
                    <option value="asc">A-Z</option>
                    <option value="desc">Z-A</option>
                </select>
            </div>
        </div>
    </div>

    <div class="gallery" id="gallery">
        <?php if (!empty($books)): ?>
            <?php foreach ($books as $book): ?>
                <div class="book" data-title="<?= strtolower(htmlspecialchars($book['judul'] ?? '')) ?>"
                    data-author="<?= htmlspecialchars($book['penulis'] ?? '') ?>"
                    data-publisher="<?= htmlspecialchars($book['penerbit'] ?? '') ?>"
                    data-year="<?= htmlspecialchars($book['tahun'] ?? $book['tahun_terbit'] ?? '') ?>"
                    data-category="<?= htmlspecialchars($book['kategori'] ?? '') ?>"
                    data-stock="<?= htmlspecialchars($book['stok'] ?? '0') ?>">
                    <div class="book-image-container">
                        <img src="gambar/<?= htmlspecialchars($book['gambar'] ?? 'default.jpg') ?>" loading="lazy">
                        <div class="quick-view">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </div>
                    </div>
                    <p class="book-title"><?= htmlspecialchars($book['judul']) ?></p>
                    <div class="book-info">
                        <small>Stok: <?= htmlspecialchars($book['stok']) ?></small>
                    </div>
                    <div class="book-actions">
                        <a href="editBuku.php?id=<?= $book['id'] ?>" class="edit-btn">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="javascript:void(0)" onclick="confirmDelete(<?= $book['id'] ?>)" class="delete-btn">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                        <a href="pinjamBuku.php?id=<?= $book['id'] ?>" class="pinjam-btn" style="display: none;">
                            Pinjam
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Tidak ada buku yang tersedia.</p>
        <?php endif; ?>
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
                    <div class="modal-actions">
                        <a id="modal-edit" href="#" class="edit-btn">
                            <i class="fas fa-edit"></i> Edit Buku
                        </a>
                        <a id="modal-delete" href="javascript:void(0)" class="delete-btn">
                            <i class="fas fa-trash"></i> Hapus Buku
                        </a>
                        <a id="modal-pinjam" href="#" class="modal-pinjam-btn">Pinjam Buku</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>