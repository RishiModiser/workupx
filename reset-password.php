<?php
require_once __DIR__ . '/includes/functions.php';

$token = trim((string) ($_GET['token'] ?? $_POST['token'] ?? ''));
$validRow = null;

if ($token !== '') {
    $tokenHash = hash('sha256', $token);
    $stmt = db()->prepare('SELECT pr.user_id, u.email FROM password_resets pr INNER JOIN users u ON u.id = pr.user_id WHERE pr.token_hash = :hash AND pr.expires_at > NOW() LIMIT 1');
    $stmt->execute(['hash' => $tokenHash]);
    $validRow = $stmt->fetch();
}

if (is_post()) {
    verify_csrf();

    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if (!$validRow) {
        set_flash('error', 'Reset token is invalid or expired.');
        redirect('/forgot-password.php');
    }

    if ($password !== $confirmPassword || strlen($password) < 8) {
        set_flash('error', 'Password must match and be at least 8 characters.');
        redirect('/reset-password.php?token=' . urlencode($token));
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    db()->prepare('UPDATE users SET password_hash = :hash, failed_login_attempts = 0, locked_until = NULL WHERE id = :uid')
        ->execute(['hash' => $hash, 'uid' => (int) $validRow['user_id']]);
    db()->prepare('DELETE FROM password_resets WHERE user_id = :uid')->execute(['uid' => (int) $validRow['user_id']]);

    set_flash('success', 'Password has been reset successfully. Please log in.');
    redirect('/login.php');
}

$pageTitle = 'Reset Password';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width:520px;margin:0 auto">
  <h1>Reset Password</h1>
  <?php if (!$validRow): ?>
    <p class="muted">Reset token is invalid or expired.</p>
    <a class="btn" href="/forgot-password.php">Request new reset link</a>
  <?php else: ?>
    <form method="post" action="/reset-password.php">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <label>New Password <input type="password" name="password" minlength="8" required></label>
      <label>Confirm Password <input type="password" name="confirm_password" minlength="8" required></label>
      <button class="btn" type="submit">Reset Password</button>
    </form>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
