<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'petugas')) {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $book_id = $_GET['id'];
    
    $query = $koneksi->prepare("SELECT gambar FROM buku WHERE id = ?");
    $query->bind_param("i", $book_id);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
        $image_file = $book['gambar'];
        
        $delete_query = $koneksi->prepare("DELETE FROM buku WHERE id = ?");
        $delete_query->bind_param("i", $book_id);
        
        if ($delete_query->execute()) {
            if ($image_file != 'default_book.jpg' && file_exists('gambar/' . $image_file)) {
                unlink('gambar/' . $image_file);
            }
            
            echo "<script>alert('Buku berhasil dihapus!'); window.location='kelolaBuku.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus buku!'); window.location='kelolaBuku.php';</script>";
        }
    } else {
        echo "<script>alert('Buku tidak ditemukan!'); window.location='kelolaBuku.php';</script>";
    }
} else {
    echo "<script>alert('ID buku tidak valid!'); window.location='kelolaBuku.php';</script>";
}
?>