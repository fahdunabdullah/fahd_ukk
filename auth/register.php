<?php
session_start();
include '../config/koneksi.php';

if (isset($_POST['Daftar'])) {

    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = $koneksi->prepare("INSERT INTO user (nama, email, password) VALUES (?, ?, ?)");
    $query->bind_param("sss", $nama, $email, $password);

    if ($query->execute()) {
        echo "<script>alert('Registrasi Berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Registrasi Gagal!');</script>";
    }
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h2>Daftar</h2>
        <form method="POST">
            <div class="input-group">
                <input type="text" name="nama" placeholder="Nama Lengkap" required>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" name="Daftar">Daftar</button>
            <a href="dashboard.php" class="btn-kembali">Kembali</a>
            <p class="text-link">Sudah punya akun? <a href="login.php">Login sekarang</a></p>
        </form>
    </div>
</body>

</html>