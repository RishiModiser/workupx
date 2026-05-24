<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

if (is_post()) {
    verify_csrf();
    $signalCode = strtoupper(trim((string) ($_POST['signal_code'] ?? '')));

    $stmt = db()->prepare('SELECT * FROM trade_signals WHERE signal_code = :code AND is_active = 1 AND (scheduled_at IS NULL OR scheduled_at <= NOW()) LIMIT 1');
    $stmt->execute(['code' => $signalCode]);
    $signal = $stmt->fetch();

    if (!$signal) {
        set_flash('error', 'Signal code is invalid or inactive.');
        redirect('/trade.php');
    }

    $targetPackage = (string) ($signal['target_package'] ?? 'all');
    if ($targetPackage !== 'all' && $targetPackage !== (string) $user['package_name']) {
        set_flash('error', 'This copy-trade code is not available for your package.');
        redirect('/trade.php');
    }

    $referenceTime = (string) ($signal['scheduled_at'] ?: $signal['created_at']);
    $referenceTs = strtotime($referenceTime);
    $expiresInMinutes = max(1, (int) ($signal['expires_in_minutes'] ?? 60));
    if ($referenceTs !== false && time() > ($referenceTs + ($expiresInMinutes * 60))) {
        set_flash('error', 'This copy-trade code has expired.');
        redirect('/trade.php');
    }

    if ((int) ($signal['one_time_use'] ?? 1) === 1) {
        $usageCheck = db()->prepare('SELECT id FROM copy_trade_usage WHERE trade_signal_id = :signal_id AND user_id = :user_id LIMIT 1');
        $usageCheck->execute(['signal_id' => (int) $signal['id'], 'user_id' => (int) $user['id']]);
        if ($usageCheck->fetch()) {
            set_flash('error', 'This copy-trade code has already been used on your account.');
            redirect('/trade.php');
        }
    }

    $packageInfo = package_config((string) $user['package_name']);
    $packageAmount = (float) $packageInfo['deposit'];
    $base = (float) $user['balance'] > 0 ? (float) $user['balance'] : $packageAmount;
    $refCountStmt = db()->prepare('SELECT COUNT(*) total FROM referrals WHERE referrer_user_id = :uid');
    $refCountStmt->execute(['uid' => (int) $user['id']]);
    $boost = referral_earning_boost_percent((int) ($refCountStmt->fetch()['total'] ?? 0));
    $basePercent = max(0.1, (float) ($signal['profit_percent'] ?? 1.0));
    $percent = round($basePercent + $boost, 2);
    $estimated = round($base * ($percent / 100), 2);
    $status = 'estimated_gain';
    $note = 'Educational copy-trade simulation result based on admin-configured percentage.';

    $pdo = db();
    $pdo->beginTransaction();

    try {
        if ((int) ($signal['one_time_use'] ?? 1) === 1) {
            $pdo->prepare('INSERT INTO copy_trade_usage (trade_signal_id, user_id, used_at) VALUES (:signal_id,:user_id,NOW())')
                ->execute(['signal_id' => (int) $signal['id'], 'user_id' => (int) $user['id']]);
        }

        $insert = $pdo->prepare('INSERT INTO trades (user_id, trade_signal_id, signal_code_input, estimated_percent, estimated_amount, result_status, note, executed_at) VALUES (:user_id,:signal_id,:code,:percent,:amount,:status,:note,NOW())');
        $insert->execute([
            'user_id' => (int) $user['id'],
            'signal_id' => (int) $signal['id'],
            'code' => $signalCode,
            'percent' => $percent,
            'amount' => $estimated,
            'status' => $status,
            'note' => $note,
        ]);

        $tradeId = (int) $pdo->lastInsertId();
        $pdo->prepare('UPDATE users SET balance = balance + :amount, total_earnings = total_earnings + :amount WHERE id = :uid')
            ->execute(['amount' => $estimated, 'uid' => (int) $user['id']]);
        $earning = $pdo->prepare('INSERT INTO earnings (user_id, source_type, source_id, amount, note, created_at) VALUES (:user_id,"trade",:source_id,:amount,:note,NOW())');
        $earning->execute([
            'user_id' => (int) $user['id'],
            'source_id' => $tradeId,
            'amount' => $estimated,
            'note' => 'Educational copy-trade estimated profit credited to wallet.',
        ]);

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        set_flash('error', 'Copy-trade execution failed. Please try again.');
        redirect('/trade.php');
    }

    $notif = db()->prepare('INSERT INTO notifications (user_id, title, body, type, created_at) VALUES (:user_id,:title,:body,"trade",NOW())');
    $notif->execute([
        'user_id' => (int) $user['id'],
        'title' => 'Trade Simulation Complete',
        'body' => sprintf('Estimated Educational Result: +%s%% (%s).', $percent, $signalCode),
    ]);

    set_flash('success', sprintf('Estimated Educational Result: +%s%% (%s).', $percent, $signalCode));
    redirect('/trade.php');
}

$signals = db()->query('SELECT * FROM trade_signals WHERE is_active = 1 ORDER BY created_at DESC LIMIT 20')->fetchAll();
$historyStmt = db()->prepare('SELECT t.*, ts.pair_name FROM trades t LEFT JOIN trade_signals ts ON ts.id = t.trade_signal_id WHERE t.user_id = :user_id ORDER BY t.executed_at DESC LIMIT 20');
$historyStmt->execute(['user_id' => (int) $user['id']]);
$history = $historyStmt->fetchAll();

$pageTitle = 'Trade';
require_once __DIR__ . '/includes/header.php';
?>
<section class="grid grid-2">
  <div class="card">
    <h1>Educational Copy Trade</h1>
    <p class="muted">Paste admin-generated code. Codes are package-targeted with expiry windows and one-time controls.</p>
    <form method="post" action="/trade.php">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <label>Signal Code <input name="signal_code" maxlength="40" required></label>
      <button class="btn" type="submit">Run Simulated Trade</button>
    </form>
    <p class="muted">Estimated educational results only. Market risk applies and results are not guaranteed.</p>
  </div>
  <div class="card">
    <h2>Active Signals</h2>
    <div class="table-wrap"><table class="table"><thead><tr><th>Code</th><th>Pair</th><th>Plan</th><th>Profit</th><th>Expiry</th></tr></thead><tbody>
      <?php foreach ($signals as $s): ?>
      <tr><td><?= e($s['signal_code']) ?></td><td><?= e($s['pair_name']) ?></td><td><?= e(ucfirst((string) ($s['target_package'] ?? 'all'))) ?></td><td><?= e((string) ($s['profit_percent'] ?? '1')) ?>%</td><td><?= (int) ($s['expires_in_minutes'] ?? 60) ?>m</td></tr>
      <?php endforeach; ?>
    </tbody></table></div>
  </div>
</section>
<section class="card" style="margin-top:1rem">
  <h2>Trade History</h2>
  <div class="table-wrap"><table class="table"><thead><tr><th>Time</th><th>Code</th><th>Pair</th><th>Result</th><th>Amount</th></tr></thead><tbody>
    <?php foreach ($history as $h): ?>
    <tr><td><?= e($h['executed_at']) ?></td><td><?= e($h['signal_code_input']) ?></td><td><?= e($h['pair_name'] ?? '-') ?></td><td><?= e($h['result_status']) ?></td><td><?= format_money((float) $h['estimated_amount']) ?></td></tr>
    <?php endforeach; ?>
  </tbody></table></div>
</section>
<?php require_once __DIR__ . '/includes/user_nav.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
