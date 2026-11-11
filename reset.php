<?php
require 'config/database.php';

$password = password_hash('admin123', PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->execute([$password]);

echo "Password admin berhasil diubah!";
?>