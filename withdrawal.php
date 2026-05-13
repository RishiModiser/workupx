<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

if (is_post()) {
    verify_csrf();

    $amount = (float) ($_POST['amount'] ?? 0);
    $wallet = trim((string) ($_POST['wallet_address'] ?? ''));
    $network = trim((string) ($_POST['network'] ?? ''));

    if ($amount < MIN_WITHDRAWAL_AMOUNT || $wallet === '' || $network === '') {
        set_flash('error', 'Please fill all fields and request at least ' . format_money(MIN_WITHDRAWAL_AMOUNT) . '.');
        redirect('/withdrawal.php');
    }

    $availableBalance = calculate_available_withdrawal_balance((int) $user['id']);

    if ($amount > $availableBalance) {
        set_flash('error', 'Insufficient balance for this request.');
        redirect('/withdrawal.php');
    }

    $stmt = db()->prepare('INSERT INTO withdrawals (user_id, amount, wallet_address, network, status, created_at, updated_at) VALUES (:uid,:amount,:wallet,:network,"pending",NOW(),NOW())');
    $stmt->execute(['uid' => (int) $user['id'], 'amount' => $amount, 'wallet' => $wallet, 'network' => $network]);

    set_flash('success', 'Withdrawal request submitted. Withdrawals processed within ' . WITHDRAWAL_PROCESSING_HOURS . ' hours.');
    redirect('/withdrawal.php');
}

$wdStmt = db()->prepare('SELECT * FROM withdrawals WHERE user_id = :uid ORDER BY created_at DESC LIMIT 20');
$wdStmt->execute(['uid' => (int) $user['id']]);
$withdrawals = $wdStmt->fetchAll();

$pageTitle = 'Withdrawal';
require_once __DIR__ . '/includes/header.php';
?>
<section class="card" style="max-width:720px;margin:0 auto">
  <h1>Withdrawal Request</h1>
  <p class="muted">Withdrawals processed within <?= WITHDRAWAL_PROCESSING_HOURS ?> hours.</p>
  <form method="post" action="/withdrawal.php">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Wallet Address <input name="wallet_address" maxlength="190" required></label>
    <label>Network <input name="network" maxlength="60" placeholder="BEP20 / ERC20" required></label>
    <label>Amount (USD) <input type="number" step="0.01" min="<?= MIN_WITHDRAWAL_AMOUNT ?>" name="amount" required></label>
    <button class="btn" type="submit">Submit Withdrawal</button>
  </form>
</section>
<section class="card" style="margin-top:1rem">
  <h2>Withdrawal History</h2>
  <div class="table-wrap"><table class="table"><thead><tr><th>Date</th><th>Amount</th><th>Network</th><th>Status</th></tr></thead><tbody><?php foreach ($withdrawals as $w): ?><tr><td><?= e($w['created_at']) ?></td><td><?= format_money((float)$w['amount']) ?></td><td><?= e($w['network']) ?></td><td class="<?= e($w['status']) ?>"><?= e($w['status']) ?></td></tr><?php endforeach; ?></tbody></table></div>
</section>
<?php require_once __DIR__ . '/includes/user_nav.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
