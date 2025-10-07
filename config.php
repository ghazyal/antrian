<?php
$host = 'localhost';
$user = 'root';        // default user XAMPP
$pass = '';            // kosong kalau default XAMPP
$db   = 'antrian_db'; // nama database sesuai yang kamu import dari db.sql

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}
?>
