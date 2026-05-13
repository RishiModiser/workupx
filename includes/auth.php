<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        redirect('/login.php');
    }
}

function require_admin(): void
{
    require_login();

    $user = current_user();
    if (($user['role'] ?? ROLE_USER) !== ROLE_ADMIN) {
        http_response_code(403);
        exit('Admin access required.');
    }
}

function login_user(array $user, bool $remember): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];

    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $stmt = db()->prepare('UPDATE users SET remember_token_hash = :hash, remember_token_expires_at = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE id = :id');
        $stmt->execute(['hash' => $tokenHash, 'id' => (int) $user['id']]);

        setcookie('remember_token', $token, [
            'expires' => time() + (60 * 60 * 24 * 30),
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}

function logout_user(): void
{
    if (!empty($_SESSION['user_id'])) {
        $stmt = db()->prepare('UPDATE users SET remember_token_hash = NULL, remember_token_expires_at = NULL WHERE id = :id');
        $stmt->execute(['id' => (int) $_SESSION['user_id']]);
    }

    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }
    setcookie('remember_token', '', time() - 3600, '/');
    session_destroy();
}

function bootstrap_remember_me(): void
{
    if (!empty($_SESSION['user_id']) || empty($_COOKIE['remember_token'])) {
        return;
    }

    $tokenHash = hash('sha256', $_COOKIE['remember_token']);

    $stmt = db()->prepare('SELECT * FROM users WHERE remember_token_hash = :hash AND remember_token_expires_at > NOW() LIMIT 1');
    $stmt->execute(['hash' => $tokenHash]);
    $user = $stmt->fetch();

    if ($user) {
        login_user($user, false);
    }
}

function throttle_login(array $user): void
{
    if (($user['locked_until'] ?? null) && strtotime((string) $user['locked_until']) > time()) {
        throw new RuntimeException('Account temporarily locked due to failed login attempts. Try again later.');
    }
}

function register_failed_attempt(int $userId, int $attempts): void
{
    $lockedUntil = $attempts >= 5 ? date('Y-m-d H:i:s', time() + 900) : null;

    $stmt = db()->prepare('UPDATE users SET failed_login_attempts = :attempts, locked_until = :locked_until WHERE id = :id');
    $stmt->execute([
        'attempts' => $attempts,
        'locked_until' => $lockedUntil,
        'id' => $userId,
    ]);
}
