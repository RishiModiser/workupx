<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Copy Trade Community';
$signals = db()->query('SELECT * FROM trade_signals WHERE is_active = 1 AND (scheduled_at IS NULL OR scheduled_at <= NOW()) ORDER BY created_at DESC LIMIT 50')->fetchAll();
$announcements = db()->query("SELECT * FROM notifications WHERE type='announcement' AND user_id IS NULL ORDER BY created_at DESC LIMIT 20")->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>
<section class="grid grid-2">
  <div class="card">
    <h1>Community Trade Ideas</h1>
    <p class="muted">Educational Trade Signals only.</p>
    <div class="table-wrap"><table class="table"><thead><tr><th>Code</th><th>Pair</th><th>Direction</th><th>Category</th></tr></thead><tbody>
      <?php foreach ($signals as $s): ?>
      <tr><td><?= e($s['signal_code']) ?></td><td><?= e($s['pair_name']) ?></td><td><?= e($s['direction']) ?></td><td><?= e($s['category']) ?></td></tr>
      <?php endforeach; ?>
    </tbody></table></div>
  </div>
  <div class="card">
    <h2>Announcements</h2>
    <?php foreach ($announcements as $n): ?>
      <article class="card" style="margin:.5rem 0"><strong><?= e($n['title']) ?></strong><p class="muted"><?= e($n['body']) ?></p></article>
    <?php endforeach; ?>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
