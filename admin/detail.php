<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Cek autentikasi admin agar tidak terjadi login berulang
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$error = '';
$success = '';

// Ambil data aspirasi berdasarkan ID
try {
    $stmt = $pdo->prepare("SELECT * FROM aspirasi WHERE id = ?");
    $stmt->execute([$id]);
    $aspirasi = $stmt->fetch();

    if (!$aspirasi) {
        header("Location: dashboard.php");
        exit;
    }
} catch (PDOException $e) {
    $error = "Gagal memuat data: " . $e->getMessage();
}

// Tangani penyimpanan tanggapan & pembaruan status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_tanggapan'])) {
    $tanggapan = trim($_POST['tanggapan'] ?? '');
    $status = trim($_POST['status'] ?? 'Diproses');

    if (!empty($tanggapan)) {
        try {
            $stmtUpdate = $pdo->prepare("UPDATE aspirasi SET tanggapan = ?, status = ? WHERE id = ?");
            $stmtUpdate->execute([$tanggapan, $status, $id]);
            
            // Redirect untuk mencegah form resubmission
            header("Location: detail.php?id=" . $id . "&success=1");
            exit;
        } catch (PDOException $e) {
            $error = "Gagal menyimpan tanggapan: " . $e->getMessage();
        }
    } else {
        $error = "Kolom tanggapan tidak boleh kosong.";
    }
}

// Cek notifikasi sukses dari URL
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success = "Tanggapan berhasil dikirim dan status diperbarui.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanggapi Laporan - Dashboard Admin</title>
    <link rel="shortcut icon" href="../assets/logo-perpus.jpg" type="image/jpeg">
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

    <!-- Top Announcement Bar -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-6 text-center font-medium border-b border-slate-800 tracking-wide flex items-center justify-center gap-2">
        <i data-lucide="landmark" class="w-3.5 h-3.5 text-amber-400"></i>
        <span>Panel Administrator Kotak Saran - Dinas Perpustakaan dan Kearsipan Kabupaten OKU Timur</span>
    </div>

    <!-- Top Navbar Admin -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-5xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="dashboard.php" class="bg-indigo-50 text-indigo-600 p-2 rounded-xl hover:bg-indigo-100 transition">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div class="flex items-center gap-1.5 bg-white rounded-lg border border-slate-200 p-1">
                    <img src="../assets/logo-oku-timur.jpg" alt="Logo OKU Timur" class="h-7 w-auto object-contain">
                    <img src="../assets/logo-perpus.jpg" alt="Logo Perpustakaan" class="h-7 w-auto object-contain">
                </div>
                <div>
                    <h1 class="font-bold text-slate-900 text-base leading-tight">Detail & Tanggapan Laporan</h1>
                    <p class="text-xs text-slate-500 font-mono">Tiket: <?php echo htmlspecialchars($aspirasi['nomor_tiket'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
            <a href="dashboard.php" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 rounded-xl transition flex items-center gap-1.5">
                <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i> Kembali ke Dashboard
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-6 py-8 flex-grow w-full">
        
        <?php if (!empty($success)): ?>
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl font-medium flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-2xl font-medium">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Informasi Detail Aspirasi (Kolom Kiri) -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-4">
                    <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3">Informasi Pelapor</h3>
                    
                    <div>
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block">Nama Pengirim</span>
                        <strong class="text-slate-800 text-sm"><?php echo htmlspecialchars($aspirasi['nama'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>

                    <div>
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block">Kontak (No. HP / Email)</span>
                        <span class="text-slate-700 text-sm"><?php echo htmlspecialchars($aspirasi['kontak'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>

                    <div>
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block">Asal Sekolah</span>
                        <span class="text-slate-700 text-sm"><?php echo htmlspecialchars($aspirasi['asal_sekolah'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>

                    <div>
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block">Asal Instansi</span>
                        <span class="text-slate-700 text-sm"><?php echo htmlspecialchars($aspirasi['asal_instansi'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>

                    <div>
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block">Alamat</span>
                        <span class="text-slate-700 text-sm"><?php echo nl2br(htmlspecialchars($aspirasi['alamat'] ?? '-', ENT_QUOTES, 'UTF-8')); ?></span>
                    </div>

                    <div>
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block">Jenis & Kategori</span>
                        <span class="inline-block mt-1 px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-semibold">
                            <?php echo htmlspecialchars($aspirasi['jenis'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($aspirasi['kategori'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>

                    <div>
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block">Tanggal Masuk</span>
                        <span class="text-slate-600 text-xs"><?php echo htmlspecialchars($aspirasi['dibuat_pada'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>

                <!-- Ulasan Kepuasan Pengguna (Jika Ada) -->
                <?php if (!empty($aspirasi['rating'])): ?>
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-3">
                        <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                            <i data-lucide="star" class="w-4 h-4 text-amber-500 fill-amber-400"></i> Ulasan Pengguna
                        </h3>
                        <div class="flex items-center gap-1 text-amber-500">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i data-lucide="star" class="w-3.5 h-3.5 <?php echo $i <= $aspirasi['rating'] ? 'fill-amber-400 text-amber-400' : 'text-slate-200'; ?>"></i>
                            <?php endfor; ?>
                            <span class="text-xs font-bold text-slate-700 ml-2">(<?php echo $aspirasi['rating']; ?> / 5.0)</span>
                        </div>
                        <p class="text-slate-600 text-xs italic bg-slate-50 p-3 rounded-xl border border-slate-100">
                            "<?php echo htmlspecialchars($aspirasi['ulasan'] ?? 'Tidak ada ulasan teks.', ENT_QUOTES, 'UTF-8'); ?>"
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Konten Aspirasi & Form Tanggapan (Kolom Kanan) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Isi Aspirasi -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-4">
                    <div class="flex justify-between items-start border-b border-slate-100 pb-3">
                        <div>
                            <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block">Subjek Laporan</span>
                            <h2 class="font-bold text-slate-900 text-lg"><?php echo htmlspecialchars($aspirasi['subjek'], ENT_QUOTES, 'UTF-8'); ?></h2>
                        </div>
                        <div>
                            <?php 
                                $st = $aspirasi['status'];
                                $badge = 'bg-amber-50 text-amber-700 border-amber-200';
                                if ($st === 'Diproses') $badge = 'bg-blue-50 text-blue-700 border-blue-200';
                                if ($st === 'Selesai') $badge = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo $badge; ?>">
                                <?php echo htmlspecialchars($st, ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block mb-1">Isi Aspirasi / Pengaduan</span>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-slate-700 text-sm leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($aspirasi['isi'], ENT_QUOTES, 'UTF-8')); ?>
                        </div>
                    </div>
                </div>

                <!-- Form Berikan / Ubah Tanggapan -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <div class="mb-4 border-b border-slate-100 pb-3">
                        <h3 class="font-bold text-slate-900 text-base">Formulir Tanggapan Petugas</h3>
                        <p class="text-xs text-slate-500">Berikan tanggapan dan ubah status penanganan laporan.</p>
                    </div>

                    <form action="detail.php?id=<?php echo $id; ?>" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Status Laporan</label>
                            <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:border-indigo-600">
                                <option value="Menunggu" <?php echo ($aspirasi['status'] === 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="Diproses" <?php echo ($aspirasi['status'] === 'Diproses') ? 'selected' : ''; ?>>Diproses</option>
                                <option value="Selesai" <?php echo ($aspirasi['status'] === 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Teks Tanggapan / Solusi</label>
                            <textarea name="tanggapan" rows="5" required placeholder="Tuliskan tanggapan atau tindak lanjut di sini..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-slate-50/50"><?php echo htmlspecialchars($aspirasi['tanggapan'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <button type="submit" name="kirim_tanggapan" class="w-full py-3.5 bg-indigo-600 text-white font-semibold rounded-xl text-sm hover:bg-indigo-700 transition shadow-sm shadow-indigo-200 flex items-center justify-center gap-2">
                            <i data-lucide="send" class="w-4 h-4"></i> Simpan & Kirim Tanggapan
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500 mt-12">
        &copy; <?php echo date('Y'); ?> Dinas Perpustakaan dan Kearsipan Kabupaten OKU Timur. All rights reserved.
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>