<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "fahd_ukk";

$koneksi = new mysqli($host,$user,$pass,$db);

if ($koneksi->connect_error){
    die("koneksi ke database gagal:" .$koneksi->connect_error);
}
?>