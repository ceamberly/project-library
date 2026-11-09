<?php
require __DIR__ . '/../includes/auth.php';
requireAdmin();
$judul = 'Edit Buku';
$halaman = 'buku';
$flash = getFlash();

// Ambil buku yang akan diedit
$idBuku = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM buku WHERE id = ?');
$stmt->execute([$idBuku]);
$buku = $stmt->fetch();
if (!$buku) {
    setFlash('danger', 'Buku tidak ditemukan.');
    redirect('/pages/books.php');
}

// Isian sebelumnya (kalau validasi gagal) menimpa data dari database
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
$data = array_merge($buku, $old);
$aksi = BASE_URL . '/process/edit.php';
$sampulLama = $buku['sampul'];
$tombol = 'Simpan Perubahan';

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>
<div class="mb-3">
  <h1 class="page-title">Edit Buku</h1>
</div>

<?php if ($flash): ?>
  <div class="alert-ios <?= e($flash['type']) ?>" role="alert"><?= e($flash['msg']) ?></div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/form-buku.php'; ?>
<?php
require __DIR__ . '/../includes/layout_end.php';
require __DIR__ . '/../includes/footer.php';