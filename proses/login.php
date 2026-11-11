<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/function.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/login.php');
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Cari user, lalu cocokkan password dengan hash
$stmt = $pdo->prepare('SELECT id, username, password, nama, role, foto FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    setFlash('danger', 'Username atau password salah.');
    redirect('/login.php');
}

// Login berhasil: ganti ID session, simpan identitas
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['nama'] = $user['nama'];
$_SESSION['role'] = $user['role'];
$_SESSION['foto'] = $user['foto'];

// Remember me: simpan username 30 hari, atau hapus kalau tidak dicentang
if (isset($_POST['ingat'])) {
    setcookie('ingat_username', $user['username'], [
        'expires'  => time() + 60 * 60 * 24 * 30,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
} else {
    setcookie('ingat_username', '', time() - 3600, '/');
}

redirect('/pages/dashboard.php');