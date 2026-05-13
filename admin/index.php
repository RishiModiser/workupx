<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$stats = [
    'users' => (int) (db()->query('SELECT COUNT(*) c FROM users WHERE role = "user"')->fetch()['c'] ?? 0),
    'deposits' => (float) (db()->query('SELECT COALESCE(SUM(amount),0) c FROM deposits WHERE status="approved"')->fetch()['c'] ?? 0),
    'withdrawals' => (float) (db()->query('SELECT COALESCE(SUM(amount),0) c FROM withdrawals WHERE status="approved"')->fetch()['c'] ?? 0),
    'referrals' => (float) (db()->query('SELECT COALESCE(SUM(commission_amount),0) c FROM referrals')->fetch()['c'] ?? 0),
    'pending' => (int) ((db()->query('SELECT COUNT(*) c FROM deposits WHERE status="pending"')->fetch()['c'] ?? 0) + (db()->query('SELECT COUNT(*) c FROM withdrawals WHERE status="pending"')->fetch()['c'] ?? 0)),
];

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
  <h1>Super Admin Dashboard</h1>
  <p><a href="/admin/users.php">Users</a> • <a href="/admin/deposits.php">Deposits</a> • <a href="/admin/withdrawals.php">Withdrawals</a> • <a href="/admin/signals.php">Signals</a> • <a href="/admin/announcements.php">Announcements</a> • <a href="/admin/earnings.php">Earnings</a> • <a href="/admin/settings.php">Settings</a> • <a href="/admin/logout.php">Logout</a></p>
</section>
<section class="grid grid-3" style="margin-top:1rem">
  <div class="card"><div class="muted">Total Users</div><div class="kpi" data-count="<?= $stats['users'] ?>"><?= $stats['users'] ?></div></div>
  <div class="card"><div class="muted">Total Deposits</div><div class="kpi"><?= format_money($stats['deposits']) ?></div></div>
  <div class="card"><div class="muted">Total Withdrawals</div><div class="kpi"><?= format_money($stats['withdrawals']) ?></div></div>
  <div class="card"><div class="muted">Referral Commissions</div><div class="kpi"><?= format_money($stats['referrals']) ?></div></div>
  <div class="card"><div class="muted">Pending Requests</div><div class="kpi"><?= $stats['pending'] ?></div></div>
  <div class="card"><div class="muted">Notice</div><div class="muted"><?= e(app_setting('site_notice', 'Educational mode')) ?></div></div>
</section>
<section class="card" style="margin-top:1rem"><h2>Analytics</h2><div class="skeleton"></div><div style="height:8px"></div><div class="skeleton"></div></section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
