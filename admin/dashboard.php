
<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Cek autentikasi admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Filter dari URL
$filter_kategori = trim($_GET['kategori'] ?? '');
$filter_status = trim($_GET['status'] ?? '');
$search_keyword = trim($_GET['q'] ?? '');
$tanggal_mulai = trim($_GET['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_GET['tanggal_selesai'] ?? '');

// Query database
$sql = "SELECT * FROM aspirasi WHERE 1=1";
$params = [];
$db_error = '';

if ($filter_kategori !== '') {
    $sql .= " AND kategori = :kategori";
    $params['kategori'] = $filter_kategori;
}

if ($filter_status !== '') {
    $sql .= " AND status = :status";
    $params['status'] = $filter_status;
}

if ($search_keyword !== '') {
    $sql .= " AND (nomor_tiket LIKE :q1 OR nama LIKE :q2 OR subjek LIKE :q3 OR asal_sekolah LIKE :q4 OR asal_instansi LIKE :q5 OR alamat LIKE :q6)";
    $like_val = '%' . $search_keyword . '%';

    $params['q1'] = $like_val;
    $params['q2'] = $like_val;
    $params['q3'] = $like_val;
    $params['q4'] = $like_val;
    $params['q5'] = $like_val;
    $params['q6'] = $like_val;
}

if ($tanggal_mulai !== '') {
    $sql .= " AND DATE(dibuat_pada) >= :tanggal_mulai";
    $params['tanggal_mulai'] = $tanggal_mulai;
}

if ($tanggal_selesai !== '') {
    $sql .= " AND DATE(dibuat_pada) <= :tanggal_selesai";
    $params['tanggal_selesai'] = $tanggal_selesai;
}

$sql .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $aspirasis = $stmt->fetchAll();
} catch (PDOException $e) {
    $aspirasis = [];
    $db_error = "Terjadi kesalahan database: " . $e->getMessage();
}

// Daftar kategori
$kategoriList = [
    'Pelayanan Petugas',
    'Fasilitas Perpustakaan',
    'Kenyamanan Ruang Baca',
    'Akses Layanan',
    'Koleksi dan Bahan Pustaka',
    'Kebersihan dan Keamanan',
    'Saran Pengembangan',
    'Lainnya'
];

// Nama admin
$adminNama = htmlspecialchars(
    $_SESSION['admin_nama'] ?? 'Administrator',
    ENT_QUOTES,
    'UTF-8'
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - Kotak Saran Perpustakaan OKU Timur</title>
    <link rel="shortcut icon" href="../assets/logo-perpus.jpg" type="image/jpeg">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            color: #1E293B;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col antialiased">

    <!-- TOP ANNOUNCEMENT BAR -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-6 text-center font-medium border-b border-slate-800 tracking-wide flex items-center justify-center gap-2">

        <i data-lucide="landmark" class="w-3.5 h-3.5 text-amber-400"></i>

        <span>
            Panel Administrator Kotak Saran -
            Dinas Perpustakaan dan Kearsipan Kabupaten OKU Timur
        </span>

    </div>

    <!-- NAVBAR ADMIN -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">

        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center gap-4">

            <!-- LOGO DAN IDENTITAS -->
            <div class="flex items-center gap-3">

                <div class="flex items-center gap-2 bg-white rounded-xl border border-slate-200 p-1.5 shadow-sm">
                    <img
                        src="../assets/logo-oku-timur.jpg"
                        alt="Logo Pemkab OKU Timur"
                        class="h-10 w-auto object-contain"
                    >
                    <img
                        src="../assets/logo-perpus.jpg"
                        alt="Logo Perpustakaan Daerah OKU Timur"
                        class="h-10 w-auto object-contain"
                    >
                </div>

                <div>

                    <h1 class="font-bold text-slate-900 text-base leading-tight">
                        Dashboard Admin
                    </h1>

                    <p class="text-xs text-slate-500 mt-1">
                        Halo, <?= $adminNama; ?>
                    </p>

                    <p class="text-[10px] text-indigo-600 font-bold tracking-wide mt-1">
                        PERPUSTAKAAN DAERAH OKU TIMUR
                    </p>

                </div>

            </div>

            <!-- TOMBOL NAVIGASI -->
            <div class="flex items-center gap-3">

                <a
                    href="../index.php"
                    target="_blank"
                    class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 rounded-xl transition flex items-center gap-1.5"
                >
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    Lihat Website
                </a>

                <a
                    href="logout.php"
                    class="px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 bg-red-50/50 rounded-xl transition flex items-center gap-1.5"
                >
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    Keluar
                </a>

            </div>

        </div>

    </header>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-6 py-8 flex-grow w-full">

        <!-- JUDUL HALAMAN -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">

            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    Kelola Saran & Pengaduan
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Filter berdasarkan kategori, status, dan rentang tanggal untuk rekapitulasi.
                </p>
            </div>

        </div>

        <!-- ERROR DATABASE -->
        <?php if (!empty($db_error)): ?>

            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-xs rounded-2xl">
                <strong>Terjadi Kendala Database:</strong><br>
                <?= htmlspecialchars($db_error, ENT_QUOTES, 'UTF-8'); ?>
            </div>

        <?php endif; ?>

        <!-- FILTER DAN PENCARIAN -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 mb-8">

            <form
                action="dashboard.php"
                method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end"
            >

                <!-- FILTER KATEGORI -->
                <div>

                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                        Filter Kategori
                    </label>

                    <select
                        name="kategori"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:border-indigo-600"
                    >

                        <option value="">-- Semua Kategori --</option>

                        <?php foreach ($kategoriList as $kat): ?>

                            <option
                                value="<?= htmlspecialchars($kat, ENT_QUOTES, 'UTF-8'); ?>"
                                <?= ($filter_kategori === $kat) ? 'selected' : ''; ?>
                            >
                                <?= htmlspecialchars($kat, ENT_QUOTES, 'UTF-8'); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- FILTER STATUS -->
                <div>

                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                        Filter Status
                    </label>

                    <select
                        name="status"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:border-indigo-600"
                    >

                        <option value="">-- Semua Status --</option>

                        <option value="Menunggu" <?= ($filter_status === 'Menunggu') ? 'selected' : ''; ?>>
                            Menunggu
                        </option>

                        <option value="Diproses" <?= ($filter_status === 'Diproses') ? 'selected' : ''; ?>>
                            Diproses
                        </option>

                        <option value="Selesai" <?= ($filter_status === 'Selesai') ? 'selected' : ''; ?>>
                            Selesai
                        </option>

                    </select>

                </div>

                <!-- TANGGAL MULAI -->
                <div>

                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                        Dari Tanggal
                    </label>

                    <input
                        type="date"
                        name="tanggal_mulai"
                        value="<?= htmlspecialchars($tanggal_mulai, ENT_QUOTES, 'UTF-8'); ?>"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:border-indigo-600"
                    >

                </div>

                <!-- TANGGAL SELESAI -->
                <div>

                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                        Sampai Tanggal
                    </label>

                    <input
                        type="date"
                        name="tanggal_selesai"
                        value="<?= htmlspecialchars($tanggal_selesai, ENT_QUOTES, 'UTF-8'); ?>"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:border-indigo-600"
                    >

                </div>

                <!-- PENCARIAN DAN TOMBOL -->
                <div class="flex flex-wrap gap-2 lg:col-span-5 pt-2 items-center">

                    <div class="flex-grow min-w-[200px]">

                        <input
                            type="text"
                            name="q"
                            value="<?= htmlspecialchars($search_keyword, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="Cari nomor tiket, nama pelapor, atau subjek..."
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-white"
                        >

                    </div>

                    <button
                        type="submit"
                        class="py-2.5 px-5 bg-indigo-600 text-white font-semibold text-sm rounded-xl hover:bg-indigo-700 transition flex items-center gap-2 whitespace-nowrap shadow-sm"
                    >

                        <i data-lucide="filter" class="w-4 h-4"></i>
                        Terapkan Filter

                    </button>

                    <a
                        href="dashboard.php"
                        class="py-2.5 px-4 bg-slate-100 text-slate-600 font-semibold text-sm rounded-xl hover:bg-slate-200 transition flex items-center justify-center whitespace-nowrap"
                    >

                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Reset

                    </a>

                    <a
                        href="export.php?<?= htmlspecialchars(http_build_query($_GET), ENT_QUOTES, 'UTF-8'); ?>"
                        class="py-2.5 px-5 bg-emerald-600 text-white font-semibold text-sm rounded-xl hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm whitespace-nowrap"
                    >

                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        Ekspor Excel

                    </a>

                </div>

            </form>

        </div>

        <!-- TABEL DATA ASPIRASI -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">

                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                    Daftar Laporan Masuk (<?= count($aspirasis); ?>)
                </span>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-left border-collapse">

                    <thead>

                        <tr class="border-b border-slate-100 text-xs font-bold text-slate-400 uppercase bg-slate-50/30">

                            <th class="py-4 px-6">Tiket & Tanggal</th>
                            <th class="py-4 px-6">Pengirim</th>
                            <th class="py-4 px-6">Kategori & Jenis</th>
                            <th class="py-4 px-6">Subjek</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6 text-center">Aksi</th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100 text-sm">

                        <?php if (count($aspirasis) > 0): ?>

                            <?php foreach ($aspirasis as $row): ?>

                                <tr class="hover:bg-slate-50/60 transition">

                                    <!-- TIKET DAN TANGGAL -->
                                    <td class="py-4 px-6">

                                        <span class="font-bold text-slate-900 block font-mono">
                                            <?= htmlspecialchars($row['nomor_tiket'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>

                                        <span class="text-xs text-slate-400">
                                            <?= htmlspecialchars($row['dibuat_pada'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>

                                    </td>

                                    <!-- PENGIRIM -->
                                    <td class="py-4 px-6">

                                        <span class="font-medium text-slate-800 block">
                                            <?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>

                                        <?php if (!empty($row['asal_sekolah'])): ?>
                                            <span class="text-xs text-indigo-600 font-medium block">
                                                🏫 <?= htmlspecialchars($row['asal_sekolah'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if (!empty($row['asal_instansi'])): ?>
                                            <span class="text-xs text-emerald-600 font-medium block">
                                                🏛️ <?= htmlspecialchars($row['asal_instansi'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        <?php endif; ?>

                                        <span class="text-xs text-slate-400 block">
                                            <?= htmlspecialchars($row['kontak'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                        </span>

                                    </td>

                                    <!-- KATEGORI DAN JENIS -->
                                    <td class="py-4 px-6">

                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-medium inline-block mb-1">
                                            <?= htmlspecialchars($row['jenis'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>

                                        <span class="text-xs text-slate-500 block">
                                            <?= htmlspecialchars($row['kategori'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>

                                    </td>

                                    <!-- SUBJEK -->
                                    <td class="py-4 px-6">

                                        <span class="font-semibold text-slate-800">
                                            <?= htmlspecialchars($row['subjek'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>

                                    </td>

                                    <!-- STATUS -->
                                    <td class="py-4 px-6">

                                        <?php
                                        $st = $row['status'];

                                        $badge = 'bg-amber-50 text-amber-700 border-amber-200';

                                        if ($st === 'Diproses') {
                                            $badge = 'bg-blue-50 text-blue-700 border-blue-200';
                                        }

                                        if ($st === 'Selesai') {
                                            $badge = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                        }
                                        ?>

                                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?= $badge; ?>">
                                            <?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>

                                    </td>

                                    <!-- AKSI -->
                                    <td class="py-4 px-6 text-center">

                                        <a
                                            href="detail.php?id=<?= (int) $row['id']; ?>"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700 transition shadow-sm"
                                        >

                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            Tanggapi

                                        </a>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="6" class="py-12 text-center text-slate-400 text-sm">

                                    <div class="flex flex-col items-center gap-2">

                                        <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>

                                        <span>
                                            Tidak ada data aspirasi yang sesuai dengan filter atau rentang tanggal tersebut.
                                        </span>

                                    </div>

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

    <!-- FOOTER -->
    <footer class="w-full border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">

        &copy; <?= date('Y'); ?>
        Dinas Perpustakaan dan Kearsipan Kabupaten OKU Timur.
        All rights reserved.

    </footer>

    <script>
        lucide.createIcons();
    </script>

</body>
</html>
