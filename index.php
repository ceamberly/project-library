<?php
// Pengarah: sudah login ke dashboard, belum ke login
require __DIR__ . '/config/database.php';
session_start();
header('Location: ' . BASE_URL . (isset($_SESSION['user_id']) ? '/pages/dashboard.php' : '/login.php'));
exit;