<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

if (is_post()) {
    verify_csrf();
    $action = (string) ($_POST['form_action'] ?? 'profile');

    if ($action === 'profile') {
        $name = trim((string) ($_POST['full_name'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $package = (string) ($_POST['package_name'] ?? 'starter');

        if ($name === '' || $phone === '') {
            set_flash('error', 'Name and phone are required.');
            redirect('/profile.php');
        }

        if (!in_array($package, ['starter', 'advanced', 'premium'], true)) {
            $package = 'starter';
        }

        $stmt = db()->prepare('UPDATE users SET full_name = :name, phone = :phone, package_name = :package WHERE id = :id');
        $stmt->execute(['name' => $name, 'phone' => $phone, 'package' => $package, 'id' => (int) $user['id']]);

        set_flash('success', 'Profile updated.');
    }

    if ($action === 'password') {
        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');

        if (!password_verify($current, (string) $user['password_hash'])) {
            set_flash('error', 'Current password is incorrect.');
            redirect('/profile.php');
        }

        if ($new !== $confirm || strlen($new) < 8) {
            set_flash('error', 'New password must match and be at least 8 characters.');
            redirect('/profile.php');
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);
        db()->prepare('UPDATE users SET password_hash = :hash WHERE id = :id')->execute(['hash' => $hash, 'id' => (int) $user['id']]);
        set_flash('success', 'Password changed successfully.');
    }

    redirect('/profile.php');
}

$notifStmt = db()->prepare('SELECT COUNT(*) as total FROM notifications WHERE user_id = :uid AND is_read = 0');
$notifStmt->execute(['uid' => (int) $user['id']]);
$notifCount = (int) ($notifStmt->fetch()['total'] ?? 0);

$pageTitle = 'Profile';
require_once __DIR__ . '/includes/header.php';
?>
<section class="grid grid-2">
  <div class="card">
    <h1>Profile Settings</h1>
    <p><a href="/notifications.php">Notifications (<?= $notifCount ?> unread)</a></p>
    <form method="post" action="/profile.php">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="form_action" value="profile">
      <label>Full Name <input name="full_name" value="<?= e($user['full_name']) ?>" required></label>
      <label>Email <input value="<?= e($user['email']) ?>" readonly></label>
      <label>Phone <input name="phone" value="<?= e($user['phone']) ?>" required></label>
      <label>Referral Code <input value="<?= e($user['referral_code']) ?>" readonly></label>
      <label>Package
        <select name="package_name">
          <option value="starter" <?= $user['package_name'] === 'starter' ? 'selected' : '' ?>>$50 Starter</option>
          <option value="advanced" <?= $user['package_name'] === 'advanced' ? 'selected' : '' ?>>$100 Advanced</option>
          <option value="premium" <?= $user['package_name'] === 'premium' ? 'selected' : '' ?>>$200 Premium</option>
        </select>
      </label>
      <button class="btn" type="submit">Save Profile</button>
    </form>
  </div>
  <div class="card">
    <h2>Change Password</h2>
    <form method="post" action="/profile.php">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="form_action" value="password">
      <label>Current Password <input type="password" name="current_password" required></label>
      <label>New Password <input type="password" name="new_password" minlength="8" required></label>
      <label>Confirm New Password <input type="password" name="confirm_password" minlength="8" required></label>
      <button class="btn btn-outline" type="submit">Change Password</button>
    </form>
  </div>
</section>
<?php require_once __DIR__ . '/includes/user_nav.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
