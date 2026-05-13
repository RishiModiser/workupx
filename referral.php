<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

$uid = (int) $user['id'];
$countStmt = db()->prepare('SELECT COUNT(*) as total FROM referrals WHERE referrer_user_id = :uid');
$countStmt->execute(['uid' => $uid]);
$refCount = (int) ($countStmt->fetch()['total'] ?? 0);

$earnStmt = db()->prepare('SELECT COALESCE(SUM(commission_amount),0) total FROM referrals WHERE referrer_user_id = :uid');
$earnStmt->execute(['uid' => $uid]);
$refEarn = (float) ($earnStmt->fetch()['total'] ?? 0);

$treeStmt = db()->prepare('SELECT u.full_name, u.created_at FROM referrals r INNER JOIN users u ON u.id = r.referred_user_id WHERE r.referrer_user_id = :uid ORDER BY r.created_at DESC LIMIT 50');
$treeStmt->execute(['uid' => $uid]);
$tree = $treeStmt->fetchAll();

$link = APP_URL . '/register.php?ref=' . urlencode((string) $user['referral_code']);
$pageTitle = 'Referral';
require_once __DIR__ . '/includes/header.php';
?>
<section class="grid grid-3">
  <div class="card"><div class="muted">Referral Count</div><div class="kpi" data-count="<?= $refCount ?>"><?= $refCount ?></div></div>
  <div class="card"><div class="muted">Referral Earnings</div><div class="kpi"><?= format_money($refEarn) ?></div></div>
  <div class="card"><div class="muted">Your Code</div><div class="kpi accent"><?= e($user['referral_code']) ?></div></div>
</section>
<section class="card" style="margin-top:1rem">
  <h2>Unique Referral Link</h2>
  <input value="<?= e($link) ?>" readonly>
  <button class="btn btn-sm" data-copy="<?= e($link) ?>">Copy</button>
</section>
<section class="grid grid-3" style="margin-top:1rem">
  <article class="card"><h3>20 Referrals</h3><p>Bike Reward</p><span class="badge <?= $refCount >= 20 ? 'approved' : 'pending' ?>"><?= $refCount >= 20 ? 'Unlocked' : 'In Progress' ?></span></article>
  <article class="card"><h3>40 Referrals</h3><p>Europe Trip Reward</p><span class="badge <?= $refCount >= 40 ? 'approved' : 'pending' ?>"><?= $refCount >= 40 ? 'Unlocked' : 'In Progress' ?></span></article>
  <article class="card"><h3>80 Referrals</h3><p>Car Reward</p><span class="badge <?= $refCount >= 80 ? 'approved' : 'pending' ?>"><?= $refCount >= 80 ? 'Unlocked' : 'In Progress' ?></span></article>
</section>
<section class="card" style="margin-top:1rem">
  <h2>Referral Network</h2>
  <div class="table-wrap"><table class="table"><thead><tr><th>Name</th><th>Joined</th></tr></thead><tbody><?php foreach ($tree as $row): ?><tr><td><?= e($row['full_name']) ?></td><td><?= e($row['created_at']) ?></td></tr><?php endforeach; ?></tbody></table></div>
</section>
<?php require_once __DIR__ . '/includes/user_nav.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
