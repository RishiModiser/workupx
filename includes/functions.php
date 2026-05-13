<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Invalid CSRF token.');
    }
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flash(): array
{
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flash;
}

function app_setting(string $key, string $default = ''): string
{
    $stmt = db()->prepare('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1');
    $stmt->execute(['key' => $key]);
    $row = $stmt->fetch();
    return $row['setting_value'] ?? $default;
}

function random_code(int $length = 8): string
{
    return strtoupper(substr(bin2hex(random_bytes($length)), 0, $length));
}

function paginate(int $total, int $page, int $perPage = 10): array
{
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;

    return ['page' => $page, 'offset' => $offset, 'perPage' => $perPage, 'totalPages' => $totalPages];
}

function upload_image(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    if (($file['size'] ?? 0) > MAX_UPLOAD_SIZE) {
        throw new RuntimeException('File too large.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => throw new RuntimeException('Invalid image format.'),
    };

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $name = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $target = UPLOAD_DIR . '/' . $name;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Upload failed.');
    }

    return $name;
}

function format_money(float $amount): string
{
    return '$' . number_format($amount, 2);
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = db()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => (int) $_SESSION['user_id']]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function log_admin(string $action, string $context = ''): void
{
    $adminId = $_SESSION['user_id'] ?? null;
    if (!$adminId) {
        return;
    }

    $stmt = db()->prepare('INSERT INTO admin_logs (admin_id, action, context, ip_address, created_at) VALUES (:admin_id, :action, :context, :ip, NOW())');
    $stmt->execute([
        'admin_id' => (int) $adminId,
        'action' => $action,
        'context' => $context,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
    ]);
}
