<?php
session_start();
require '../config/koneksi.php';

if (isset($_POST['submit'])) {
    $book_id = $_POST['id'];
    $user_id = $_SESSION['user']['id'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $status = 0; 

    if (strtotime($tanggal_kembali) <= strtotime($tanggal_pinjam)) {
        echo "<script>alert('Tanggal kembali harus setelah tanggal pinjam!'); window.history.back();</script>";
        exit();
    }
    
    $durasi = floor((strtotime($tanggal_kembali) - strtotime($tanggal_pinjam)) / (60 * 60 * 24));
    
    $query = "INSERT INTO peminjaman_buku (book_id, user_id, tanggal_pinjam, tanggal_kembali, status) 
              VALUES (?, ?, ?, ?, ?)";

    $stmt = $koneksi->prepare($query);

    if (!$stmt) {
        die("Query error: " . $koneksi->error);
    }

    $stmt->bind_param("iissi", $book_id, $user_id, $tanggal_pinjam, $tanggal_kembali, $status);

    if ($stmt->execute()) {
        
        $pesan = "Buku berhasil dipinjam!";
        
        if ($durasi > 14) {
            $pesan .= " PERHATIAN: Anda meminjam buku melebihi durasi normal (14 hari). ";
        }
        
        $pesan .= " Harap kembalikan buku sebelum tanggal " . date('d-m-Y', strtotime($tanggal_kembali)) . 
                  ". Keterlambatan akan dikenakan denda Rp 5.000/hari.";
        
        echo "<script>alert('" . $pesan . "'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal meminjam buku! Error: " . $stmt->error . "'); window.location='buku.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>window.location='buku.php';</script>";
}
?>