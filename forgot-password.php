<?php
require_once __DIR__ . '/includes/functions.php';

if (is_post()) {
    verify_csrf();
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $stmt->fetch();
    }
    set_flash('success', 'If an account exists, password reset instructions will be sent.');
    redirect('/forgot-password.php');
}

$pageTitle = 'Forgot Password';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width:520px;margin:0 auto">
  <h1>Forgot Password</h1>
  <p class="muted">Enter your email to request a password reset.</p>
  <form method="post" action="/forgot-password.php">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Email <input type="email" name="email" required></label>
    <button class="btn" type="submit">Request Reset</button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
