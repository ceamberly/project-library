<?php
require __DIR__ . '/../includes/auth.php';

// Hasil pencarian singkat (maks 5) dalam JSON untuk dropdown
$q = trim($_GET['q'] ?? '');
$hasil = [];

if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $pdo->prepare('SELECT judul, penulis, tahun_terbit, stok FROM buku WHERE judul LIKE ? OR penulis LIKE ? OR kategori LIKE ? ORDER BY id DESC LIMIT 5');
    $stmt->execute([$like, $like, $like]);
    foreach ($stmt->fetchAll() as $b) {
        [$label, $warna] = statusBuku((int) $b['stok']);
        $hasil[] = [
            'judul'   => $b['judul'],
            'penulis' => $b['penulis'],
            'tahun'   => $b['tahun_terbit'],
            'status'  => $label,
            'warna'   => $warna,
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($hasil);