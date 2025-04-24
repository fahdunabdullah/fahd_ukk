<?php
session_start();
include __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Masuk'])) {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $query = $koneksi->prepare("SELECT * FROM user WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'nama' => $user['nama'],
                    'email' => $user['email'],
                    'role' => ($user['role']) ? $user['role'] : 'user'
                ];

                echo "<script>alert('Masuk Berhasil!'); window.location='../dashboard/index.php';</script>";
            } else {
                echo "<script>alert('Password salah!');</script>";
            }
        } else {
            echo "<script>alert('Email tidak ditemukan!');</script>";
        }
    } else {
        echo "<script>alert('Email dan Password wajib diisi!');</script>";
    }
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h2>Masuk</h2>
        <form method="POST">
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" name="Masuk">Masuk</button>
            <a href="dashboard.php" class="btn-kembali">Kembali</a>
            <p class="text-link">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
        </form>
    </div>
</body>

</html>