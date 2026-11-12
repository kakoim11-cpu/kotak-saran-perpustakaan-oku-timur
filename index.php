<?php
require_once __DIR__ . '/config/database.php';

// Ambil data statistik publik untuk transparansi
try {
    $totalAspirasi = $pdo->query("SELECT COUNT(*) FROM aspirasi")->fetchColumn();
    $totalSelesai = $pdo->query("SELECT COUNT(*) FROM aspirasi WHERE status = 'Selesai'")->fetchColumn();
} catch (PDOException $e) {
    $totalAspirasi = 0;
    $totalSelesai = 0;
}

// Ambil error atau sukses form jika ada dari session sebelumnya
$form_errors = $_SESSION['form_errors'] ?? [];
$old_input = $_SESSION['old_input'] ?? [];
$success = $_SESSION['submission_success'] ?? null;

// Bersihkan session pesan setelah diambil
unset($_SESSION['form_errors'], $_SESSION['old_input'], $_SESSION['submission_success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kotak Saran - Perpustakaan Daerah OKU Timur</title>
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
        <div class="flex items-center gap-2 md:gap-3">
            <button onclick="openLoginModal()" class="px-3.5 py-2 text-xs md:text-sm font-semibold text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5">
                <i data-lucide="lock" class="w-4 h-4 text-slate-400"></i>
                <span class="hidden sm:inline">Login Admin</span>
            </button>
            <a href="cek_status.php" class="px-3.5 py-2 text-xs md:text-sm font-semibold text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5">
                <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                <span>Cek Status</span>
            </a>
            <a href="#kirim" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-xs md:text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm">
                <span>Kirim Saran</span>
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </header>

    <!-- Main Hero Section -->
    <main class="w-full max-w-5xl mx-auto px-6 pt-10 pb-16 flex-grow">
        
        <!-- Notifikasi Sukses & Pesan Terima Kasih -->
        <?php if ($success): ?>
            <div class="mb-8 p-6 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-3xl shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="space-y-1 text-center sm:text-left">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold mb-1">
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Berhasil Terkirim
                    </div>
                    <h4 class="font-bold text-lg text-slate-900">Terima Kasih atas Masukan Anda!</h4>
                    <p class="text-sm text-slate-600">
                        Saran dan aspirasi Anda sangat berarti bagi kemajuan perpustakaan. Nomor Tiket Anda: <strong class="text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded font-mono"><?php echo e($success['ticket']); ?></strong>
                    </p>
                </div>
                <a href="cek_status.php?nomor_tiket=<?php echo urlencode($success['ticket']); ?>" class="whitespace-nowrap px-5 py-2.5 bg-emerald-600 text-white font-semibold text-xs rounded-xl hover:bg-emerald-700 transition shadow-sm">
                    Cek Status Laporan &rarr;
                </a>
            </div>
        <?php endif; ?>

        <?php if (!empty($form_errors)): ?>
            <div class="mb-8 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl">
                <h4 class="font-bold text-sm mb-1">Terjadi kesalahan pengisian:</h4>
                <ul class="list-disc list-inside text-xs space-y-1">
                    <?php foreach ($form_errors as $err): ?>
                        <li><?php echo e($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Tagline Kecil -->
        <div class="inline-flex items-center gap-2 text-indigo-700 text-xs font-bold uppercase tracking-wider bg-indigo-50 border border-indigo-100 px-3.5 py-1.5 rounded-full mb-6">
            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500"></i>
            <span>Pelayanan Prima, Perpustakaan Bangkit</span>
        </div>

        <!-- Headline Utama -->
        <div class="relative mb-6">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.15]">
                Suara Anda membangun <br class="hidden sm:inline" />
                <span class="text-indigo-600">Perpustakaan OKU Timur</span> lebih baik.
            </h1>
        </div>

        <!-- Deskripsi -->
        <p class="text-slate-600 text-base md:text-lg max-w-2xl leading-relaxed mb-8">
            Sampaikan saran, kritik konstruktif, serta pengalaman Anda selama memanfaatkan fasilitas dan layanan di Perpustakaan Daerah OKU Timur. Setiap masukan adalah komitmen kami untuk terus berbenah.
        </p>

        <!-- STATISTIK PUBLIK RINGKAS -->
        <div class="grid grid-cols-2 sm:grid-cols-2 gap-4 max-w-sm mb-10">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                <span class="text-xs text-slate-400 font-semibold block uppercase mb-1">Total Masuk</span>
                <span class="text-2xl font-extrabold text-slate-900"><?php echo e($totalAspirasi); ?></span>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                <span class="text-xs text-indigo-600 font-semibold block uppercase mb-1">Selesai Ditangani</span>
                <span class="text-2xl font-extrabold text-indigo-700"><?php echo e($totalSelesai); ?></span>
            </div>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-wrap items-center gap-4 mb-20">
            <a href="#kirim" class="inline-flex items-center gap-2 px-6 py-3.5 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-700 transition shadow-sm shadow-indigo-200">
                <span>Kirim Saran Sekarang</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
            <a href="cek_status.php" class="inline-flex items-center gap-2 px-6 py-3.5 bg-white border border-slate-200 text-slate-700 font-semibold rounded-full hover:bg-slate-50 transition shadow-sm">
                Cek Status Laporan
            </a>
        </div>

        <!-- Form Kirim Aspirasi Section -->
        <div id="kirim" class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200 mb-20">
            <div class="mb-6 border-b border-slate-100 pb-4">
                <div class="inline-block p-2 bg-indigo-50 text-indigo-600 rounded-xl mb-2">
                    <i data-lucide="message-square-plus" class="w-6 h-6"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-900">Formulir Kotak Saran & Pengaduan</h3>
                <p class="text-slate-500 text-sm">Silakan lengkapi formulir di bawah ini dengan data yang valid.</p>
            </div>

            <form action="kirim.php" method="POST" class="space-y-5">
                <!-- CSRF Token Wajib -->
                <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" required value="<?php echo e($old_input['nama'] ?? ''); ?>" placeholder="Nama Anda" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-slate-50/50">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Kontak (No. HP / WhatsApp / Email)</label>
                        <input type="text" name="kontak" value="<?php echo e($old_input['kontak'] ?? ''); ?>" placeholder="Opsional (misal: 08123456789)" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-slate-50/50">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Asal Sekolah</label>
                        <input type="text" name="asal_sekolah" value="<?php echo e($old_input['asal_sekolah'] ?? ''); ?>" placeholder="Opsional (misal: SMA N 1 Martapura)" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-slate-50/50">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Asal Instansi</label>
                        <input type="text" name="asal_instansi" value="<?php echo e($old_input['asal_instansi'] ?? ''); ?>" placeholder="Opsional (misal: Dinas Pendidikan / Umum)" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-slate-50/50">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Alamat</label>
                    <input type="text" name="alamat" value="<?php echo e($old_input['alamat'] ?? ''); ?>" placeholder="Opsional (misal: Jl. Lintas Sumatera No. 45, Martapura)" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-slate-50/50">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Jenis Masukan</label>
                        <select name="jenis" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-slate-50/50">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Aspirasi" <?php echo (($old_input['jenis'] ?? '') === 'Aspirasi') ? 'selected' : ''; ?>>Aspirasi</option>
                            <option value="Pengaduan" <?php echo (($old_input['jenis'] ?? '') === 'Pengaduan') ? 'selected' : ''; ?>>Pengaduan</option>
                            <option value="Saran" <?php echo (($old_input['jenis'] ?? '') === 'Saran') ? 'selected' : ''; ?>>Saran</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Kategori</label>
                        <select name="kategori" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-slate-50/50">
                            <option value="">-- Pilih Kategori --</option>
                            <?php 
                            $kategoriList = ['Pelayanan Petugas', 'Fasilitas Perpustakaan', 'Kenyamanan Ruang Baca', 'Akses Layanan', 'Koleksi dan Bahan Pustaka', 'Kebersihan dan Keamanan', 'Saran Pengembangan', 'Lainnya'];
                            foreach($kategoriList as $kat):
                            ?>
                                <option value="<?php echo $kat; ?>" <?php echo (($old_input['kategori'] ?? '') === $kat) ? 'selected' : ''; ?>><?php echo $kat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Subjek / Judul Laporan</label>
                    <input type="text" name="subjek" required value="<?php echo e($old_input['subjek'] ?? ''); ?>" placeholder="Ringkasan laporan Anda" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-slate-50/50">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Isi Saran / Aspirasi</label>
                    <textarea name="isi" rows="4" required placeholder="Jelaskan secara rinci..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-slate-50/50"><?php echo e($old_input['isi'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white font-semibold rounded-xl text-sm hover:bg-indigo-700 transition shadow-sm shadow-indigo-200">
                    Kirim Saran Sekarang
                </button>
            </form>
        </div>

    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
        &copy; <?php echo date('Y'); ?> Dinas Perpustakaan dan Kearsipan Kabupaten OKU Timur. All rights reserved.
    </footer>

    <!-- MODAL LOGIN ADMIN -->
    <div id="loginModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 m-4 relative transform scale-95 transition-transform duration-300">
            <!-- Close Button -->
            <button onclick="closeLoginModal()" class="absolute right-4 top-4 text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <!-- Modal Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <img src="assets/logo-oku-timur.jpg" alt="Logo OKU Timur" class="h-10 w-auto object-contain">
                    <img src="assets/logo-perpus.jpg" alt="Logo Per pustakaan OKU Timur" class="h-10 w-auto object-contain">
                </div>
                <h3 class="text-xl font-bold text-slate-900">Login Administrator</h3>
                <p class="text-slate-500 text-sm mt-1">Masuk untuk mengelola kotak saran & tanggapan.</p>
            </div>

            <!-- Form Login Modal (Mengarah ke admin/login.php) -->
            <form action="admin/login.php" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Username</label>
                    <input type="text" name="username" required placeholder="Masukkan username" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600">
                </div>
                <button type="submit" class="w-full py-3 bg-slate-900 text-white text-sm font-semibold rounded-xl hover:bg-slate-800 transition mt-2">
                    Masuk Portal
                </button>
            </form>
        </div>
    </div>

    <!-- Script JavaScript -->
    <script>
        lucide.createIcons();

        function openLoginModal() {
            const modal = document.getElementById('loginModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.children[0].classList.remove('scale-95');
                modal.children[0].classList.add('scale-100');
            }, 10);
        }

        function closeLoginModal() {
            const modal = document.getElementById('loginModal');
            modal.classList.add('opacity-0');
            modal.children[0].classList.remove('scale-100');
            modal.children[0].classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
</body>
</html>