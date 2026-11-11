<?php
require __DIR__ . '/../includes/auth.php';
requireAdmin();
$judul = 'Tambah Buku';
$halaman = 'tambah';
$flash = getFlash();

// Isian sebelumnya kalau validasi gagal; stok default 1
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
$data = $old + ['stok' => '1'];
$aksi = BASE_URL . '/proses/tambah.php';
$sampulLama = null;
$idBuku = null;

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>
<div class="mb-3">
  <h1 class="page-title">Tambah Buku</h1>
</div>

<?php if ($flash): ?>
  <div class="alert-ios <?= e($flash['type']) ?>" role="alert"><?= e($flash['msg']) ?></div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/form-buku.php'; ?>
<?php
require __DIR__ . '/../includes/layout_end.php';
require __DIR__ . '/../includes/footer.php';