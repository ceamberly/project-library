<?php
require __DIR__ . '/../includes/auth.php';
requireAdmin();

// Hanya menerima POST supaya tidak bisa terhapus lewat alamat biasa
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/books.php');
}

// Pastikan buku ada
$id = (int) ($_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT judul, sampul FROM buku WHERE id = ?');
$stmt->execute([$id]);
$buku = $stmt->fetch();
if (!$buku) {
    setFlash('danger', 'Buku tidak ditemukan.');
    redirect('/pages/books.php');
}

// Hapus data dulu, baru file sampulnya
try {
    $pdo->prepare('DELETE FROM buku WHERE id = ?')->execute([$id]);
} catch (PDOException $e) {
    setFlash('danger', 'Gagal menghapus buku, coba lagi.');
    redirect('/pages/books.php');
}
hapusSampul($buku['sampul']);

setFlash('success', 'Buku "' . $buku['judul'] . '" dihapus.');
redirect('/pages/books.php');