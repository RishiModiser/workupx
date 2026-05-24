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
        $maxMb = number_format(MAX_UPLOAD_SIZE / (1024 * 1024), 0);
        throw new RuntimeException('File too large. Maximum allowed size is ' . $maxMb . 'MB.');
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

function get_withdrawal_fee_percent(): float
{
    $configured = (float) app_setting('withdrawal_fee_percent', (string) DEFAULT_WITHDRAWAL_FEE_PERCENT);
    return max(0, min(100, $configured));
}

function package_catalog(): array
{
    return [
        'silver' => ['label' => 'Silver', 'deposit' => 125.0, 'welcome_bonus' => 12.0],
        'gold' => ['label' => 'Gold', 'deposit' => 250.0, 'welcome_bonus' => 25.0],
        'diamond' => ['label' => 'Diamond', 'deposit' => 500.0, 'welcome_bonus' => 50.0],
    ];
}

function package_config(string $package): array
{
    $catalog = package_catalog();
    return $catalog[$package] ?? $catalog['silver'];
}

function package_label(string $package): string
{
    return package_config($package)['label'];
}

function referral_earning_boost_percent(int $referrals): float
{
    $perReferral = (float) app_setting('referral_earning_boost_per_user_percent', '0.5');
    return round(max(0, $referrals) * max(0, $perReferral), 2);
}

function salary_rulebook(): array
{
    return [
        'silver' => ['required_referrals' => 30, 'target_referral_plan' => 'silver', 'monthly_reward' => 50.0],
        'gold' => ['required_referrals' => 20, 'target_referral_plan' => 'silver', 'monthly_reward' => 100.0],
        'diamond' => ['required_referrals' => 10, 'target_referral_plan' => 'gold', 'monthly_reward' => 200.0],
    ];
}

function get_salary_status(int $userId, string $package): array
{
    $rules = salary_rulebook();
    $rule = $rules[$package] ?? $rules['silver'];

    $stmt = db()->prepare(
        'SELECT COUNT(*) AS total
         FROM referrals r
         INNER JOIN users u ON u.id = r.referred_user_id
         WHERE r.referrer_user_id = :uid AND u.package_name = :target_plan'
    );
    $stmt->execute(['uid' => $userId, 'target_plan' => $rule['target_referral_plan']]);
    $qualifiedReferrals = (int) ($stmt->fetch()['total'] ?? 0);

    $isEligible = $qualifiedReferrals >= (int) $rule['required_referrals'];
    $monthlyReward = (float) $rule['monthly_reward'];

    return [
        'required_referrals' => (int) $rule['required_referrals'],
        'qualified_referrals' => $qualifiedReferrals,
        'target_referral_plan' => (string) $rule['target_referral_plan'],
        'monthly_reward' => $monthlyReward,
        'weekly_reward' => round($monthlyReward / 4, 2),
        'eligible' => $isEligible,
    ];
}

function calculate_available_withdrawal_balance(int $userId, ?int $excludeWithdrawalId = null): float
{
    $balanceStmt = db()->prepare('SELECT balance FROM users WHERE id = :uid LIMIT 1');
    $balanceStmt->execute(['uid' => $userId]);
    $balance = (float) ($balanceStmt->fetch()['balance'] ?? 0);

    $query = 'SELECT COALESCE(SUM(amount),0) AS total FROM withdrawals WHERE user_id = :uid AND status = "pending"';
    $params = ['uid' => $userId];

    if ($excludeWithdrawalId !== null) {
        $query .= ' AND id != :exclude_id';
        $params['exclude_id'] = $excludeWithdrawalId;
    }

    $pendingStmt = db()->prepare($query);
    $pendingStmt->execute($params);
    $pending = (float) ($pendingStmt->fetch()['total'] ?? 0);

    return max(0, $balance - $pending);
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
