<?php
declare(strict_types=1);
// Kredensial dibaca dengan urutan: config/credentials.php -> variabel lingkungan (DB_*) -> default lokal.
// File credentials.php dibuat otomatis saat deploy dari GitHub Secrets dan tidak ikut git.
$credentials = is_file(__DIR__ . '/credentials.php') ? require __DIR__ . '/credentials.php' : [];
$host = $credentials['host'] ?? (getenv('DB_HOST') ?: '127.0.0.1');
$db   = $credentials['db']   ?? (getenv('DB_NAME') ?: 'db_maju_jaya');
$user = $credentials['user'] ?? (getenv('DB_USER') ?: 'root');
$pass = $credentials['pass'] ?? (getenv('DB_PASS') ?: ''); // Default XAMPP. Change this if your MySQL password is different.
$charset = 'utf8mb4';
$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die('Database connection failed. Check config/database.php and make sure MySQL is running. Details: ' . htmlspecialchars($e->getMessage()));
}
