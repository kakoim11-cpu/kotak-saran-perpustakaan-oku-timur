<?php
require __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#kirim');
    exit;
}

verify_csrf();

$nama          = trim($_POST['nama'] ?? '');
$kontak        = trim($_POST['kontak'] ?? '');
$asal_sekolah  = trim($_POST['asal_sekolah'] ?? '');
$asal_instansi = trim($_POST['asal_instansi'] ?? '');
$alamat        = trim($_POST['alamat'] ?? '');
$jenis         = trim($_POST['jenis'] ?? '');
$kategori      = trim($_POST['kategori'] ?? '');
$subjek        = trim($_POST['subjek'] ?? '');
$isi           = trim($_POST['isi'] ?? '');

$jenisValid    = ['Aspirasi', 'Pengaduan', 'Saran'];
$kategoriValid = [
    'Pelayanan Petugas', 
    'Fasilitas Perpustakaan', 
    'Kenyamanan Ruang Baca', 
    'Akses Layanan', 
    'Koleksi dan Bahan Pustaka', 
    'Kebersihan dan Keamanan', 
    'Saran Pengembangan', 
    'Lainnya'
];

$errors = [];

if ($nama === '' || mb_strlen($nama) > 100) {
    $errors[] = 'Nama wajib diisi (maksimal 100 karakter).';
}
if (mb_strlen($kontak) > 120) {
    $errors[] = 'Kontak maksimal 120 karakter.';
}
if (mb_strlen($asal_sekolah) > 150) {
    $errors[] = 'Asal sekolah maksimal 150 karakter.';
}
if (mb_strlen($asal_instansi) > 150) {
    $errors[] = 'Asal instansi maksimal 150 karakter.';
}
if (mb_strlen($alamat) > 500) {
    $errors[] = 'Alamat maksimal 500 karakter.';
}
if (!in_array($jenis, $jenisValid, true)) {
    $errors[] = 'Jenis masukan tidak valid.';
}
if (!in_array($kategori, $kategoriValid, true)) {
    $errors[] = 'Kategori tidak valid.';
}
if ($subjek === '' || mb_strlen($subjek) > 150) {
    $errors[] = 'Subjek wajib diisi (maksimal 150 karakter).';
}
if ($isi === '' || mb_strlen($isi) > 3000) {
    $errors[] = 'Isi wajib diisi (maksimal 3.000 karakter).';
}

if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_input'] = [
        'nama'          => $nama,
        'kontak'        => $kontak,
        'asal_sekolah'  => $asal_sekolah,
        'asal_instansi' => $asal_instansi,
        'alamat'        => $alamat,
        'jenis'         => $jenis,
        'kategori'      => $kategori,
        'subjek'        => $subjek,
        'isi'           => $isi
    ];
    header('Location: index.php#kirim');
    exit;
}

$ticket = 'ASP-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

$stmt = $pdo->prepare('
    INSERT INTO aspirasi (
        nomor_tiket, nama, kontak, asal_sekolah, asal_instansi, alamat, jenis, kategori, subjek, isi, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
');

$stmt->execute([
    $ticket,
    $nama,
    $kontak ?: null,
    $asal_sekolah ?: null,
    $asal_instansi ?: null,
    $alamat ?: null,
    $jenis,
    $kategori,
    $subjek,
    $isi,
    'Menunggu'
]);

$_SESSION['submission_success'] = ['ticket' => $ticket];
header('Location: index.php#kirim');
exit;
