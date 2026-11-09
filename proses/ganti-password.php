<?php
require __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/profile.php');
}

$lama  = $_POST['lama'] ?? '';
$baru  = $_POST['baru'] ?? '';
$ulang = $_POST['ulang'] ?? '';

// Ambil hash password saat ini
$stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$hash = $stmt->fetchColumn();

// Validasi
$error = null;
if (!$hash || !password_verify($lama, $hash)) {
    $error = 'Password saat ini salah.';
} elseif (strlen($baru) < 8) {
    $error = 'Password baru minimal 8 karakter.';
} elseif ($baru !== $ulang) {
    $error = 'Konfirmasi password tidak cocok.';
} elseif ($baru === $lama) {
    $error = 'Password baru harus berbeda dari yang lama.';
}

if ($error) {
    setFlash('danger', $error);
    redirect('/pages/profile.php');
}

// Simpan sebagai hash, lalu perbarui ID session
$pdo->prepare('UPDATE users SET password = ? WHERE id = ?')
    ->execute([password_hash($baru, PASSWORD_DEFAULT), $_SESSION['user_id']]);
session_regenerate_id(true);

setFlash('success', 'Password berhasil diganti.');
redirect('/pages/profile.php');