<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

$uid = (int) $user['id'];
$todayEarnStmt = db()->prepare('SELECT COALESCE(SUM(amount),0) total FROM earnings WHERE user_id = :uid AND DATE(created_at)=CURDATE()');
$todayEarnStmt->execute(['uid' => $uid]);
$todayEarn = (float) ($todayEarnStmt->fetch()['total'] ?? 0);

$totalEarnStmt = db()->prepare('SELECT COALESCE(SUM(amount),0) total FROM earnings WHERE user_id = :uid');
$totalEarnStmt->execute(['uid' => $uid]);
$totalEarn = (float) ($totalEarnStmt->fetch()['total'] ?? 0);

$depStmt = db()->prepare('SELECT COALESCE(SUM(amount),0) total FROM deposits WHERE user_id = :uid AND status = "approved"');
$depStmt->execute(['uid' => $uid]);
$totalDep = (float) ($depStmt->fetch()['total'] ?? 0);

$wdStmt = db()->prepare('SELECT COALESCE(SUM(amount),0) total FROM withdrawals WHERE user_id = :uid AND status = "approved"');
$wdStmt->execute(['uid' => $uid]);
$totalWd = (float) ($wdStmt->fetch()['total'] ?? 0);

$deposits = db()->prepare('SELECT * FROM deposits WHERE user_id = :uid ORDER BY created_at DESC LIMIT 10');
$deposits->execute(['uid' => $uid]);
$withdrawals = db()->prepare('SELECT * FROM withdrawals WHERE user_id = :uid ORDER BY created_at DESC LIMIT 10');
$withdrawals->execute(['uid' => $uid]);
$earnings = db()->prepare('SELECT * FROM earnings WHERE user_id = :uid ORDER BY created_at DESC LIMIT 10');
$earnings->execute(['uid' => $uid]);
$trades = db()->prepare('SELECT * FROM trades WHERE user_id = :uid ORDER BY executed_at DESC LIMIT 10');
$trades->execute(['uid' => $uid]);

$pageTitle = 'Assets';
require_once __DIR__ . '/includes/header.php';
?>
<section class="grid grid-3">
  <div class="card"><div class="muted">Total Balance</div><div class="kpi counter" data-count="<?= e((string) ((float) $user['balance'])) ?>">0</div></div>
  <div class="card"><div class="muted">Today Earnings</div><div class="kpi counter" data-count="<?= e((string) $todayEarn) ?>">0</div></div>
  <div class="card"><div class="muted">Total Earnings</div><div class="kpi counter" data-count="<?= e((string) $totalEarn) ?>">0</div></div>
  <div class="card"><div class="muted">Total Deposits</div><div class="kpi counter" data-count="<?= e((string) $totalDep) ?>">0</div></div>
  <div class="card"><div class="muted">Total Withdrawals</div><div class="kpi counter" data-count="<?= e((string) $totalWd) ?>">0</div></div>
  <div class="card"><div class="muted">Package</div><div class="kpi"><?= e(strtoupper((string) $user['package_name'])) ?></div></div>
</section>

<section style="display:flex;gap:.8rem;margin-top:1rem;flex-wrap:wrap">
  <a class="btn" href="/deposit.php">+ Deposit</a>
  <a class="btn btn-outline" href="/withdrawal.php">Withdraw</a>
  <a class="btn btn-outline" href="/trade.php">Trade</a>
  <a class="btn btn-outline" href="/referral.php">Referral</a>
</section>

<section class="grid grid-2" style="margin-top:1rem">
  <div class="card"><h2>Deposit History</h2><div class="table-wrap"><table class="table"><tbody><?php foreach ($deposits->fetchAll() as $row): ?><tr><td><?= e($row['created_at']) ?></td><td><?= format_money((float)$row['amount']) ?></td><td class="<?= e($row['status']) ?>"><?= e($row['status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
  <div class="card"><h2>Withdrawal History</h2><div class="table-wrap"><table class="table"><tbody><?php foreach ($withdrawals->fetchAll() as $row): ?><tr><td><?= e($row['created_at']) ?></td><td><?= format_money((float)$row['amount']) ?></td><td class="<?= e($row['status']) ?>"><?= e($row['status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
  <div class="card"><h2>Earnings History</h2><div class="table-wrap"><table class="table"><tbody><?php foreach ($earnings->fetchAll() as $row): ?><tr><td><?= e($row['created_at']) ?></td><td><?= format_money((float)$row['amount']) ?></td><td><?= e($row['source_type']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
  <div class="card"><h2>Trade History</h2><div class="table-wrap"><table class="table"><tbody><?php foreach ($trades->fetchAll() as $row): ?><tr><td><?= e($row['executed_at']) ?></td><td><?= e($row['signal_code_input']) ?></td><td><?= format_money((float)$row['estimated_amount']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</section>
<?php require_once __DIR__ . '/includes/user_nav.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
