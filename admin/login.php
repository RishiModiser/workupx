<?php
require_once __DIR__ . '/../includes/auth.php';

if (is_post()) {
    verify_csrf();
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT * FROM users WHERE email = :email AND role = "admin" LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, (string) $user['password_hash'])) {
        set_flash('error', 'Invalid admin credentials.');
        redirect('/admin/login.php');
    }

    login_user($user, false);
    log_admin('admin_login', 'Admin authenticated');
    redirect('/admin/index.php');
}

$pageTitle = 'Admin Login';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="card" style="max-width:520px;margin:0 auto">
  <h1>Admin Login</h1>
  <form method="post" action="/admin/login.php">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Email <input type="email" name="email" required></label>
    <label>Password <input type="password" name="password" required></label>
    <button class="btn" type="submit">Login</button>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
