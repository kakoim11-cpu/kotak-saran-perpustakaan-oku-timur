<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Cek autentikasi admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Tangkap parameter filter yang sama dengan dashboard
$filter_kategori = trim($_GET['kategori'] ?? '');
$filter_status = trim($_GET['status'] ?? '');
$search_keyword = trim($_GET['q'] ?? '');
$tanggal_mulai = trim($_GET['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_GET['tanggal_selesai'] ?? '');

$sql = "SELECT * FROM aspirasi WHERE 1=1";
$params = [];

if (!empty($filter_kategori)) {
    $sql .= " AND kategori = :kategori";
    $params['kategori'] = $filter_kategori;
}

if (!empty($filter_status)) {
    $sql .= " AND status = :status";
    $params['status'] = $filter_status;
}

if (!empty($search_keyword)) {
    $sql .= " AND (nomor_tiket LIKE :q OR nama LIKE :q OR subjek LIKE :q OR asal_sekolah LIKE :q OR asal_instansi LIKE :q OR alamat LIKE :q)";
    $params['q'] = '%' . $search_keyword . '%';
}

if (!empty($tanggal_mulai)) {
    $sql .= " AND DATE(dibuat_pada) >= :tanggal_mulai";
    $params['tanggal_mulai'] = $tanggal_mulai;
}

if (!empty($tanggal_selesai)) {
    $sql .= " AND DATE(dibuat_pada) <= :tanggal_selesai";
    $params['tanggal_selesai'] = $tanggal_selesai;
}

$sql .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $data = [];
}

// Nama file export Excel (.csv yang otomatis bisa dibuka di Microsoft Excel)
$filename = "Rekap_Kotak_Saran_OKUTimur_" . date('Y-m-d_H-i-s') . ".csv";

// Header HTTP untuk proses download file
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Tambahkan BOM UTF-8 agar karakter spesial/huruf beraksen terbaca sempurna di Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Baris Header Kolom dalam Excel
fputcsv($output, [
    'No', 
    'Nomor Tiket', 
    'Tanggal Masuk', 
    'Nama Pengirim', 
    'Kontak (HP/Email)', 
    'Asal Sekolah',
    'Asal Instansi',
    'Alamat',
    'Jenis', 
    'Kategori', 
    'Subjek Laporan', 
    'Isi Aspirasi/Saran', 
    'Status Penanganan', 
    'Tanggapan Petugas', 
    'Rating (Bintang)', 
    'Ulasan Kepuasan'
]);

// Masukkan data baris demi baris dari database
$no = 1;
foreach ($data as $row) {
    fputcsv($output, [
        $no++,
        $row['nomor_tiket'],
        $row['dibuat_pada'],
        $row['nama'],
        $row['kontak'] ?? '-',
        $row['asal_sekolah'] ?? '-',
        $row['asal_instansi'] ?? '-',
        $row['alamat'] ?? '-',
        $row['jenis'],
        $row['kategori'],
        $row['subjek'],
        $row['isi'],
        $row['status'],
        $row['tanggapan'] ?? 'Belum ditanggapi',
        $row['rating'] ?? '-',
        $row['ulasan'] ?? '-'
    ]);
}

fclose($output);
exit;