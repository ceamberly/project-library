<?php
// Fungsi bantu bersama; juga memulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pindah halaman
function redirect(string $path): never
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

// Simpan pesan sekali tampil
function setFlash(string $type, string $msg): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

// Ambil pesan lalu hapus
function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// Escape teks untuk ditampilkan di HTML
function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

// Label status dari stok: [teks, kelas warna]
function statusBuku(int $stok): array
{
    if ($stok > 0) {
        return ['Tersedia', 'ok'];
    }
    return ['Dipinjam', 'warn'];
}

const SAMPUL_MAKS = 2 * 1024 * 1024;
const SAMPUL_TIPE = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

// Proses upload sampul; hasil [nama file atau null, pesan error atau null]
function uploadSampul(array $file): array
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        return [null, 'Ukuran sampul maksimal 2MB.'];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'Upload sampul gagal, coba lagi.'];
    }
    if ($file['size'] > SAMPUL_MAKS) {
        return [null, 'Ukuran sampul maksimal 2MB.'];
    }

    // Cek tipe dari isi file, bukan dari ekstensi
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset(SAMPUL_TIPE[$mime])) {
        return [null, 'Sampul harus berupa gambar JPG atau PNG.'];
    }

    // Nama acak supaya tidak bentrok dan tidak bisa ditebak
    $nama = bin2hex(random_bytes(8)) . '.' . SAMPUL_TIPE[$mime];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../uploads/' . $nama)) {
        return [null, 'Gagal menyimpan file sampul.'];
    }
    return [$nama, null];
}

// Validasi isian buku; hasil [data bersih atau null, pesan error atau null]
function validasiBuku(array $in): array
{
    $data = [
        'judul'        => trim($in['judul'] ?? ''),
        'penulis'      => trim($in['penulis'] ?? ''),
        'kategori'     => trim($in['kategori'] ?? ''),
        'tahun_terbit' => (int) ($in['tahun_terbit'] ?? 0),
        'stok'         => filter_var($in['stok'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9999]]),
    ];
    if ($data['judul'] === '' || $data['penulis'] === '' || $data['kategori'] === '') {
        return [null, 'Judul, penulis, dan kategori wajib diisi.'];
    }
    if (!in_array($data['kategori'], KATEGORI, true)) {
        return [null, 'Pilih kategori dari daftar yang tersedia.'];
    }
    if ($data['tahun_terbit'] < 1901 || $data['tahun_terbit'] > (int) date('Y') + 1) {
        return [null, 'Tahun terbit tidak valid.'];
    }
    if ($data['stok'] === false) {
        return [null, 'Stok harus berupa angka 0 sampai 9999.'];
    }
    return [$data, null];
}

// Hapus file sampul dari folder uploads (basename mencegah path berbahaya)
function hapusSampul(?string $nama): void
{
    if (!$nama) {
        return;
    }
    $path = __DIR__ . '/../uploads/' . basename($nama);
    if (is_file($path)) {
        unlink($path);
    }
}

// Peran pengguna dari session
function isAdmin(): bool
{
    return ($_SESSION['role'] ?? '') === 'admin';
}

// Guard: halaman dan proses khusus admin
function requireAdmin(): void
{
    if (!isAdmin()) {
        setFlash('warning', 'Halaman itu khusus admin.');
        redirect('/pages/books.php');
    }
}

// Daftar kategori yang boleh dipakai (ubah di sini kalau perlu)
const KATEGORI = [
    'Novel', 'Sastra', 'Teknologi', 'Pengembangan Diri', 'Psikologi',
    'Sains', 'Sejarah', 'Pendidikan', 'Lainnya',
];

// Upload foto profil: memakai uploadSampul dengan pesan error yang disesuaikan
function uploadFoto(array $file): array
{
    [$nama, $error] = uploadSampul($file);
    if ($error) {
        $error = str_replace(['sampul', 'Sampul'], ['foto', 'Foto'], $error);
    }
    return [$nama, $error];
}