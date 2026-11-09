<?php
// Konfigurasi database dan konstanta dasar
const DB_HOST = 'localhost';
const DB_NAME = 'db_perpustakaan';
const DB_USER = 'isi_user';
const DB_PASS = 'isi_password';
const BASE_URL = '';

// Koneksi PDO
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    exit('Koneksi database gagal.');
}