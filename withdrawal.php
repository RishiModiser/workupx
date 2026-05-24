<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

if (is_post()) {
    verify_csrf();

    $amount = (float) ($_POST['amount'] ?? 0);
    $wallet = trim((string) ($_POST['wallet_address'] ?? ''));
    $network = trim((string) ($_POST['network'] ?? ''));

    if ($amount < MIN_WITHDRAWAL_AMOUNT || $wallet === '' || !in_array($network, SUPPORTED_WITHDRAWAL_NETWORKS, true)) {
        set_flash('error', 'Please fill all fields correctly, use a valid network, and request at least ' . format_money(MIN_WITHDRAWAL_AMOUNT) . '.');
        redirect('/withdrawal.php');
    }

    $availableBalance = calculate_available_withdrawal_balance((int) $user['id']);

    if ($amount > $availableBalance) {
        set_flash('error', 'Insufficient balance for this request.');
        redirect('/withdrawal.php');
    }

    $feePercent = get_withdrawal_fee_percent();
    $feeAmount = round($amount * ($feePercent / 100), 2);
    $netAmount = round($amount - $feeAmount, 2);

    $stmt = db()->prepare('INSERT INTO withdrawals (user_id, amount, fee_percent, fee_amount, net_amount, wallet_address, network, status, created_at, updated_at) VALUES (:uid,:amount,:fee_percent,:fee_amount,:net_amount,:wallet,:network,"pending",NOW(),NOW())');
    $stmt->execute([
        'uid' => (int) $user['id'],
        'amount' => $amount,
        'fee_percent' => $feePercent,
        'fee_amount' => $feeAmount,
        'net_amount' => $netAmount,
        'wallet' => $wallet,
        'network' => $network,
    ]);

    set_flash('success', 'Withdrawal request submitted. Net payout after fee: ' . format_money($netAmount) . '.');
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
  <p class="muted">Withdrawals processed within <?= WITHDRAWAL_PROCESSING_HOURS ?> hours. The current admin-configured fee is <?= e((string) get_withdrawal_fee_percent()) ?>%.</p>
  <form method="post" action="/withdrawal.php">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Wallet Address <input name="wallet_address" maxlength="190" required></label>
    <label>Network
      <select name="network" required>
        <?php foreach (SUPPORTED_WITHDRAWAL_NETWORKS as $supportedNetwork): ?>
          <option value="<?= e($supportedNetwork) ?>"><?= e($supportedNetwork) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Amount (USD) <input type="number" step="0.01" min="<?= MIN_WITHDRAWAL_AMOUNT ?>" name="amount" required></label>
    <button class="btn" type="submit">Submit Withdrawal</button>
  </form>
</section>
<section class="card" style="margin-top:1rem">
  <h2>Withdrawal History</h2>
  <div class="table-wrap"><table class="table"><thead><tr><th>Date</th><th>Request</th><th>Fee</th><th>Net</th><th>Network</th><th>Status</th></tr></thead><tbody><?php foreach ($withdrawals as $w): ?><tr><td><?= e($w['created_at']) ?></td><td><?= format_money((float)$w['amount']) ?></td><td><?= format_money((float)($w['fee_amount'] ?? 0)) ?></td><td><?= format_money((float)($w['net_amount'] ?? $w['amount'])) ?></td><td><?= e($w['network']) ?></td><td class="<?= e($w['status']) ?>"><?= e($w['status']) ?></td></tr><?php endforeach; ?></tbody></table></div>
</section>
<?php require_once __DIR__ . '/includes/user_nav.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
