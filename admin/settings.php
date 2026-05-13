<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$keys = ['estimated_profit_min','estimated_profit_max','estimated_loss_chance_percent','referral_commission_percent','usdt_wallet_address','usdc_wallet_address','site_notice'];

if (is_post()) {
    verify_csrf();

    $stmt = db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    foreach ($keys as $k) {
        $v = trim((string) ($_POST[$k] ?? ''));
        $stmt->execute(['k' => $k, 'v' => $v]);
    }

    log_admin('update_settings', implode(',', $keys));
    set_flash('success', 'Settings updated.');
    redirect('/admin/settings.php');
}

$settings = [];
$get = db()->prepare('SELECT setting_value FROM settings WHERE setting_key = :k LIMIT 1');
foreach ($keys as $k) {
    $get->execute(['k' => $k]);
    $settings[$k] = (string) ($get->fetch()['setting_value'] ?? '');
}

$pageTitle = 'Platform Settings';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card" style="max-width:820px;margin:0 auto">
  <h1>Platform Settings</h1>
  <p><a href="/admin/index.php">Back</a></p>
  <form method="post" action="/admin/settings.php">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Estimated Profit Min (%) <input name="estimated_profit_min" value="<?= e($settings['estimated_profit_min']) ?>"></label>
    <label>Estimated Profit Max (%) <input name="estimated_profit_max" value="<?= e($settings['estimated_profit_max']) ?>"></label>
    <label>Estimated Loss Chance (%) <input name="estimated_loss_chance_percent" value="<?= e($settings['estimated_loss_chance_percent']) ?>"></label>
    <label>Referral Commission (%) <input name="referral_commission_percent" value="<?= e($settings['referral_commission_percent']) ?>"></label>
    <label>USDT Wallet Address <input name="usdt_wallet_address" value="<?= e($settings['usdt_wallet_address']) ?>"></label>
    <label>USDC Wallet Address <input name="usdc_wallet_address" value="<?= e($settings['usdc_wallet_address']) ?>"></label>
    <label>Site Notice <textarea name="site_notice"><?= e($settings['site_notice']) ?></textarea></label>
    <button class="btn" type="submit">Save Settings</button>
  </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
