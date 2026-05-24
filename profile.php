<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

if (is_post()) {
    verify_csrf();
    $name = trim((string) ($_POST['full_name'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $package = (string) ($_POST['package_name'] ?? 'silver');

    if ($name === '' || $phone === '') {
        set_flash('error', 'Name and phone are required.');
        redirect('/profile.php');
    }

    if (!in_array($package, ['silver', 'gold', 'diamond'], true)) {
        $package = 'silver';
    }

    $stmt = db()->prepare('UPDATE users SET full_name = :name, phone = :phone, package_name = :package WHERE id = :id');
    $stmt->execute(['name' => $name, 'phone' => $phone, 'package' => $package, 'id' => (int) $user['id']]);

    set_flash('success', 'Profile updated.');
    redirect('/profile.php');
}

$notifStmt = db()->prepare('SELECT COUNT(*) as total FROM notifications WHERE user_id = :uid AND is_read = 0');
$notifStmt->execute(['uid' => (int) $user['id']]);
$notifCount = (int) ($notifStmt->fetch()['total'] ?? 0);

$pageTitle = 'Profile';
require_once __DIR__ . '/includes/header.php';
?>
<section class="card" style="max-width:680px;margin:0 auto">
  <h1>Profile Settings</h1>
  <p><a href="/notifications.php">Notifications (<?= $notifCount ?> unread)</a></p>
  <form method="post" action="/profile.php">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Full Name <input name="full_name" value="<?= e($user['full_name']) ?>" required></label>
    <label>Email <input value="<?= e($user['email']) ?>" readonly></label>
    <label>Phone <input name="phone" value="<?= e($user['phone']) ?>" required></label>
    <label>Package
      <select name="package_name">
        <option value="silver" <?= $user['package_name'] === 'silver' ? 'selected' : '' ?>>Silver</option>
        <option value="gold" <?= $user['package_name'] === 'gold' ? 'selected' : '' ?>>Gold</option>
        <option value="diamond" <?= $user['package_name'] === 'diamond' ? 'selected' : '' ?>>Diamond</option>
      </select>
    </label>
    <button class="btn" type="submit">Save Settings</button>
  </form>
</section>
<?php require_once __DIR__ . '/includes/user_nav.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
