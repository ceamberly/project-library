<?php // Modal konfirmasi hapus buku; diisi lewat JS dari tombol .btn-hapus ?>
<div class="modal fade" id="modal-hapus" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content modal-ios" action="<?= BASE_URL ?>/process/hapus.php" method="post">
      <input type="hidden" name="id" id="hapus-id">
      <div class="modal-body text-center">
        <div class="modal-icon"><i class="bi bi-trash"></i></div>
        <h2 class="h5 fw-bold">Hapus buku?</h2>
        <p class="text-muted-ios mb-0">Buku "<span id="hapus-judul"></span>" akan dihapus beserta sampulnya. Tindakan ini tidak bisa dibatalkan.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-soft" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn-bahaya">Hapus</button>
      </div>
    </form>
  </div>
</div>