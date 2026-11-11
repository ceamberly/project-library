
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

// Label status dari stok
function statusBuku(int $stok): array
{
    if ($stok > 0) {
        return ['Tersedia', 'ok'];
    }

    return ['Dipinjam', 'warn'];
}

// Pengaturan upload gambar
const SAMPUL_MAKS = 2 * 1024 * 1024;
const SAMPUL_TIPE = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png'
];

// Proses upload sampul buku
function uploadSampul(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }

    if (
        $file['error'] === UPLOAD_ERR_INI_SIZE ||
        $file['error'] === UPLOAD_ERR_FORM_SIZE
    ) {
        return [null, 'Ukuran sampul maksimal 2MB.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'Upload sampul gagal. Kode error: ' . $file['error']];
    }

    if ($file['size'] > SAMPUL_MAKS) {
        return [null, 'Ukuran sampul maksimal 2MB.'];
    }

    // Periksa tipe file gambar
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!isset(SAMPUL_TIPE[$mime])) {
        return [null, 'Sampul harus berupa gambar JPG atau PNG.'];
    }

    // Lokasi folder uploads
    $folder = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads';

    // Buat folder otomatis jika belum tersedia
    if (!is_dir($folder)) {
        if (!mkdir($folder, 0775, true) && !is_dir($folder)) {
            return [null, 'Folder uploads gagal dibuat.'];
        }
    }

    // Periksa apakah folder bisa ditulisi
    if (!is_writable($folder)) {
        return [null, 'Folder uploads tidak bisa ditulisi.'];
    }

    // Buat nama file acak
    $nama = bin2hex(random_bytes(8)) . '.' . SAMPUL_TIPE[$mime];

    $tujuan = $folder . DIRECTORY_SEPARATOR . $nama;

    // Pindahkan file ke folder uploads
    if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
        return [null, 'PHP gagal memindahkan file ke folder uploads.'];
    }

    return [$nama, null];
}

// Validasi isian buku
function validasiBuku(array $in): array
{
    $data = [
        'judul'        => trim($in['judul'] ?? ''),
        'penulis'      => trim($in['penulis'] ?? ''),
        'kategori'     => trim($in['kategori'] ?? ''),
        'tahun_terbit' => (int) ($in['tahun_terbit'] ?? 0),
        'stok'         => filter_var(
            $in['stok'] ?? '',
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 0, 'max_range' => 9999]]
        ),
    ];

    if (
        $data['judul'] === '' ||
        $data['penulis'] === '' ||
        $data['kategori'] === ''
    ) {
        return [null, 'Judul, penulis, dan kategori wajib diisi.'];
    }

    if (!in_array($data['kategori'], KATEGORI, true)) {
        return [null, 'Pilih kategori dari daftar yang tersedia.'];
    }

    if (
        $data['tahun_terbit'] < 1901 ||
        $data['tahun_terbit'] > (int) date('Y') + 1
    ) {
        return [null, 'Tahun terbit tidak valid.'];
    }

    if ($data['stok'] === false) {
        return [null, 'Stok harus berupa angka 0 sampai 9999.'];
    }

    return [$data, null];
}

// Hapus file sampul dari folder uploads
function hapusSampul(?string $nama): void
{
    if (!$nama) {
        return;
    }

    $path = dirname(__DIR__) . '/uploads/' . basename($nama);

    if (is_file($path)) {
        unlink($path);
    }
}

// Peran pengguna dari session
function isAdmin(): bool
{
    return ($_SESSION['role'] ?? '') === 'admin';
}

// Halaman khusus admin
function requireAdmin(): void
{
    if (!isAdmin()) {
        setFlash('warning', 'Halaman itu khusus admin.');
        redirect('/pages/books.php');
    }
}

// Daftar kategori buku
const KATEGORI = [
    'Novel',
    'Sastra',
    'Teknologi',
    'Pengembangan Diri',
    'Psikologi',
    'Sains',
    'Sejarah',
    'Pendidikan',
    'Lainnya',
];

// Upload foto profil
function uploadFoto(array $file): array
{
    [$nama, $error] = uploadSampul($file);

    if ($error) {
        $error = str_replace(
            ['sampul', 'Sampul'],
            ['foto', 'Foto'],
            $error
        );
    }

    return [$nama, $error];
}
