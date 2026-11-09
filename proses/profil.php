<?php
require __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/profile.php');
}

// Akun yang sedang login
$id = (int) $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT role, foto FROM users WHERE id = ?');
$stmt->execute([$id]);
$akun = $stmt->fetch();
if (!$akun) {
    redirect('/process/logout.php');
}
$admin = $akun['role'] === 'admin';

$nama     = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$npm      = trim($_POST['npm'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telepon  = trim($_POST['telepon'] ?? '');

// Validasi isian
$error = null;
if ($nama === '' || strlen($nama) > 100) {
    $error = 'Nama wajib diisi (maksimal 100 karakter).';
} elseif (!preg_match('/^[A-Za-z0-9_.]{3,50}$/', $username)) {
    $error = 'Username 3 sampai 50 karakter: huruf, angka, titik, atau garis bawah.';
} elseif (!$admin && !preg_match('/^\d{6,15}$/', $npm)) {
    $error = 'NPM harus berupa angka, 6 sampai 15 digit.';
} elseif ($admin && $email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Format email tidak valid.';
} elseif ($admin && $telepon !== '' && !preg_match('/^\+?[0-9 ]{8,15}$/', $telepon)) {
    $error = 'Nomor HP tidak valid.';
}

// Username tidak boleh dipakai akun lain
if (!$error) {
    $cek = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? AND id <> ?');
    $cek->execute([$username, $id]);
    if ($cek->fetchColumn() > 0) {
        $error = 'Username sudah dipakai pengguna lain.';
    }
}

// Foto baru (kalau ada)
$foto = null;
if (!$error) {
    [$foto, $error] = uploadFoto($_FILES['foto'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
}

if ($error) {
    $_SESSION['old'] = $_POST;
    setFlash('danger', $error);
    redirect('/pages/profile.php');
}

// Simpan; kolom yang bukan milik perannya dikosongkan
try {
    $stmt = $pdo->prepare('UPDATE users SET nama = ?, username = ?, npm = ?, email = ?, telepon = ?, foto = ? WHERE id = ?');
    $stmt->execute([
        $nama,
        $username,
        $admin ? null : $npm,
        $admin && $email !== '' ? $email : null,
        $admin && $telepon !== '' ? $telepon : null,
        $foto ?? $akun['foto'],
        $id,
    ]);
} catch (PDOException $e) {
    hapusSampul($foto);
    $_SESSION['old'] = $_POST;
    setFlash('danger', 'Gagal menyimpan perubahan, coba lagi.');
    redirect('/pages/profile.php');
}

// Hapus foto lama setelah database berhasil diperbarui
if ($foto) {
    hapusSampul($akun['foto']);
}

// Perbarui session dan cookie Ingat saya
$_SESSION['nama'] = $nama;
$_SESSION['foto'] = $foto ?? $akun['foto'];
if (isset($_COOKIE['ingat_username'])) {
    setcookie('ingat_username', $username, [
        'expires'  => time() + 60 * 60 * 24 * 30,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

setFlash('success', 'Data diri berhasil disimpan.');
redirect('/pages/profile.php');