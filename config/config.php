<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

const APP_NAME = 'WORKUPX';
const APP_DOMAIN = 'WORKUPX.COM';
const WHATSAPP_SUPPORT = 'https://wa.me/0000000000';

const DB_HOST = '127.0.0.1';
const DB_NAME = 'workupx';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

const UPLOAD_DIR = __DIR__ . '/../uploads';
const MAX_UPLOAD_SIZE = 5 * 1024 * 1024;

const ROLE_USER = 'user';
const ROLE_ADMIN = 'admin';
const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_LOCK_SECONDS = 900;

$defaultScheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$defaultHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
if (!defined('APP_URL')) {
    define('APP_URL', rtrim((string) (getenv('APP_URL') ?: "{$defaultScheme}://{$defaultHost}"), '/'));
}

error_reporting(E_ALL);
ini_set('display_errors', '0');

if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
