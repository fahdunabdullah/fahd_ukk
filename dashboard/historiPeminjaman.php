<?php
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require '../config/koneksi.php';

$user_id = $_SESSION['user']['id'];
$user_name = $_SESSION['user']['nama'] ?? 'Pengguna';

// Query to get all borrowing history for the current user
$query = "SELECT pb.id, pb.book_id, pb.tanggal_pinjam, pb.tanggal_kembali, pb.status, 
                 b.judul, b.penulis, b.gambar, 
                 IFNULL(pb.denda, 0) as denda
          FROM peminjaman_buku pb
          JOIN buku b ON pb.book_id = b.id
          WHERE pb.user_id = ?
          ORDER BY pb.tanggal_pinjam DESC";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if(isset($_GET['cetak']) && $_GET['cetak'] == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Histori_Peminjaman_'.date('Ymd').'.csv');
    
    $output = fopen('php://output', 'w');
    
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, ['No', 'Judul Buku', 'Penulis', 'Tanggal Pinjam', 'Tanggal Kembali', 'Status', 'Denda']);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $no = 1;
    $today = date('Y-m-d');
    
    while($row = $result->fetch_assoc()) {
        $status_text = ($row['status'] == 0) ? 'Sedang Dipinjam' : 'Dikembalikan';
        
        if ($row['status'] == 0 && $today > $row['tanggal_kembali']) {
            $status_text .= ' (Terlambat)';
        }
        
        $denda_text = ($row['denda'] > 0) ? 'Rp ' . number_format($row['denda'], 0, ',', '.') : '-';
        
        $data = [
            $no,
            $row['judul'],
            $row['penulis'],
            date('d-m-Y', strtotime($row['tanggal_pinjam'])),
            date('d-m-Y', strtotime($row['tanggal_kembali'])),
            $status_text,
            $denda_text
        ];
        
        fputcsv($output, $data);
        $no++;
    }
    
    fclose($output);
    exit;
}
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Peminjaman Buku</title>
    <link rel="stylesheet" href="css/historiPeminjaman.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Histori Peminjaman Buku</h2>
        
        <div class="action-buttons">
            <a href="historiPeminjaman.php?cetak=csv" class="print-btn">
                <i class="fas fa-file-csv"></i> Cetak Laporan (CSV)
            </a>
        </div>
        
        <?php if ($result->num_rows > 0) : ?>
            <div class="filter-container">
                <button class="filter-btn active" data-filter="all">Semua</button>
                <button class="filter-btn" data-filter="active">Sedang Dipinjam</button>
                <button class="filter-btn" data-filter="returned">Dikembalikan</button>
                <button class="filter-btn" data-filter="late">Terlambat</button>
            </div>
            
            <div class="history-list">
                <?php 
                $today = date('Y-m-d');
                while ($row = $result->fetch_assoc()) : 
                    $status_text = ($row['status'] == 0) ? 'Sedang Dipinjam' : 'Dikembalikan';
                    $item_class = ($row['status'] == 0) ? 'active' : 'returned';
                    
                    $is_late = false;
                    if ($row['status'] == 0 && $today > $row['tanggal_kembali']) {
                        $is_late = true;
                        $item_class .= ' late';
                    }
                ?>
                    <div class="history-item <?php echo $item_class; ?>">
                        <div class="book-image">
                            <?php if (!empty($row['gambar'])) : ?>
                                <img src="gambar/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['judul']); ?>">
                            <?php else : ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h3><?php echo htmlspecialchars($row['judul']); ?></h3>
                            <p><strong>Penulis:</strong> <?php echo htmlspecialchars($row['penulis']); ?></p>
                            <p><strong>Tanggal Pinjam:</strong> <?php echo date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?></p>
                            <p><strong>Batas Kembali:</strong> <?php echo date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></p>
                            
                            <?php if ($row['status'] == 1) : ?>
                                <p><strong>Tanggal Dikembalikan:</strong> <?php echo date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></p>
                            <?php endif; ?>
                            
                            <div class="status-container">
                                <span class="status-badge <?php echo strtolower($status_text); ?>">
                                    <?php echo $status_text; ?>
                                </span>
                                
                                <?php if ($is_late) : ?>
                                    <span class="status-badge late">Terlambat</span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($row['denda'] > 0) : ?>
                                <p class="denda"><strong>Denda:</strong> Rp <?php echo number_format($row['denda'], 0, ',', '.'); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($row['status'] == 0) : ?>
                                <a href="kembalikanBuku.php" class="return-link">Kembalikan Buku</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="no-history">
                <p>Belum ada riwayat peminjaman buku.</p>
            </div>
        <?php endif; ?>
        
        <div class="buttons">
            <a href="index.php" class="back-link">Kembali ke Beranda</a>
            <a href="buku.php" class="browse-link">Pinjam Buku Lainnya</a>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const historyItems = document.querySelectorAll('.history-item');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    historyItems.forEach(item => {
                        if (filter === 'all') {
                            item.style.display = 'flex';
                        } else if (filter === 'active' && item.classList.contains('active')) {
                            item.style.display = 'flex';
                        } else if (filter === 'returned' && item.classList.contains('returned')) {
                            item.style.display = 'flex';
                        } else if (filter === 'late' && item.classList.contains('late')) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>