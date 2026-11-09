<?php
require __DIR__ . '/../includes/auth.php';
$judul = 'Buku';
$halaman = 'buku';
$filterBuku = true;
$admin = isAdmin();
$flash = getFlash();

// Cari berdasarkan judul, penulis, atau kategori
$q = trim($_GET['q'] ?? '');
$kat = trim($_GET['kategori'] ?? '');
$where = [];
$param = [];
if ($q !== '') {
    $like = '%' . $q . '%';
    $where[] = '(judul LIKE ? OR penulis LIKE ? OR kategori LIKE ?)';
    array_push($param, $like, $like, $like);
}
if ($kat !== '') {
    $where[] = 'kategori = ?';
    $param[] = $kat;
}

// Filter status dari stok (hanya nilai yang diizinkan)
$status = $_GET['status'] ?? '';
if (!in_array($status, ['tersedia', 'dipinjam'], true)) {
    $status = '';
}
if ($status === 'tersedia') {
    $where[] = 'stok > 0';
} elseif ($status === 'dipinjam') {
    $where[] = 'stok = 0';
}
$sql = 'SELECT * FROM buku' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($param);
$daftar = $stmt->fetchAll();
$kosong = ($q !== '' || $kat !== '' || $status !== '') ? 'Buku tidak ditemukan.' : 'Belum ada buku.';

// Jumlah buku per kategori (seluruh koleksi)
$kondisi = ['tersedia' => ' WHERE stok > 0', 'dipinjam' => ' WHERE stok = 0'][$status] ?? '';
$kategoriList = $pdo->query('SELECT kategori, COUNT(*) AS jml FROM buku' . $kondisi . ' GROUP BY kategori ORDER BY jml DESC, kategori')->fetchAll();
$total = array_sum(array_column($kategoriList, 'jml'));

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<?php if ($flash): ?>
  <div class="alert-ios <?= e($flash['type']) ?>" role="alert"><?= e($flash['msg']) ?></div>
<?php endif; ?>

<div class="panel" id="hasil-buku">

  <!-- Layar lebar: tabel -->
  <div class="table-responsive d-none d-md-block">
    <table class="table table-ios table-hover align-middle">
      <thead>
        <tr>
          <th>Buku</th><th>Kategori</th><th>Tahun</th><th>Stok</th><th>Status</th>
          <?php if ($admin): ?><th class="text-end">Aksi</th><?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (!$daftar): ?>
          <tr><td colspan="<?= $admin ? 6 : 5 ?>" class="text-center text-muted-ios py-4"><?= $kosong ?></td></tr>
        <?php endif; ?>
        <?php foreach ($daftar as $b): [$label, $warna] = statusBuku((int) $b['stok']); ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-3">
                <?php if ($b['sampul']): ?>
                  <img class="cover" src="<?= BASE_URL ?>/uploads/<?= rawurlencode($b['sampul']) ?>" alt="">
                <?php else: ?>
                  <div class="cover"><i class="bi bi-book"></i></div>
                <?php endif; ?>
                <div>
                  <p class="book-title m-0"><?= e($b['judul']) ?></p>
                  <p class="book-meta m-0"><?= e($b['penulis']) ?></p>
                </div>
              </div>
            </td>
            <td><?= e($b['kategori']) ?></td>
            <td><?= e((string) $b['tahun_terbit']) ?></td>
            <td><?= (int) $b['stok'] ?></td>
            <td><span class="pill <?= $warna ?>"><?= $label ?></span></td>
            <?php if ($admin): ?>
            <td class="text-end text-nowrap">
              <a class="icon-btn" href="<?= BASE_URL ?>/pages/edit-book.php?id=<?= (int) $b['id'] ?>" aria-label="Edit"><i class="bi bi-pencil"></i></a>
              <button type="button" class="icon-btn danger btn-hapus" data-id="<?= (int) $b['id'] ?>" data-judul="<?= e($b['judul']) ?>" aria-label="Hapus"><i class="bi bi-trash"></i></button>
            </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- HP: baris yang bisa digeser -->
  <div class="d-md-none">
    <?php if (!$daftar): ?>
      <p class="text-center text-muted-ios py-3 m-0"><?= $kosong ?></p>
    <?php else: ?>
      <?php if ($admin): ?><p class="text-muted-ios small mb-2"><i class="bi bi-arrow-left-right"></i> Swipe to edit</p><?php endif; ?>
    <?php endif; ?>
    <?php foreach ($daftar as $b): [$label, $warna] = statusBuku((int) $b['stok']); ?>
      <div class="swipe">
        <div class="swipe-content">
          <?php if ($b['sampul']): ?>
            <img class="cover" src="<?= BASE_URL ?>/uploads/<?= rawurlencode($b['sampul']) ?>" alt="">
          <?php else: ?>
            <div class="cover"><i class="bi bi-book"></i></div>
          <?php endif; ?>
          <div class="book-info">
            <p class="book-title text-truncate m-0"><?= e($b['judul']) ?></p>
            <p class="book-meta text-truncate m-0"><?= e($b['penulis']) ?> · <?= e((string) $b['tahun_terbit']) ?></p>
          </div>
          <span class="pill <?= $warna ?>"><?= $label ?></span>
        </div>
        <?php if ($admin): ?>
        <div class="swipe-actions">
          <a class="edit" href="<?= BASE_URL ?>/pages/edit-book.php?id=<?= (int) $b['id'] ?>"><i class="bi bi-pencil"></i>Edit</a>
          <button type="button" class="hapus btn-hapus" data-id="<?= (int) $b['id'] ?>" data-judul="<?= e($b['judul']) ?>"><i class="bi bi-trash"></i>Hapus</button>
        </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

</div>
<?php
if ($admin) {
    require __DIR__ . '/../includes/modal-hapus.php';
}
require __DIR__ . '/../includes/layout_end.php';
require __DIR__ . '/../includes/footer.php';