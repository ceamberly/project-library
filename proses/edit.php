<?php
require __DIR__ . '/../includes/auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/books.php');
}

// Pastikan buku ada
$id = (int) ($_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT id, sampul FROM buku WHERE id = ?');
$stmt->execute([$id]);
$lama = $stmt->fetch();
if (!$lama) {
    setFlash('danger', 'Buku tidak ditemukan.');
    redirect('/pages/books.php');
}

// Validasi isian, lalu sampul baru (kalau ada)
[$buku, $error] = validasiBuku($_POST);
$baru = null;
if (!$error) {
    [$baru, $error] = uploadSampul($_FILES['sampul'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
}
if ($error) {
    $_SESSION['old'] = $_POST;
    setFlash('danger', $error);
    redirect('/pages/edit-book.php?id=' . $id);
}

// Update; pakai sampul baru kalau ada, kalau tidak pertahankan yang lama
try {
    $stmt = $pdo->prepare('UPDATE buku SET judul = ?, kategori = ?, penulis = ?, tahun_terbit = ?, stok = ?, sampul = ? WHERE id = ?');
    $stmt->execute([$buku['judul'], $buku['kategori'], $buku['penulis'], $buku['tahun_terbit'], $buku['stok'], $baru ?? $lama['sampul'], $id]);
} catch (PDOException $e) {
    hapusSampul($baru);
    $_SESSION['old'] = $_POST;
    setFlash('danger', 'Gagal menyimpan perubahan, coba lagi.');
    redirect('/pages/edit-book.php?id=' . $id);
}

// Hapus file sampul lama setelah database berhasil diperbarui
if ($baru) {
    hapusSampul($lama['sampul']);
}

setFlash('success', 'Perubahan buku "' . $buku['judul'] . '" disimpan.');
redirect('/pages/books.php');