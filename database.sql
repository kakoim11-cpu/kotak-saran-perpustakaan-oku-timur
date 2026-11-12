CREATE DATABASE IF NOT EXISTS db_aspirasi_perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_aspirasi_perpustakaan;

CREATE TABLE IF NOT EXISTS admin (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS aspirasi (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nomor_tiket VARCHAR(30) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  kontak VARCHAR(120) NULL,
  asal_sekolah VARCHAR(150) NULL,
  asal_instansi VARCHAR(150) NULL,
  alamat TEXT NULL,
  jenis ENUM('Aspirasi','Pengaduan','Saran') NOT NULL,
  kategori VARCHAR(80) NOT NULL,
  subjek VARCHAR(150) NOT NULL,
  isi TEXT NOT NULL,
  status ENUM('Menunggu','Diproses','Selesai') NOT NULL DEFAULT 'Menunggu',
  tanggapan TEXT NULL,
  rating TINYINT NULL,
  ulasan TEXT NULL,
  dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  diperbarui_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status), INDEX idx_kategori (kategori), INDEX idx_dibuat (dibuat_pada)
) ENGINE=InnoDB;

-- Buat hash kata sandi dengan PHP (lihat README.md), lalu masukkan admin.
-- Jangan menggunakan kata sandi contoh di lingkungan produksi.
