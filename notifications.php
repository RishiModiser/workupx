<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();
$uid = (int) $user['id'];

$mark = db()->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = :uid');
$mark->execute(['uid' => $uid]);

$stmt = db()->prepare('SELECT * FROM notifications WHERE user_id = :uid OR user_id IS NULL ORDER BY created_at DESC LIMIT 50');
$stmt->execute(['uid' => $uid]);
$rows = $stmt->fetchAll();

$pageTitle = 'Notifications';
require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h1>Notifications</h1>
  <?php foreach ($rows as $n): ?>
    <article class="card" style="margin:.6rem 0"><strong><?= e($n['title']) ?></strong><p class="muted"><?= e($n['body']) ?></p><small><?= e($n['created_at']) ?></small></article>
  <?php endforeach; ?>
</section>
<?php require_once __DIR__ . '/includes/user_nav.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
