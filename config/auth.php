<?php
declare(strict_types=1);

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => (!empty($_SERVER['HTTPS']) || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')),
]);
session_start();

function auth_user(): ?array {
    if (empty($_SESSION['id_pengguna'])) return null;
    return [
        'id_pengguna' => $_SESSION['id_pengguna'],
        'username' => $_SESSION['username'] ?? '',
        'nama_lengkap' => $_SESSION['nama_lengkap'] ?? '',
        'peran' => $_SESSION['peran'] ?? 'karyawan',
        'id_karyawan' => $_SESSION['id_karyawan'] ?? null,
    ];
}

function auth_require_login(): void {
    if (auth_user() === null) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(['error' => 'Silakan login terlebih dahulu.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function auth_require_super_admin(): void {
    $u = auth_user();
    if ($u === null || ($u['peran'] ?? '') !== 'super_admin') {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(403);
        echo json_encode(['error' => 'Akses ditolak. Hanya Super Admin yang dapat melakukan ini.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function auth_require_csrf_header(): void {
    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(403);
        echo json_encode(['error' => 'Permintaan ditolak.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
