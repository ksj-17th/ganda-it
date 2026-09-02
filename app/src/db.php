<?php
function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host = getenv('DB_HOST') ?: 'db';
    $name = getenv('DB_NAME') ?: 'mft';
    $user = getenv('DB_USER') ?: 'mft';
    $pass = getenv('DB_PASS') ?: 'mftpass';

    $pdo = new PDO(
        "mysql:host={$host};dbname={$name};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    return $pdo;
}

function current_user(): ?array {
    $sid = $_COOKIE['mft_session'] ?? '';
    if ($sid === '') return null;

    $st = db()->prepare(
        'SELECT u.id,u.username,u.role,s.session_id,s.expires_at
         FROM sessions s JOIN users u ON u.id=s.user_id
         WHERE s.session_id=? AND s.expires_at > NOW()'
    );
    $st->execute([$sid]);
    return $st->fetch() ?: null;
}

function require_login(): array {
    $u = current_user();
    if (!$u) {
        header('Location: /login.php');
        exit;
    }
    return $u;
}

function require_admin(): array {
    $u = require_login();
    if ($u['role'] !== 'admin') {
        http_response_code(403);
        exit('Forbidden');
    }
    return $u;
}

require_once '/var/www/src/audit.php';
