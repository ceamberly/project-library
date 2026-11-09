<?php
require __DIR__ . '/../includes/auth.php';
$judul = 'Profil';
$halaman = 'profil';
$flash = getFlash();

// Data akun yang sedang login
$stmt = $pdo->prepare('SELECT username, nama, role, npm, email, telepon, foto, created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$akun = $stmt->fetch();
if (!$akun) {
    redirect('/process/logout.php');
}
$admin = $akun['role'] === 'admin';

// Isian sebelumnya (kalau validasi gagal) menimpa data dari database
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
$data = array_merge($akun, $old);

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<?php if ($flash): ?>
  <div class="alert-ios <?= e($flash['type']) ?>" role="alert"><?= e($flash['msg']) ?></div>
<?php endif; ?>

<form class="panel mb-3" action="<?= BASE_URL ?>/process/profil.php" method="post" enctype="multipart/form-data">
  <h2 class="h5 fw-bold mb-3">Data diri</h2>
  <div class="profil-grid">
    <div class="foto-blok">
      <label class="foto-bulat" for="foto">
        <?= e(strtoupper(substr($akun['nama'], 0, 1))) ?>
        <img id="foto-preview" alt="Foto profil" <?= $akun['foto'] ? 'src="' . e(BASE_URL . '/uploads/' . rawurlencode($akun['foto'])) . '"' : 'hidden' ?>>
      </label>
      <label for="foto" class="btn-soft btn-pilih">Ganti foto</label>
      <input type="file" id="foto" name="foto" accept="image/jpeg,image/png" hidden>
      <span class="badge-peran"><?= $admin ? 'Admin' : 'Pengunjung' ?></span>
      <span class="text-muted-ios small">Bergabung <?= e(date('d/m/Y', strtotime($akun['created_at']))) ?></span>
    </div>

    <div class="row g-3">
      <div class="col-12">
        <label class="form-label-ios" for="nama">Nama lengkap</label>
        <div class="field"><input id="nama" name="nama" maxlength="100" value="<?= e($data['nama'] ?? '') ?>" required></div>
      </div>
      <div class="col-md-6">
        <label class="form-label-ios" for="username">Username</label>
        <div class="field"><input id="username" name="username" maxlength="50" pattern="[A-Za-z0-9_.]{3,50}" title="3 sampai 50 karakter: huruf, angka, titik, atau garis bawah" value="<?= e($data['username'] ?? '') ?>" required></div>
      </div>
      <?php if ($admin): ?>
        <div class="col-md-6">
          <label class="form-label-ios" for="telepon">No. HP <span class="text-muted-ios">(opsional)</span></label>
          <div class="field"><input id="telepon" name="telepon" maxlength="20" inputmode="tel" value="<?= e($data['telepon'] ?? '') ?>"></div>
        </div>
        <div class="col-12">
          <label class="form-label-ios" for="email">Email <span class="text-muted-ios">(opsional)</span></label>
          <div class="field"><input type="email" id="email" name="email" maxlength="100" value="<?= e($data['email'] ?? '') ?>"></div>
        </div>
      <?php else: ?>
        <div class="col-md-6">
          <label class="form-label-ios" for="npm">NPM</label>
          <div class="field"><input id="npm" name="npm" maxlength="15" inputmode="numeric" pattern="[0-9]{6,15}" title="Angka, 6 sampai 15 digit" value="<?= e($data['npm'] ?? '') ?>" required></div>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="form-actions">
    <button type="submit" class="btn-main"><i class="bi bi-check-lg"></i> Simpan perubahan</button>
  </div>
</form>

<form class="panel" action="<?= BASE_URL ?>/process/ganti-password.php" method="post">
  <h2 class="h5 fw-bold mb-3">Ganti password</h2>
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label-ios" for="pw-lama">Password saat ini</label>
      <label class="field">
        <i class="bi bi-lock"></i>
        <input type="password" id="pw-lama" name="lama" autocomplete="current-password" required>
        <button type="button" class="btn p-0 border-0" data-toggle-password="#pw-lama"><i class="bi bi-eye-slash"></i></button>
      </label>
    </div>
    <div class="col-md-4">
      <label class="form-label-ios" for="pw-baru">Password baru</label>
      <label class="field">
        <i class="bi bi-key"></i>
        <input type="password" id="pw-baru" name="baru" minlength="8" autocomplete="new-password" required>
        <button type="button" class="btn p-0 border-0" data-toggle-password="#pw-baru"><i class="bi bi-eye-slash"></i></button>
      </label>
    </div>
    <div class="col-md-4">
      <label class="form-label-ios" for="pw-ulang">Ulangi password baru</label>
      <label class="field">
        <i class="bi bi-key"></i>
        <input type="password" id="pw-ulang" name="ulang" minlength="8" autocomplete="new-password" required>
        <button type="button" class="btn p-0 border-0" data-toggle-password="#pw-ulang"><i class="bi bi-eye-slash"></i></button>
      </label>
    </div>
    <div class="col-12"><p class="form-hint m-0">Minimal 8 karakter dan berbeda dari password saat ini.</p></div>
  </div>
  <div class="form-actions">
    <button type="submit" class="btn-main"><i class="bi bi-check-lg"></i> Simpan password</button>
  </div>
</form>
<?php
require __DIR__ . '/../includes/layout_end.php';
require __DIR__ . '/../includes/footer.php';