<?php // Kartu statistik bersama; butuh $total, $tersedia, $dipinjam ?>
<div class="row g-3 mb-4">
  <div class="col-4">
    <div class="panel stat">
      <div class="stat-icon tone-blue"><i class="bi bi-book"></i></div>
      <div><div class="stat-num"><?= $total ?></div><div class="stat-label">Total Buku</div></div>
    </div>
  </div>
  <div class="col-4">
    <div class="panel stat">
      <div class="stat-icon tone-green"><i class="bi bi-check-lg"></i></div>
      <div><div class="stat-num"><?= $tersedia ?></div><div class="stat-label">Buku Tersedia</div></div>
    </div>
  </div>
  <div class="col-4">
    <div class="panel stat">
      <div class="stat-icon tone-orange"><i class="bi bi-clock"></i></div>
      <div><div class="stat-num"><?= $dipinjam ?></div><div class="stat-label">Buku Dipinjam</div></div>
    </div>
  </div>
</div>