<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if (is_post()) {
    verify_csrf();
    $title = trim((string) ($_POST['title'] ?? ''));
    $body = trim((string) ($_POST['body'] ?? ''));

    if ($title !== '' && $body !== '') {
        db()->prepare('INSERT INTO notifications (user_id, title, body, type, created_at) VALUES (NULL,:title,:body,"announcement",NOW())')->execute(['title' => $title, 'body' => $body]);
        log_admin('announcement_create', $title);
        set_flash('success', 'Announcement posted.');
    }

    redirect('/admin/announcements.php');
}

$rows = db()->query("SELECT * FROM notifications WHERE type='announcement' AND user_id IS NULL ORDER BY created_at DESC LIMIT 100")->fetchAll();
$pageTitle = 'Announcements';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card" style="max-width:800px;margin:0 auto">
  <h1>Announcements</h1>
  <form method="post" action="/admin/announcements.php">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Title <input name="title" required></label>
    <label>Message <textarea name="body" required></textarea></label>
    <button class="btn" type="submit">Send Announcement</button>
  </form>
  <hr style="border-color:rgba(255,255,255,.1)">
  <?php foreach ($rows as $r): ?>
    <article class="card" style="margin:.5rem 0"><strong><?= e($r['title']) ?></strong><p class="muted"><?= e($r['body']) ?></p><small><?= e($r['created_at']) ?></small></article>
  <?php endforeach; ?>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
