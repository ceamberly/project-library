<?php
require __DIR__ . '/../includes/auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/add-book.php');
}

// Validasi isian, lalu sampul
[$buku, $error] = validasiBuku($_POST);
$sampul = null;
if (!$error) {
    [$sampul, $error] = uploadSampul($_FILES['sampul'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
}

if ($error) {
    $_SESSION['old'] = $_POST;
    setFlash('danger', $error);
    redirect('/pages/add-book.php');
}

// Simpan ke database; hapus file kalau query gagal
try {
    $stmt = $pdo->prepare('INSERT INTO buku (judul, kategori, penulis, tahun_terbit, stok, sampul) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$buku['judul'], $buku['kategori'], $buku['penulis'], $buku['tahun_terbit'], $buku['stok'], $sampul]);
} catch (PDOException $e) {
    hapusSampul($sampul);
    $_SESSION['old'] = $_POST;
    setFlash('danger', 'Gagal menyimpan buku, coba lagi.');
    redirect('/pages/add-book.php');
}

setFlash('success', 'Buku "' . $buku['judul'] . '" berhasil ditambahkan.');
redirect('/pages/books.php');