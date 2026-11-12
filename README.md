# Sistem Informasi Aspirasi dan Pengaduan Pengunjung Perpustakaan

Aplikasi prototipe proyek magang untuk Dinas Perpustakaan dan Kearsipan Kabupaten OKU Timur. Dibangun dengan PHP Native (PDO), MySQL, Bootstrap 5, dan Bootstrap Icons. Disiapkan untuk dijalankan secara lokal menggunakan **Laragon**.

## Fitur
- Halaman utama dan formulir aspirasi, pengaduan, atau saran.
- Nomor tiket unik dan halaman cek status publik.
- Login admin menggunakan `password_hash()` / `password_verify()`.
- Dashboard ringkasan, filter status, detail laporan, tanggapan, dan pembaruan status.
- Rekap kategori dan ekspor CSV.
- Prepared statements, output HTML di-escape, dan token CSRF pada formulir POST.

## Instalasi di Laragon

1. Ekstrak ZIP proyek.
2. Pindahkan folder `aspirasi-perpustakaan` ke folder web Laragon, biasanya:

   ```text
   C:\laragon\www\aspirasi-perpustakaan
   ```

3. Buka Laragon, lalu klik **Start All** untuk menjalankan Apache dan MySQL.
4. Buka alat database yang tersedia di instalasi Anda, misalnya HeidiSQL atau phpMyAdmin. Buat/impor database dengan cara:
   - Jika menggunakan phpMyAdmin: buka `http://localhost/phpmyadmin`, pilih **Import**, lalu pilih file `database.sql`.
   - Jika menggunakan HeidiSQL: sambungkan ke MySQL lokal, lalu jalankan isi file `database.sql` melalui editor SQL.
   File SQL akan membuat database `db_aspirasi_perpustakaan` beserta tabel yang diperlukan.
5. Salin file `config/database.example.php` menjadi `config/database.php`. Konfigurasi bawaan adalah host `localhost`, database `db_aspirasi_perpustakaan`, username `root`, dan password kosong. Sesuaikan jika konfigurasi MySQL Laragon Anda berbeda.
6. Buat hash password admin melalui **Laragon Terminal**. Jalankan perintah berikut dan ganti `GANTI_PASSWORD_KAMU` dengan password yang ingin dipakai:

   ```bash
   php -r "echo password_hash('GANTI_PASSWORD_KAMU', PASSWORD_DEFAULT), PHP_EOL;"
   ```

   Salin hash yang muncul. Kemudian jalankan SQL berikut di database `db_aspirasi_perpustakaan` dan ganti `TEMPEL_HASH_DI_SINI` dengan hash tersebut:

   ```sql
   INSERT INTO admin (username, password_hash, nama_lengkap)
   VALUES ('admin', 'TEMPEL_HASH_DI_SINI', 'Administrator Perpustakaan');
   ```

   Jika username `admin` sudah ada, jangan jalankan INSERT yang sama lagi. Pilih username yang berbeda atau perbarui data admin yang sudah ada.
7. Buka halaman publik:

   ```text
   http://localhost/aspirasi-perpustakaan/
   ```

8. Buka halaman login admin:

   ```text
   http://localhost/aspirasi-perpustakaan/admin/login.php
   ```

## Jika halaman tidak bisa dibuka
- Pastikan folder proyek berada langsung di `C:\laragon\www\aspirasi-perpustakaan`.
- Pastikan Apache dan MySQL berstatus berjalan di Laragon.
- Pastikan nama database pada `config/database.php` sama dengan nama database yang dibuat dari `database.sql`.
- Jika port MySQL atau password berbeda, ubah konfigurasi koneksi.
- Bootstrap dan Bootstrap Icons dimuat melalui CDN, sehingga tampilan lengkap membutuhkan koneksi internet.

## Catatan keamanan
Ini adalah prototipe awal untuk magang dan belum diaudit untuk penggunaan publik di internet. Sebelum dipublikasikan, gunakan HTTPS, password admin yang kuat, backup rutin, pembatasan percobaan login, serta kebijakan privasi. Halaman cek status menggunakan nomor tiket sebagai kunci akses; jangan memasukkan data pribadi sensitif ke laporan publik. Identitas visual dan konten dinas perlu ditinjau oleh pihak dinas. Proyek ini tidak menyertakan atau merekayasa logo resmi.
