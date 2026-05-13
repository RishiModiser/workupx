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

    $min = (float) app_setting('estimated_profit_min', '1.5');
    $max = (float) app_setting('estimated_profit_max', '4.8');
    $lossChance = (int) app_setting('estimated_loss_chance_percent', '28');
    if ($min > $max) {
        [$min, $max] = [$max, $min];
    }
    $isLoss = random_int(1, 100) <= max(0, min(100, $lossChance));
    $percent = round((float) (random_int((int) ($min * 100), (int) ($max * 100)) / 100), 2);
    $packageAmount = match ($user['package_name']) {
        'premium' => 200.0,
        'advanced' => 100.0,
        default => 50.0,
    };
    $base = (float) $user['balance'] > 0 ? (float) $user['balance'] : $packageAmount;
    $estimated = round($base * ($percent / 100), 2);
    $status = $isLoss ? 'estimated_loss' : 'estimated_gain';
    $note = 'Estimated Educational Result based on admin-configured parameters.';

    $insert = db()->prepare('INSERT INTO trades (user_id, trade_signal_id, signal_code_input, estimated_percent, estimated_amount, result_status, note, executed_at) VALUES (:user_id,:signal_id,:code,:percent,:amount,:status,:note,NOW())');
    $insert->execute([
        'user_id' => (int) $user['id'],
        'signal_id' => (int) $signal['id'],
        'code' => $signalCode,
        'percent' => $isLoss ? -$percent : $percent,
        'amount' => $estimated,
        'status' => $status,
        'note' => $note,
    ]);

    if (!$isLoss) {
        $earning = db()->prepare('INSERT INTO earnings (user_id, source_type, source_id, amount, note, created_at) VALUES (:user_id,"trade",:source_id,:amount,:note,NOW())');
        $earning->execute([
            'user_id' => (int) $user['id'],
            'source_id' => (int) db()->lastInsertId(),
            'amount' => $estimated,
            'note' => 'Estimated Profit from educational trade simulation.',
        ]);
    }

    $notif = db()->prepare('INSERT INTO notifications (user_id, title, body, type, created_at) VALUES (:user_id,:title,:body,"trade",NOW())');
    $notif->execute([
        'user_id' => (int) $user['id'],
        'title' => 'Trade Simulation Complete',
        'body' => sprintf('Estimated Educational Result: %s%s (%s).', $isLoss ? '-' : '+', $percent, $signalCode),
    ]);

    set_flash('success', sprintf('Estimated Educational Result: %s%s%% (%s).', $isLoss ? '-' : '+', $percent, $signalCode));
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
    <p class="muted">Paste admin-generated code. This is simulation only.</p>
    <form method="post" action="/trade.php">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <label>Signal Code <input name="signal_code" maxlength="40" required></label>
      <button class="btn" type="submit">Run Simulated Trade</button>
    </form>
    <p class="muted">Estimated Educational Result only. No guaranteed profits. If balance is zero, package baseline is used for education-only estimation.</p>
  </div>
  <div class="card">
    <h2>Active Signals</h2>
    <div class="table-wrap"><table class="table"><thead><tr><th>Code</th><th>Pair</th><th>Direction</th></tr></thead><tbody>
      <?php foreach ($signals as $s): ?>
      <tr><td><?= e($s['signal_code']) ?></td><td><?= e($s['pair_name']) ?></td><td><?= e($s['direction']) ?></td></tr>
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
