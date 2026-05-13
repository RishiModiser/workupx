<?php
require_once __DIR__ . '/includes/auth.php';

if (is_post()) {
    verify_csrf();

    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $remember = !empty($_POST['remember']);

    $stmt = db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        set_flash('error', 'Invalid credentials.');
        redirect('/login.php');
    }

    try {
        throttle_login($user);
    } catch (RuntimeException $e) {
        set_flash('error', $e->getMessage());
        redirect('/login.php');
    }

    if (($user['is_banned'] ?? 0) === 1) {
        set_flash('error', 'Your account is restricted.');
        redirect('/login.php');
    }

    if (!password_verify($password, (string) $user['password_hash'])) {
        $attempts = ((int) $user['failed_login_attempts']) + 1;
        register_failed_attempt((int) $user['id'], $attempts);
        set_flash('error', 'Invalid credentials.');
        redirect('/login.php');
    }

    $resetStmt = db()->prepare('UPDATE users SET failed_login_attempts = 0, locked_until = NULL WHERE id = :id');
    $resetStmt->execute(['id' => (int) $user['id']]);

    login_user($user, $remember);
    set_flash('success', 'Welcome back to WORKUPX.');

    if (($user['role'] ?? ROLE_USER) === ROLE_ADMIN) {
        redirect('/admin/index.php');
    }
    redirect('/quote.php');
}

$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width:520px;margin:0 auto">
  <h1>Login</h1>
  <form method="post" action="/login.php">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Email <input type="email" name="email" required></label>
    <label>Password <input type="password" name="password" required></label>
    <label><input type="checkbox" name="remember" value="1"> Remember me</label>
    <button class="btn" type="submit">Secure Login</button>
    <a href="/forgot-password.php" class="muted">Forgot password?</a>
  </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
