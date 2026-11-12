<?php
// Salin file ini menjadi 'config/database.php' dan sesuaikan dengan kredensial server/database Anda.

$host = 'localhost';
$db   = 'db_aspirasi_perpustakaan';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Auto-migration: pastikan kolom baru otomatis dibuat jika belum ada di database
    try {
        $checkCol = $pdo->query("SHOW COLUMNS FROM aspirasi LIKE 'asal_sekolah'")->fetch();
        if (!$checkCol) {
            $pdo->exec("ALTER TABLE aspirasi ADD COLUMN asal_sekolah VARCHAR(150) NULL AFTER kontak");
            $pdo->exec("ALTER TABLE aspirasi ADD COLUMN asal_instansi VARCHAR(150) NULL AFTER asal_sekolah");
            $pdo->exec("ALTER TABLE aspirasi ADD COLUMN alamat TEXT NULL AFTER asal_instansi");
        }
        $checkRating = $pdo->query("SHOW COLUMNS FROM aspirasi LIKE 'rating'")->fetch();
        if (!$checkRating) {
            $pdo->exec("ALTER TABLE aspirasi ADD COLUMN rating TINYINT NULL AFTER tanggapan");
            $pdo->exec("ALTER TABLE aspirasi ADD COLUMN ulasan TEXT NULL AFTER rating");
        }
    } catch (Exception $e) {
        // Abaikan jika tabel aspirasi belum diimpor
    }
} catch (PDOException $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Pastikan MySQL aktif dan database sudah diimpor dari database.sql.');
}

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function e($value) { 
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); 
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        http_response_code(403); 
        exit('Permintaan tidak valid. Muat ulang halaman lalu coba kembali.');
    }
}

