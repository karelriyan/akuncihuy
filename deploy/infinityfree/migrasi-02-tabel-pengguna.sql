-- ============================================================
-- MIGRASI 02: TABEL PENGGUNA (autentikasi & RBAC)
-- Untuk database yang SUDAH ter-import di hosting.
--
-- CARA PAKAI:
-- 1. Buka phpMyAdmin dari panel hosting.
-- 2. Klik nama database aplikasi di daftar sebelah KIRI.
-- 3. Tab "SQL" -> tempel isi file ini -> "Go".
--
-- Catatan:
-- - Skrip ini menggunakan CREATE TABLE IF NOT EXISTS dan INSERT IGNORE,
--   sehingga aman dijalankan berulang kali dan TIDAK menghapus data
--   yang sudah ada.
-- - Password awal semua akun: 12345678 (harus diganti setelah login pertama).
-- ============================================================

CREATE TABLE IF NOT EXISTS pengguna (
  id_pengguna BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(150) NOT NULL,
  peran ENUM('super_admin','karyawan') NOT NULL DEFAULT 'karyawan',
  id_karyawan BIGINT UNSIGNED NULL,
  status_aktif BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pengguna_karyawan FOREIGN KEY (id_karyawan) REFERENCES karyawan(id_karyawan)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO pengguna (username,password_hash,nama_lengkap,peran,id_karyawan) VALUES
('admin','$2y$12$wVBdN9BZ8kJGgJbemBeaV.bqCPUvU2UNpD128UtFlgCmoTqBRYg8m','Super Admin','super_admin',NULL),
('andi','$2y$12$wVBdN9BZ8kJGgJbemBeaV.bqCPUvU2UNpD128UtFlgCmoTqBRYg8m','Andi Pratama','karyawan',(SELECT id_karyawan FROM karyawan WHERE kode_karyawan='KRY-001')),
('siti','$2y$12$wVBdN9BZ8kJGgJbemBeaV.bqCPUvU2UNpD128UtFlgCmoTqBRYg8m','Siti Rahmawati','karyawan',(SELECT id_karyawan FROM karyawan WHERE kode_karyawan='KRY-002')),
('dimas','$2y$12$wVBdN9BZ8kJGgJbemBeaV.bqCPUvU2UNpD128UtFlgCmoTqBRYg8m','Dimas Saputra','karyawan',(SELECT id_karyawan FROM karyawan WHERE kode_karyawan='KRY-003')),
('nadia','$2y$12$wVBdN9BZ8kJGgJbemBeaV.bqCPUvU2UNpD128UtFlgCmoTqBRYg8m','Nadia Putri','karyawan',(SELECT id_karyawan FROM karyawan WHERE kode_karyawan='KRY-004'));
