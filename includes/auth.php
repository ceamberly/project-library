<?php
// Guard: halaman yang meng-include file ini wajib login
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/function.php';

if (empty($_SESSION['user_id'])) {
    setFlash('warning', 'Silakan masuk dulu.');
    redirect('/login.php');
}