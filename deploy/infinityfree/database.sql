-- ============================================================
-- DATABASE SISTEM AKUNTANSI FOTOKOPI & ATK MAJU JAYA
-- VERSI KHUSUS HOSTING GRATIS (InfinityFree) - tanpa view.
--
-- CARA IMPORT:
-- 1. Buat dulu database-nya di panel hosting: Control Panel ->
--    menu "MySQL Databases" -> tambah database baru dari halaman itu.
-- 2. Buka phpMyAdmin dari panel.
-- 3. Klik nama database itu di daftar sebelah KIRI (jangan pakai tab
--    Import dari halaman depan sebelum database dipilih).
-- 4. Tab "Import" -> "Choose File" -> pilih file ini -> "Go".
-- 5. Pastikan muncul 8 tabel: profil_usaha, coa, barang, pelanggan,
--    pemasok, karyawan, jurnal_umum, detail_jurnal.
--
-- BEDA DARI database.sql DI FOLDER UTAMA (hanya 3 hal ini):
-- - Perintah pembuatan dan pemilihan database dihapus: user paket gratis
--   tidak punya hak itu, database dibuat dulu dari panel.
-- - Dua view laporan (vw_neraca_saldo, vw_laba_rugi) beserta baris
--   penghapusnya dihapus: paket gratis menolak perintah bikin view dengan
--   pesan "command denied". Aplikasi ini tidak memakai kedua view itu -
--   laporan dihitung langsung dari tabel lewat PHP - jadi tidak ada fitur
--   yang hilang.
-- - Sisanya sama persis: 8 tabel, seluruh tipe ENUM, batasan, index, dan
--   kunci antar-tabel, plus data contoh yang sama (22 COA, 8 barang,
--   6 pelanggan, 5 pemasok, 4 karyawan).
--
-- PERHATIAN - DATA LAMA BISA HILANG:
-- Skrip ini memakai DROP TABLE IF EXISTS untuk tabel di atas, jadi data
-- lama pada tabel dengan nama yang sama AKAN TERHAPUS. Jangan dijalankan
-- di database yang sudah berisi data asli.
-- Data barang/pelanggan/pemasok/karyawan adalah DATA DUMMY.
-- ============================================================
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS detail_jurnal;
DROP TABLE IF EXISTS jurnal_umum;
DROP TABLE IF EXISTS barang;
DROP TABLE IF EXISTS pelanggan;
DROP TABLE IF EXISTS pemasok;
DROP TABLE IF EXISTS pengguna;
DROP TABLE IF EXISTS karyawan;
DROP TABLE IF EXISTS coa;
DROP TABLE IF EXISTS profil_usaha;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. PROFIL USAHA
CREATE TABLE profil_usaha (
  id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_usaha VARCHAR(150) NOT NULL,
  jenis_usaha VARCHAR(120) NULL,
  alamat TEXT NULL,
  telepon VARCHAR(30) NULL,
  standar_akuntansi VARCHAR(80) DEFAULT 'SAK EMKM',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO profil_usaha (nama_usaha, jenis_usaha, standar_akuntansi)
VALUES ('Fotokopi & ATK Maju Jaya', 'Fotokopi & Alat Tulis Kantor', 'SAK EMKM');

-- 2. CHART OF ACCOUNTS (22 akun dari workbook)
CREATE TABLE coa (
  kode_akun VARCHAR(15) PRIMARY KEY,
  nama_akun VARCHAR(120) NOT NULL,
  kelompok_akun ENUM('Aset','Liabilitas','Ekuitas','Pendapatan','HPP','Beban Operasional') NOT NULL,
  jenis_akun VARCHAR(60) NOT NULL,
  saldo_normal ENUM('Debit','Kredit') NOT NULL,
  saldo_referensi DECIMAL(18,2) NOT NULL DEFAULT 0
    COMMENT 'Saldo referensi dari workbook; bukan saldo jurnal berjalan',
  keterangan TEXT NULL,
  status_aktif BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO coa
(kode_akun,nama_akun,kelompok_akun,jenis_akun,saldo_normal,saldo_referensi,keterangan)
VALUES
('1-1110','Kas Kecil (Kasir)','Aset','Aset Lancar','Debit',2500000,'Uang tunai fisik di laci kasir harian toko'),
('1-1120','Kas Bank / QRIS','Aset','Aset Lancar','Debit',10000000,'Saldo rekening bank toko / penampungan transaksi QRIS'),
('1-1200','Piutang Usaha','Aset','Aset Lancar','Debit',4500000,'Tagihan fotokopi/cetak bon nota langganan sekolah, instansi, & dinas'),
('1-1300','Persediaan Barang Dagang','Aset','Aset Lancar','Debit',18000000,'Stok kertas (A4, F4, A3), toner printer, mika, spiral jilid, & ATK'),
('1-1400','Perlengkapan Toko','Aset','Aset Lancar','Debit',1200000,'Lakban, kawat staples, klip, plastik pembungkus, pembersih mesin'),
('1-2100','Peralatan Toko','Aset','Aset Tetap','Debit',45000000,'2 unit mesin fotokopi high-speed, 2 PC kasir/cetak, printer warna, mesin potong'),
('1-2200','Akumulasi Penyusutan','Aset','Aset Tetap','Kredit',-7500000,'Akumulasi penyusutan nilai mesin fotokopi dan komputer toko'),
('2-1100','Utang Usaha','Liabilitas','Liabilitas Lancar','Kredit',5000000,'Tagihan tempo pembayaran kertas & toner ke distributor grosir'),
('2-1200','Utang Bank','Liabilitas','Liabilitas Lancar','Kredit',12000000,'Sisa angsuran pinjaman kredit modal kerja mesin fotokopi'),
('2-1300','Utang Pajak','Liabilitas','Liabilitas Lancar','Kredit',175000,'PPh Final UMKM 0,5% bulan berjalan'),
('3-1100','Modal Pemilik','Ekuitas','Ekuitas','Kredit',45000000,'Setoran modal awal pendirian toko fotokopi'),
('3-1200','Laba Ditahan','Ekuitas','Ekuitas','Kredit',11525000,'Akumulasi laba operasional bulan-bulan sebelumnya'),
('4-1100','Penjualan','Pendapatan','Pendapatan','Kredit',35000000,'Total omzet jasa fotokopi, print, jilid, laminating, & penjualan ATK'),
('4-1200','Pendapatan Lain-lain','Pendapatan','Pendapatan','Kredit',600000,'Komisi agen PPOB (listrik, pulsa, transfer bank)'),
('5-1100','Harga Pokok Penjualan','HPP','HPP','Debit',16800000,'Pemakaian kertas, tinta/toner, mika, & modal beli ATK terpakai'),
('6-1100','Beban Pembelian (Opsional)','Beban Operasional','Beban Operasional','Debit',200000,'Ongkos angkut/kurir pengambilan stok kertas dus-dusan'),
('6-1200','Beban Gaji dan Upah','Beban Operasional','Beban Operasional','Debit',4500000,'Gaji 2 operator fotokopi/pencetak'),
('6-1300','Beban Sewa','Beban Operasional','Beban Operasional','Debit',1500000,'Alokasi sewa ruko strategis per bulan (Rp18.000.000/tahun)'),
('6-1400','Beban Listrik dan Air','Beban Operasional','Beban Operasional','Debit',1800000,'Daya listrik pemanas mesin fotokopi, AC, & PC'),
('6-1500','Beban Pemasaran','Beban Operasional','Beban Operasional','Debit',100000,'Cetak brosur promo awal semester sekolah/kuliah'),
('6-1600','Beban Administrasi','Beban Operasional','Beban Operasional','Debit',850000,'Biaya servis berkala mesin fotokopi, sparepart, & Wi-Fi'),
('6-1700','Beban Lain-lain','Beban Operasional','Beban Operasional','Debit',250000,'Kebersihan, keamanan, dan konsumsi harian toko');

-- 3. DATA BARANG (DUMMY)
CREATE TABLE barang (
  id_barang BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode_barang VARCHAR(30) NOT NULL UNIQUE,
  nama_barang VARCHAR(150) NOT NULL,
  kategori VARCHAR(80) NULL,
  satuan VARCHAR(30) NOT NULL,
  stok DECIMAL(14,2) NOT NULL DEFAULT 0,
  harga_beli DECIMAL(18,2) NOT NULL DEFAULT 0,
  harga_jual DECIMAL(18,2) NOT NULL DEFAULT 0,
  status_aktif BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO barang
(kode_barang,nama_barang,kategori,satuan,stok,harga_beli,harga_jual,status_aktif)
VALUES
('BRG-001','Kertas HVS A4 80 gsm','Kertas','Rim',35,48000,55000,1),
('BRG-002','Kertas HVS F4 80 gsm','Kertas','Rim',28,52000,60000,1),
('BRG-003','Pulpen Hitam 0.5 mm','Alat Tulis','Pcs',120,2500,4000,1),
('BRG-004','Pensil 2B','Alat Tulis','Pcs',90,2000,3500,1),
('BRG-005','Map Plastik Kancing','Perlengkapan Kantor','Pcs',75,1800,3000,1),
('BRG-006','Tinta Printer Hitam 100 ml','Tinta dan Toner','Botol',18,55000,68000,1),
('BRG-007','Spiral Jilid Plastik 10 mm','Jilid','Pcs',60,1500,3000,1),
('BRG-008','Lakban Bening 2 inch','Perlengkapan Kantor','Roll',25,9000,12000,1);

-- 4. DATA PELANGGAN (DUMMY)
CREATE TABLE pelanggan (
  id_pelanggan BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode_pelanggan VARCHAR(30) NOT NULL UNIQUE,
  nama_pelanggan VARCHAR(150) NOT NULL,
  no_telepon VARCHAR(30) NULL,
  email VARCHAR(120) NULL,
  alamat TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO pelanggan
(kode_pelanggan,nama_pelanggan,no_telepon,email,alamat)
VALUES
('PLG-001','Koperasi Mahasiswa Sejahtera','081234560101','kopma.simulasi@example.com','Jl. Kampus No. 1'),
('PLG-002','SMA Harapan Bangsa','081234560102','sma.harapan@example.com','Jl. Pendidikan No. 12'),
('PLG-003','Kantor Kelurahan Sukamaju','081234560103','kelurahan.sukamaju@example.com','Jl. Merdeka No. 5'),
('PLG-004','Rina Amelia','081234560104','rina.amelia@example.com','Jl. Melati No. 8'),
('PLG-005','CV Karya Mandiri','081234560105','karya.mandiri@example.com','Jl. Industri No. 21'),
('PLG-006','Budi Santoso','081234560106','budi.santoso@example.com','Jl. Kenanga No. 14');

-- 5. DATA PEMASOK / SUPPLIER (DUMMY)
CREATE TABLE pemasok (
  id_pemasok BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode_pemasok VARCHAR(30) NOT NULL UNIQUE,
  nama_pemasok VARCHAR(150) NOT NULL,
  no_telepon VARCHAR(30) NULL,
  email VARCHAR(120) NULL,
  alamat TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO pemasok
(kode_pemasok,nama_pemasok,no_telepon,email,alamat)
VALUES
('SUP-001','PT Sumber Kertas Nusantara','082211110001','sales@sumberkertas.example.com','Jl. Raya Industri No. 10'),
('SUP-002','CV Mitra ATK Sejahtera','082211110002','mitra.atk@example.com','Jl. Perdagangan No. 7'),
('SUP-003','Toko Grosir Tinta Jaya','082211110003','tintajaya@example.com','Jl. Ahmad Yani No. 25'),
('SUP-004','Distributor Mesin & Toner Prima','082211110004','prima.toner@example.com','Jl. Pabrik No. 18'),
('SUP-005','CV Aneka Perlengkapan Kantor','082211110005','aneka.kantor@example.com','Jl. Sudirman No. 32');

-- 6. DATA KARYAWAN (DUMMY)
CREATE TABLE karyawan (
  id_karyawan BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode_karyawan VARCHAR(30) NOT NULL UNIQUE,
  nama_karyawan VARCHAR(150) NOT NULL,
  jabatan VARCHAR(80) NULL,
  no_telepon VARCHAR(30) NULL,
  alamat TEXT NULL,
  status_aktif BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO karyawan
(kode_karyawan,nama_karyawan,jabatan,no_telepon,alamat,status_aktif)
VALUES
('KRY-001','Andi Pratama','Kasir','081322220001','Jl. Anggrek No. 3',1),
('KRY-002','Siti Rahmawati','Operator Fotokopi','081322220002','Jl. Mawar No. 6',1),
('KRY-003','Dimas Saputra','Operator Cetak dan Jilid','081322220003','Jl. Cempaka No. 9',1),
('KRY-004','Nadia Putri','Administrasi','081322220004','Jl. Flamboyan No. 11',1);

-- 6b. PENGGUNA (autentikasi & RBAC)
CREATE TABLE pengguna (
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

-- Password awal untuk semua akun: 12345678 (harus diganti setelah login pertama).
INSERT INTO pengguna (username,password_hash,nama_lengkap,peran,id_karyawan) VALUES
('admin','$2y$12$wVBdN9BZ8kJGgJbemBeaV.bqCPUvU2UNpD128UtFlgCmoTqBRYg8m','Super Admin','super_admin',NULL),
('andi','$2y$12$wVBdN9BZ8kJGgJbemBeaV.bqCPUvU2UNpD128UtFlgCmoTqBRYg8m','Andi Pratama','karyawan',(SELECT id_karyawan FROM karyawan WHERE kode_karyawan='KRY-001')),
('siti','$2y$12$wVBdN9BZ8kJGgJbemBeaV.bqCPUvU2UNpD128UtFlgCmoTqBRYg8m','Siti Rahmawati','karyawan',(SELECT id_karyawan FROM karyawan WHERE kode_karyawan='KRY-002')),
('dimas','$2y$12$wVBdN9BZ8kJGgJbemBeaV.bqCPUvU2UNpD128UtFlgCmoTqBRYg8m','Dimas Saputra','karyawan',(SELECT id_karyawan FROM karyawan WHERE kode_karyawan='KRY-003')),
('nadia','$2y$12$wVBdN9BZ8kJGgJbemBeaV.bqCPUvU2UNpD128UtFlgCmoTqBRYg8m','Nadia Putri','karyawan',(SELECT id_karyawan FROM karyawan WHERE kode_karyawan='KRY-004'));

-- 7. JURNAL UMUM
CREATE TABLE jurnal_umum (
  id_jurnal BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nomor_jurnal VARCHAR(40) NOT NULL UNIQUE,
  tanggal DATE NOT NULL,
  keterangan TEXT NOT NULL,
  status ENUM('Draft','Posted','Dibatalkan') NOT NULL DEFAULT 'Draft',
  dibuat_oleh VARCHAR(100) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_jurnal_tanggal (tanggal),
  INDEX idx_jurnal_status (status)
) ENGINE=InnoDB;

-- 8. DETAIL JURNAL
CREATE TABLE detail_jurnal (
  id_detail BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_jurnal BIGINT UNSIGNED NOT NULL,
  nomor_baris SMALLINT UNSIGNED NOT NULL,
  kode_akun VARCHAR(15) NOT NULL,
  keterangan VARCHAR(255) NULL,
  debit DECIMAL(18,2) NOT NULL DEFAULT 0,
  kredit DECIMAL(18,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_detail_jurnal FOREIGN KEY (id_jurnal)
    REFERENCES jurnal_umum(id_jurnal) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_detail_coa FOREIGN KEY (kode_akun)
    REFERENCES coa(kode_akun) ON UPDATE CASCADE,
  CONSTRAINT uq_jurnal_baris UNIQUE (id_jurnal, nomor_baris),
  CONSTRAINT chk_debit_kredit CHECK
    (debit >= 0 AND kredit >= 0 AND NOT (debit > 0 AND kredit > 0)),
  INDEX idx_detail_akun (kode_akun)
) ENGINE=InnoDB;

-- VERIFIKASI JUMLAH DATA: COA=22, BARANG=8, PELANGGAN=6,
-- PEMASOK=5, KARYAWAN=4. JURNAL DIMULAI KOSONG.
