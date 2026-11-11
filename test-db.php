
<?php

$host = '127.0.0.1';
$db   = 'project-library';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

    echo "KONEKSI DATABASE BERHASIL!";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
