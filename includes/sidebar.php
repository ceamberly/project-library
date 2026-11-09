<?php
// Sidebar + topbar; isi $halaman untuk menandai menu aktif
$halaman = $halaman ?? '';
$aktif = fn(string $k): string => $halaman === $k ? 'active' : '';
$nama = $_SESSION['nama'] ?? 'Admin';
?>
<div class="app">
  <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar">
    <div class="sidebar-inner">
      <div class="sidebar-brand">
        <i class="bi bi-book-half"></i>
        <div><strong>Shelfwise</strong><small>Digital Library</small></div>
      </div>
      <nav>
        <a class="nav-item-ios <?= $aktif('dashboard') ?>" href="<?= BASE_URL ?>/pages/dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
        <p class="nav-label">Manajemen</p>
        <a class="nav-item-ios <?= $aktif('buku') ?>" href="<?= BASE_URL ?>/pages/books.php"><i class="bi bi-book"></i> Buku</a>
        <?php if (isAdmin()): ?>
        <a class="nav-item-ios <?= $aktif('tambah') ?>" href="<?= BASE_URL ?>/pages/add-book.php"><i class="bi bi-plus-lg"></i> Tambah Buku</a>
        <?php endif; ?>
        <hr class="my-3" style="border-color: var(--line); opacity: 1;">
        <button type="button" class="nav-item-ios nav-btn d-lg-none" data-toggle-tema><i class="bi bi-circle-half"></i> Ganti tema</button>
        <a class="nav-item-ios" href="<?= BASE_URL ?>/process/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
      </nav>
    </div>
  </aside>

  <div class="app-main">
    <header class="topbar">
      <button class="menu-btn d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Menu">
        <i class="bi bi-list"></i>
      </button>
      <form class="search" action="<?= BASE_URL ?>/pages/books.php" method="get" data-cari="<?= BASE_URL ?>/process/cari.php" autocomplete="off">
        <i class="bi bi-search"></i>
        <input type="search" name="q" placeholder="Cari buku..." value="<?= e($_GET["q"] ?? "") ?>">
        <div class="search-results" id="hasil-cari" hidden></div>
        <?php if (!empty($filterBuku)) require __DIR__ . "/filter-buku.php"; ?>
      </form>
      <button type="button" class="tema-btn ms-auto d-none d-lg-flex" data-toggle-tema aria-label="Ganti tema">
        <i class="bi bi-moon icon-moon"></i><i class="bi bi-sun icon-sun"></i>
      </button>
      <a class="user-chip" href="<?= BASE_URL ?>/pages/profile.php" aria-label="Profil">
        <span class="avatar"><?php if (!empty($_SESSION['foto'])): ?><img src="<?= BASE_URL ?>/uploads/<?= rawurlencode($_SESSION['foto']) ?>" alt=""><?php else: ?><?= htmlspecialchars(strtoupper(substr($nama, 0, 1))) ?><?php endif; ?></span>
        <span class="d-none d-sm-inline"><?= htmlspecialchars($nama) ?></span>
      </a>
    </header>