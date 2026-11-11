<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/function.php';
$judul = 'Masuk';
$flash = getFlash();
$ingatUser = $_COOKIE['ingat_username'] ?? '';
require __DIR__ . '/includes/header.php';
?>
<main class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
  <div class="w-100" style="max-width: 420px;">
    <div class="card-ios text-center">
      <i class="bi bi-book-half brand-glyph"></i>
      <h1 class="h2 fw-bold mt-2 mb-0">Shelfwise</h1>
      <p class="text-muted-ios mb-0">Digital Library</p>
      <div class="divider-dot"><span></span></div>
      <?php if ($flash): ?>
        <div class="alert-ios <?= htmlspecialchars($flash['type']) ?>" role="alert">
          <?= htmlspecialchars($flash['msg']) ?>
        </div>
      <?php endif; ?>
      <form action="<?= BASE_URL ?>/proses/login.php" method="post" class="text-start">
        <label class="field">
          <i class="bi bi-person"></i>
          <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($ingatUser) ?>" required <?= $ingatUser ? '' : 'autofocus' ?>>
        </label>
        <label class="field">
          <i class="bi bi-lock"></i>
          <input type="password" id="password" name="password" placeholder="Password" required <?= $ingatUser ? 'autofocus' : '' ?>>
          <button type="button" class="btn p-0 border-0" data-toggle-password="#password">
            <i class="bi bi-eye-slash"></i>
          </button>
        </label>
        <div class="form-check my-3">
          <input class="form-check-input" type="checkbox" id="ingat" name="ingat" <?= $ingatUser ? 'checked' : '' ?>>
          <label class="form-check-label" for="ingat">Ingat saya</label>
        </div>
        <button type="submit" class="btn-accent">Masuk</button>
      </form>
      <p class="text-muted-ios small mt-4 mb-0">Perpustakaan Digital Mini</p>
    </div>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>