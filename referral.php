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
$boostPercent = referral_earning_boost_percent($refCount);
$boostPerReferral = (float) app_setting('referral_earning_boost_per_user_percent', '0.5');
$salaryStatus = get_salary_status($uid, (string) $user['package_name']);

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
  <div class="card"><div class="muted">Earning Boost</div><div class="kpi accent"><?= e((string) $boostPercent) ?>%</div></div>
</section>
<section class="card" style="margin-top:1rem">
  <h2>Your Referral Access</h2>
  <p>Your code: <strong><?= e($user['referral_code']) ?></strong></p>
  <input value="<?= e($link) ?>" readonly>
  <button class="btn btn-sm" data-copy="<?= e($link) ?>">Copy</button>
</section>
<section class="grid grid-3" style="margin-top:1rem">
  <article class="card">
    <h3>Salary Qualification</h3>
    <p><?= (int) $salaryStatus['qualified_referrals'] ?> / <?= (int) $salaryStatus['required_referrals'] ?> <?= e(ucfirst((string) $salaryStatus['target_referral_plan'])) ?> referrals</p>
    <span class="badge <?= $salaryStatus['eligible'] ? 'approved' : 'pending' ?>"><?= $salaryStatus['eligible'] ? 'Eligible' : 'In Progress' ?></span>
  </article>
  <article class="card">
    <h3>Monthly Reward</h3>
    <p><?= format_money((float) $salaryStatus['monthly_reward']) ?> credited over 4 weeks</p>
    <span class="badge approved"><?= format_money((float) $salaryStatus['weekly_reward']) ?> / week</span>
  </article>
  <article class="card">
    <h3>Referral Earning Boost</h3>
    <p>+<?= e((string) $boostPerReferral) ?>% per referral based on platform setting</p>
    <span class="badge approved">Current +<?= e((string) $boostPercent) ?>%</span>
  </article>
</section>
<section class="card" style="margin-top:1rem">
  <h2>Referral Network</h2>
  <div class="table-wrap"><table class="table"><thead><tr><th>Name</th><th>Joined</th></tr></thead><tbody><?php foreach ($tree as $row): ?><tr><td><?= e($row['full_name']) ?></td><td><?= e($row['created_at']) ?></td></tr><?php endforeach; ?></tbody></table></div>
</section>
<?php require_once __DIR__ . '/includes/user_nav.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
