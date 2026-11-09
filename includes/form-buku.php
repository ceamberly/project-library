<?php
// Form buku bersama (tambah dan edit)
// Butuh: $aksi, $data, $sampulLama, $idBuku; opsional $tombol
$tombol = $tombol ?? 'Simpan';
$fKategori = $data['kategori'] ?? '';
?>
<form class="panel form-buku" action="<?= e($aksi) ?>" method="post" enctype="multipart/form-data">
  <?php if ($idBuku): ?>
    <input type="hidden" name="id" value="<?= (int) $idBuku ?>">
  <?php endif; ?>

  <div class="form-grid">
    <div class="sampul-blok">
      <label class="upload" for="sampul">
        <img id="preview" alt="Pratinjau sampul" <?= $sampulLama ? 'src="' . e(BASE_URL . '/uploads/' . rawurlencode($sampulLama)) . '"' : 'hidden' ?>>
        <span class="upload-hint"><i class="bi bi-image"></i><small>Pilih sampul<br>JPG/PNG, maks 2MB</small></span>
      </label>
      <div class="sampul-info d-md-none">
        <p class="sampul-judul">Sampul buku</p>
        <p class="form-hint">JPG atau PNG, maksimal 2MB.</p>
        <label for="sampul" class="btn-soft btn-pilih">Pilih gambar</label>
      </div>
      <input type="file" id="sampul" name="sampul" accept="image/jpeg,image/png" hidden>
    </div>

    <div class="row g-3">
      <div class="col-12">
        <label class="form-label-ios" for="judul">Judul Buku</label>
        <div class="field"><input id="judul" name="judul" maxlength="150" value="<?= e($data['judul'] ?? '') ?>" required></div>
      </div>
      <div class="col-md-6">
        <label class="form-label-ios" for="penulis">Penulis</label>
        <div class="field"><input id="penulis" name="penulis" maxlength="100" value="<?= e($data['penulis'] ?? '') ?>" required></div>
      </div>
      <div class="col-md-6">
        <label class="form-label-ios" for="kategori">Kategori</label>
        <div class="field">
          <select id="kategori" name="kategori" required>
            <option value="" disabled <?= in_array($fKategori, KATEGORI, true) ? '' : 'selected' ?>>Pilih kategori</option>
            <?php foreach (KATEGORI as $fOpsi): ?>
              <option <?= $fKategori === $fOpsi ? 'selected' : '' ?>><?= e($fOpsi) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="col-6">
        <label class="form-label-ios" for="tahun_terbit">Tahun Terbit</label>
        <div class="field"><input type="number" id="tahun_terbit" name="tahun_terbit" min="1901" max="<?= date('Y') + 1 ?>" value="<?= e((string) ($data['tahun_terbit'] ?? '')) ?>" required></div>
      </div>
      <div class="col-6">
        <label class="form-label-ios" for="stok">Stok</label>
        <div class="field"><input type="number" id="stok" name="stok" min="0" max="9999" value="<?= e((string) ($data['stok'] ?? '')) ?>" required></div>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <a class="btn-soft" href="<?= BASE_URL ?>/pages/books.php">Batal</a>
    <button type="submit" class="btn-main"><i class="bi bi-check-lg"></i> <?= e($tombol) ?></button>
  </div>
</form>