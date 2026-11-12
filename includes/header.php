<?php $pageTitle = $pageTitle ?? 'Aspirasi Perpustakaan'; ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | Dinas Perpustakaan dan Kearsipan OKU Timur</title>
  <meta name="description" content="Layanan aspirasi dan pengaduan pengunjung perpustakaan Kabupaten OKU Timur.">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= $basePath ?? '' ?>/assets/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark nav-main sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $basePath ?? '' ?>/index.php">
      <span class="brand-mark"><i class="bi bi-book-half"></i></span>
      <span class="brand-name">PERPUSTAKAAN<small>ASPIRASI & PENGADUAN</small></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Buka navigasi"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="mainNav"><div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
      <a class="nav-link" href="<?= $basePath ?? '' ?>/index.php">Beranda</a>
      <a class="nav-link" href="<?= $basePath ?? '' ?>/cek_status.php">Cek Status</a>
      <a class="btn btn-light btn-sm rounded-pill px-3 ms-lg-2" href="<?= $basePath ?? '' ?>/index.php#kirim">Kirim Aspirasi <i class="bi bi-arrow-up-right"></i></a>
    </div></div>
  </div>
</nav>
<main>
