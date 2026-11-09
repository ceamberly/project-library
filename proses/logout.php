<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';

// Kosongkan session dan hapus cookie session
$_SESSION = [];
$p = session_get_cookie_params();
setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
session_destroy();

// Hapus cookie remember me
setcookie('ingat_username', '', time() - 3600, '/');

redirect('/login.php');