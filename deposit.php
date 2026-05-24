<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

$usdtTrc20Wallet = app_setting('usdt_trc20_wallet_address', 'DEMO_TRC20_ADDRESS');
$usdtBep20Wallet = app_setting('usdt_bep20_wallet_address', 'DEMO_BEP20_ADDRESS');

if (is_post()) {
    verify_csrf();

    $amount = (float) ($_POST['amount'] ?? 0);
    $asset = (string) ($_POST['asset'] ?? 'USDT_TRC20');

    if ($amount < MIN_DEPOSIT_AMOUNT || !in_array($asset, ['USDT_TRC20', 'USDT_BEP20'], true)) {
        set_flash('error', 'Invalid deposit details.');
        redirect('/deposit.php');
    }

    $wallet = match ($asset) {
        'USDT_TRC20' => $usdtTrc20Wallet,
        'USDT_BEP20' => $usdtBep20Wallet,
        default => ''
    };

    if ($wallet === '') {
        set_flash('error', 'Invalid asset wallet configuration.');
        redirect('/deposit.php');
    }

    try {
        $screenshot = upload_image($_FILES['screenshot'] ?? []);
        $stmt = db()->prepare('INSERT INTO deposits (user_id, amount, asset, wallet_address, screenshot_path, status, created_at, updated_at) VALUES (:uid,:amount,:asset,:wallet,:screenshot,"pending",NOW(),NOW())');
        $stmt->execute([
            'uid' => (int) $user['id'],
            'amount' => $amount,
            'asset' => $asset,
            'wallet' => $wallet,
            'screenshot' => $screenshot,
        ]);

        set_flash('success', 'Deposit request submitted. Confirm on WhatsApp for manual verification.');
    } catch (Throwable $e) {
        set_flash('error', 'Upload failed. Please verify image format, file size, and try again.');
    }

    redirect('/deposit.php');
}

$depStmt = db()->prepare('SELECT * FROM deposits WHERE user_id = :uid ORDER BY created_at DESC LIMIT 20');
$depStmt->execute(['uid' => (int) $user['id']]);
$deposits = $depStmt->fetchAll();

$pageTitle = 'Deposit';
require_once __DIR__ . '/includes/header.php';
?>
<section class="grid grid-2">
  <div class="card">
    <h1>Manual Deposit</h1>
    <form method="post" action="/deposit.php" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <label>Amount (USD) <input type="number" name="amount" step="0.01" min="<?= MIN_DEPOSIT_AMOUNT ?>" required></label>
      <label>Asset
        <select name="asset">
          <option value="USDT_TRC20">USDT (TRC20)</option>
          <option value="USDT_BEP20">USDT (BEP20)</option>
        </select>
      </label>
      <label>Upload Payment Screenshot <input type="file" name="screenshot" accept="image/*" required></label>
      <button class="btn" type="submit">Submit Deposit</button>
    </form>
    <p class="muted">Pending/Approved/Rejected status available below.</p>
  </div>
  <div class="card">
    <h2>Wallet Addresses</h2>
    <p>USDT (TRC20): <code><?= e($usdtTrc20Wallet) ?></code> <button class="btn btn-sm" data-copy="<?= e($usdtTrc20Wallet) ?>">Copy</button></p>
    <p>USDT (BEP20): <code><?= e($usdtBep20Wallet) ?></code> <button class="btn btn-sm" data-copy="<?= e($usdtBep20Wallet) ?>">Copy</button></p>
    <img alt="QR placeholder" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=WORKUPX" style="border-radius:12px;max-width:180px">
    <p class="muted">After transfer, upload screenshot and confirm payment via the WhatsApp support link below.</p>
    <p><a class="btn btn-gold" target="_blank" rel="noopener" href="<?= e(WHATSAPP_SUPPORT) ?>">Confirm on WhatsApp</a></p>
  </div>
</section>
<section class="card" style="margin-top:1rem">
  <h2>Deposit History</h2>
  <div class="table-wrap"><table class="table"><thead><tr><th>Time</th><th>Amount</th><th>Asset</th><th>Status</th></tr></thead><tbody><?php foreach ($deposits as $d): ?><tr><td><?= e($d['created_at']) ?></td><td><?= format_money((float)$d['amount']) ?></td><td><?= e($d['asset']) ?></td><td class="<?= e($d['status']) ?>"><?= e($d['status']) ?></td></tr><?php endforeach; ?></tbody></table></div>
</section>
<?php require_once __DIR__ . '/includes/user_nav.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
