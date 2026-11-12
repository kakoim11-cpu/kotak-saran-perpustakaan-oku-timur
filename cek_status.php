<?php
require_once __DIR__ . '/config/database.php';

$aspirasi = null;
$error = '';
$success_msg = '';

// Tangani pengiriman ulasan / rating kepuasan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_ulasan'])) {
    $ticket_id = trim($_POST['ticket_nomor'] ?? '');
    $rating = intval($_POST['rating'] ?? 5);
    $ulasan = trim($_POST['ulasan'] ?? '');

    if (!empty($ticket_id)) {
        try {
            $stmtUpdate = $pdo->prepare("UPDATE aspirasi SET rating = ?, ulasan = ? WHERE nomor_tiket = ?");
            $stmtUpdate->execute([$rating, $ulasan, $ticket_id]);
            header("Location: cek_status.php?nomor_tiket=" . urlencode($ticket_id) . "&success_review=1");
            exit;
        } catch (PDOException $e) {
            $error = "Gagal menyimpan ulasan: " . $e->getMessage();
        }
    }
}

// Cek apakah ada notifikasi sukses ulasan dari URL
if (isset($_GET['success_review']) && $_GET['success_review'] == '1') {
    $success_msg = "Terima kasih! Ulasan kepuasan Anda berhasil disimpan.";
}

$search_query = trim($_GET['nomor_tiket'] ?? '');

if (!empty($search_query)) {
    try {
        // Pencarian fleksibel dengan parameter unik PDO (:q1 dan :q2)
        $stmt = $pdo->prepare("SELECT * FROM aspirasi WHERE nomor_tiket = :q1 OR kontak = :q2 ORDER BY id DESC LIMIT 1");
        $stmt->execute([
            'q1' => $search_query,
            'q2' => $search_query
        ]);
        $aspirasi = $stmt->fetch();

        if (!$aspirasi) {
            $error = "Data tidak ditemukan berdasarkan nomor tiket atau kontak tersebut.";
        }
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan database: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status - Kotak Saran Perpustakaan Daerah OKU Timur</title>
    <link rel="shortcut icon" href="assets/logo-perpus.jpg" type="image/jpeg">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
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
<body class="min-h-screen flex flex-col justify-between antialiased">

    <!-- LAYER DI ATAS: Top Announcement Bar (Identitas Resmi Instansi) -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-6 text-center font-medium border-b border-slate-800 tracking-wide flex items-center justify-center gap-2">
        <i data-lucide="landmark" class="w-3.5 h-3.5 text-amber-400"></i>
        <span>Portal Resmi Layanan & Kotak Saran Dinas Perpustakaan dan Kearsipan Kabupaten OKU Timur</span>
    </div>

    <!-- Header / Navbar -->
    <header class="w-full max-w-7xl mx-auto px-6 py-5 flex justify-between items-center bg-white/80 backdrop-blur-md sticky top-0 z-20 border-b border-slate-100">
        <a href="index.php" class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-white rounded-xl border border-slate-200 p-1 shadow-sm">
                <img src="assets/logo-oku-timur.jpg" alt="Logo OKU Timur" class="h-9 w-auto object-contain">
                <img src="assets/logo-perpus.jpg" alt="Logo Perpustakaan OKU Timur" class="h-9 w-auto object-contain">
            </div>
            <div>
                <span class="font-extrabold text-sm md:text-base text-slate-900 tracking-tight block leading-tight">Kotak Saran</span>
                <span class="text-[11px] font-semibold text-indigo-600 uppercase tracking-wider block">Perpustakaan Daerah OKU Timur</span>
            </div>
        </a>
        <div class="flex items-center gap-3">
            <a href="index.php" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900 transition flex items-center gap-2">
                <i data-lucide="home" class="w-4 h-4"></i>
                Beranda
            </a>
            <a href="index.php#kirim" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm shadow-indigo-200">
                Kirim Saran
                <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full max-w-3xl mx-auto px-6 pt-10 pb-16 flex-grow">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Pantau Status & Tanggapan</h1>
            <p class="text-slate-500 text-sm">Masukkan nomor tiket atau nomor kontak Anda untuk melihat tanggapan dan memberikan ulasan kepuasan.</p>
        </div>

        <!-- Form Pencarian Tiket / Kontak -->
        <form action="cek_status.php" method="GET" class="bg-white p-4 rounded-3xl shadow-sm border border-slate-200 flex gap-3 mb-8">
            <div class="relative flex-grow">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="nomor_tiket" required value="<?php echo e($search_query); ?>" placeholder="No. Tiket (Cth: ASP-...) atau No. HP/Email" class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-slate-50/50">
            </div>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold text-sm rounded-2xl hover:bg-indigo-700 transition shadow-sm shadow-indigo-200">
                Cek Status
            </button>
        </form>

        <!-- Pesan Sukses Ulasan -->
        <?php if (!empty($success_msg)): ?>
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl mb-6 text-center font-medium">
                <?php echo e($success_msg); ?>
            </div>
        <?php endif; ?>

        <!-- Pesan Error -->
        <?php if (!empty($error)): ?>
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-2xl mb-6 text-center">
                <?php echo e($error); ?>
            </div>
        <?php endif; ?>

        <!-- Hasil Pencarian / Detail Aspirasi -->
        <?php if ($aspirasi): ?>
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6">
                
                <!-- Header Status & Nomor Tiket -->
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-5">
                    <div>
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block">Nomor Tiket</span>
                        <span class="text-lg font-bold text-slate-900 font-mono"><?php echo e($aspirasi['nomor_tiket']); ?></span>
                    </div>
                    <div>
                        <?php 
                            $status = $aspirasi['status'];
                            $badgeColor = 'bg-amber-50 text-amber-700 border-amber-200';
                            if ($status === 'Diproses') $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200';
                            if ($status === 'Selesai') $badgeColor = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                        ?>
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-bold border <?php echo $badgeColor; ?>">
                            Status: <?php echo e($status); ?>
                        </span>
                    </div>
                </div>

                <!-- Informasi Pelapor & Kategori -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div>
                        <span class="text-slate-400 text-xs block font-medium">Nama Pengirim</span>
                        <strong class="text-slate-800"><?php echo e($aspirasi['nama']); ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs block font-medium">Jenis & Kategori</span>
                        <strong class="text-slate-800"><?php echo e($aspirasi['jenis']); ?> - <?php echo e($aspirasi['kategori']); ?></strong>
                    </div>
                    <?php if (!empty($aspirasi['asal_sekolah'])): ?>
                    <div>
                        <span class="text-slate-400 text-xs block font-medium">Asal Sekolah</span>
                        <span class="text-slate-700 font-medium"><?php echo e($aspirasi['asal_sekolah']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($aspirasi['asal_instansi'])): ?>
                    <div>
                        <span class="text-slate-400 text-xs block font-medium">Asal Instansi</span>
                        <span class="text-slate-700 font-medium"><?php echo e($aspirasi['asal_instansi']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($aspirasi['alamat'])): ?>
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 text-xs block font-medium">Alamat</span>
                        <span class="text-slate-700"><?php echo e($aspirasi['alamat']); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 text-xs block font-medium">Tanggal Dibuat</span>
                        <span class="text-slate-700"><?php echo e($aspirasi['dibuat_pada']); ?></span>
                    </div>
                </div>

                <!-- Subjek & Isi Laporan -->
                <div class="space-y-3">
                    <div>
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block">Subjek Laporan</span>
                        <h3 class="font-bold text-slate-900 text-base"><?php echo e($aspirasi['subjek']); ?></h3>
                    </div>
                    <div>
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block">Isi Saran / Pengaduan</span>
                        <p class="text-slate-600 text-sm leading-relaxed bg-slate-50/50 p-4 rounded-2xl border border-slate-100"><?php echo nl2br(e($aspirasi['isi'])); ?></p>
                    </div>
                </div>

                <!-- KOTAK TANGGAPAN DARI PETUGAS -->
                <div class="border-t border-slate-100 pt-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <i data-lucide="message-square-text" class="w-4 h-4"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 text-base">Tanggapan Petugas Perpustakaan</h4>
                    </div>

                    <?php if (!empty($aspirasi['tanggapan'])): ?>
                        <div class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-2xl text-slate-700 text-sm leading-relaxed">
                            <?php echo nl2br(e($aspirasi['tanggapan'])); ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-amber-50/60 border border-amber-100 p-4 rounded-2xl text-amber-800 text-sm italic flex items-center gap-2">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            <span>Belum ada tanggapan dari petugas. Laporan Anda sedang dalam antrean peninjauan.</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- FITUR ULASAN / RATING KEPUASAN (JIKA STATUS SELESAI) -->
                <?php if ($aspirasi['status'] === 'Selesai'): ?>
                    <div class="border-t border-slate-100 pt-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i data-lucide="star" class="w-4 h-4"></i>
                            </div>
                            <h4 class="font-bold text-slate-900 text-base">Penilaian Kepuasan Layanan</h4>
                        </div>

                        <?php if (!empty($aspirasi['rating'])): ?>
                            <div class="bg-amber-50/40 border border-amber-200/60 p-4 rounded-2xl space-y-2">
                                <div class="flex items-center gap-1 text-amber-500">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <i data-lucide="star" class="w-4 h-4 <?php echo $i <= $aspirasi['rating'] ? 'fill-amber-400 text-amber-400' : 'text-slate-300'; ?>"></i>
                                    <?php endfor; ?>
                                    <span class="text-xs font-bold text-slate-700 ml-2">(<?php echo $aspirasi['rating']; ?> / 5.0 - Puas)</span>
                                </div>
                                <p class="text-slate-600 text-sm italic">Ulasan Anda: "<?php echo e($aspirasi['ulasan'] ?? 'Tidak ada ulasan teks.'); ?>"</p>
                            </div>
                        <?php else: ?>
                            <form action="cek_status.php?nomor_tiket=<?php echo urlencode($search_query); ?>" method="POST" class="bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-3">
                                <input type="hidden" name="ticket_nomor" value="<?php echo e($aspirasi['nomor_tiket']); ?>">
                                <p class="text-xs text-slate-500">Laporan Anda telah selesai ditangani. Berikan penilaian kepuasan:</p>
                                
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Rating Bintang</label>
                                    <select name="rating" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white font-medium focus:outline-none focus:border-indigo-600">
                                        <option value="5">⭐⭐⭐⭐⭐ (5 - Sangat Puas)</option>
                                        <option value="4">⭐⭐⭐⭐ (4 - Puas)</option>
                                        <option value="3">⭐⭐⭐ (3 - Cukup)</option>
                                        <option value="2">⭐⭐ (2 - Kurang)</option>
                                        <option value="1">⭐ (1 - Buruk)</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Ulasan / Masukan</label>
                                    <textarea name="ulasan" rows="2" placeholder="Tuliskan ulasan atau saran tambahan Anda..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:border-indigo-600"></textarea>
                                </div>
                                
                                <button type="submit" name="kirim_ulasan" class="px-5 py-2.5 bg-indigo-600 text-white text-xs font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm">
                                    Kirim Penilaian Kepuasan
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        <?php endif; ?>

    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
        &copy; <?php echo date('Y'); ?> Dinas Perpustakaan dan Kearsipan Kabupaten OKU Timur. All rights reserved.
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html> 