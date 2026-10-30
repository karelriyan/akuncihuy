<?php
/**
 * Template kredensial database.
 *
 * Cara pakai (kalau menjalankan aplikasi dari komputer sendiri):
 * 1. Copy file ini menjadi config/credentials.php.
 * 2. Isi nilainya dengan data dari panel hosting (InfinityFree:
 *    Control Panel -> MySQL Databases -> Create Database).
 * 3. Jangan commit config/credentials.php — file itu sudah masuk .gitignore.
 *
 * Saat deploy ke hosting, config/credentials.php TIDAK diambil dari file ini,
 * tapi dibuat otomatis oleh .github/workflows/deploy.yml dari GitHub Secrets
 * (DB_HOST, DB_NAME, DB_USER, DB_PASS). Jadi di hosting tidak perlu diedit manual.
 *
 * Urutan yang dibaca aplikasi (lihat config/database.php):
 * config/credentials.php -> variabel lingkungan DB_* -> default lokal.
 */
return [
    // Hostname MySQL dari panel, bukan 'localhost'. Contoh: sql302.infinityfree.com
    'host' => 'sqlXXX.infinityfree.com',
    // Nama database lengkap dengan prefix akun. Contoh: if0_12345678_majujaya
    'db'   => 'if0_xxxxxxx_namadb',
    // Username MySQL. Contoh: if0_12345678
    'user' => 'if0_xxxxxxx',
    // Password database = password akun hosting (tombol Show/Hide di panel).
    'pass' => 'password-database-anda',
];
