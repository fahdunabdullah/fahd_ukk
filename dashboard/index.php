<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/dashboard.php");
    exit;
}

if ($_SESSION['user']['role'] === 'admin') {
    header("Location: admin.php");
    exit;
} elseif ($_SESSION['user']['role'] === 'petugas') {
    header("Location: petugas.php");
    exit;
} else {
    header("Location: user.php");
    exit;
}
?>
