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

$refCountStmt = db()->prepare('SELECT COUNT(*) total FROM referrals WHERE referrer_user_id = :uid');
$refCountStmt->execute(['uid' => $uid]);
$refCount = (int) ($refCountStmt->fetch()['total'] ?? 0);

$refEarnStmt = db()->prepare('SELECT COALESCE(SUM(commission_amount),0) total FROM referrals WHERE referrer_user_id = :uid');
$refEarnStmt->execute(['uid' => $uid]);
$refEarn = (float) ($refEarnStmt->fetch()['total'] ?? 0);

$boostPercent = referral_earning_boost_percent($refCount);
$salaryStatus = get_salary_status($uid, (string) $user['package_name']);

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
  <div class="card"><div class="muted">Active Package</div><div class="kpi"><?= e(package_label((string) $user['package_name'])) ?></div></div>
  <div class="card"><div class="muted">Today Earnings</div><div class="kpi counter" data-count="<?= e((string) $todayEarn) ?>">0</div></div>
  <div class="card"><div class="muted">Total Earnings</div><div class="kpi counter" data-count="<?= e((string) $totalEarn) ?>">0</div></div>
  <div class="card"><div class="muted">Referral Earnings</div><div class="kpi counter" data-count="<?= e((string) $refEarn) ?>">0</div></div>
  <div class="card"><div class="muted">Referral Boost</div><div class="kpi"><?= e((string) $boostPercent) ?>%</div></div>
  <div class="card"><div class="muted">Total Deposits</div><div class="kpi counter" data-count="<?= e((string) $totalDep) ?>">0</div></div>
  <div class="card"><div class="muted">Total Withdrawals</div><div class="kpi counter" data-count="<?= e((string) $totalWd) ?>">0</div></div>
  <div class="card"><div class="muted">Copy Trade Access</div><div class="kpi">Enabled</div></div>
  <div class="card"><div class="muted">Salary Status</div><div class="kpi"><?= $salaryStatus['eligible'] ? 'Eligible' : 'In Progress' ?></div></div>
</section>

<section class="card" style="margin-top:1rem">
  <h2>Monthly Salary Achievement</h2>
  <p class="muted">
    Plan: <?= e(package_label((string) $user['package_name'])) ?> •
    Target: <?= (int) $salaryStatus['required_referrals'] ?> <?= e(ucfirst((string) $salaryStatus['target_referral_plan'])) ?> referrals •
    Progress: <?= (int) $salaryStatus['qualified_referrals'] ?>/<?= (int) $salaryStatus['required_referrals'] ?>
  </p>
  <p>
    Monthly Reward: <strong><?= format_money((float) $salaryStatus['monthly_reward']) ?></strong> •
    Weekly Payout: <strong><?= format_money((float) $salaryStatus['weekly_reward']) ?></strong>
  </p>
</section>

<section class="grid grid-2" style="margin-top:1rem">
  <div class="card"><h2>Deposit History</h2><div class="table-wrap"><table class="table"><tbody><?php foreach ($deposits->fetchAll() as $row): ?><tr><td><?= e($row['created_at']) ?></td><td><?= format_money((float)$row['amount']) ?></td><td class="<?= e($row['status']) ?>"><?= e($row['status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
  <div class="card"><h2>Withdrawal History</h2><div class="table-wrap"><table class="table"><tbody><?php foreach ($withdrawals->fetchAll() as $row): ?><tr><td><?= e($row['created_at']) ?></td><td><?= format_money((float)$row['amount']) ?></td><td class="<?= e($row['status']) ?>"><?= e($row['status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
  <div class="card"><h2>Earnings History</h2><div class="table-wrap"><table class="table"><tbody><?php foreach ($earnings->fetchAll() as $row): ?><tr><td><?= e($row['created_at']) ?></td><td><?= format_money((float)$row['amount']) ?></td><td><?= e($row['source_type']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
  <div class="card"><h2>Trade History</h2><div class="table-wrap"><table class="table"><tbody><?php foreach ($trades->fetchAll() as $row): ?><tr><td><?= e($row['executed_at']) ?></td><td><?= e($row['signal_code_input']) ?></td><td><?= format_money((float)$row['estimated_amount']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</section>
<?php require_once __DIR__ . '/includes/user_nav.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
