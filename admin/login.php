<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin) {
                // Deteksi nama kolom password secara otomatis (mencegah Undefined Array Key)[cite: 10]
                $hash = '';
                if (isset($admin['password'])) {
                    $hash = (string)$admin['password'];
                } elseif (isset($admin['pass'])) {
                    $hash = (string)$admin['pass'];
                } elseif (isset($admin['password_hash'])) {
                    $hash = (string)$admin['password_hash'];
                } elseif (isset($admin['pwd'])) {
                    $hash = (string)$admin['pwd'];
                }

                // Verifikasi password secara aman (Mencegah Deprecated Warning)[cite: 10]
                $isValidPassword = false;
                if ($hash !== '') {
                    if (password_verify($password, $hash) || $password === $hash) {
                        $isValidPassword = true;
                    }
                }

                if ($isValidPassword) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_nama'] = $admin['nama'] ?? $admin['username'] ?? 'Administrator';
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = "Username atau password salah.";
                }
            } else {
                $error = "Username atau password salah.";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan database: " . $e->getMessage();
        }
    } else {
        $error = "Harap isi semua kolom.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Kotak Saran Perpustakaan OKU Timur</title>
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

    <!-- Top Announcement Bar (Senada dengan Dashboard) -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-6 text-center font-medium border-b border-slate-800 tracking-wide flex items-center justify-center gap-2">
        <i data-lucide="landmark" class="w-3.5 h-3.5 text-amber-400"></i>
        <span>Portal Resmi Administrator Dinas Perpustakaan dan Kearsipan Kabupaten OKU Timur</span>
    </div>

    <!-- Main Login Container -->
    <main class="flex-grow flex items-center justify-center px-4 py-12">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 w-full max-w-md p-8 relative">
            
            <div class="text-center mb-8">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <img src="../assets/logo-oku-timur.jpg" alt="Logo Pemkab OKU Timur" class="h-12 w-auto object-contain">
                    <img src="../assets/logo-perpus.jpg" alt="Logo Perpustakaan OKU Timur" class="h-12 w-auto object-contain">
                </div>
                <h1 class="text-2xl font-bold text-slate-900">Login Administrator</h1>
                <p class="text-slate-500 text-sm mt-1">Masuk untuk mengelola kotak saran & tanggapan.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl text-center font-medium">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Username</label>
                    <input type="text" name="username" required placeholder="Masukkan username" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-slate-50/50">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-600 bg-slate-50/50">
                </div>
                <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm shadow-indigo-200 mt-2">
                    Masuk ke Dashboard
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="../index.php" class="text-xs text-slate-500 hover:text-indigo-600 transition inline-flex items-center gap-1 font-medium">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Beranda Utama
                </a>
            </div>
        </div>
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