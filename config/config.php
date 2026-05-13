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
const APP_URL = 'http://localhost';
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

error_reporting(E_ALL);
ini_set('display_errors', '0');

if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
