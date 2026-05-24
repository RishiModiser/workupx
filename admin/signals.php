<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if (is_post()) {
    verify_csrf();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'create') {
        $code = strtoupper(trim((string) ($_POST['signal_code'] ?? random_code(8))));
        $pair = strtoupper(trim((string) ($_POST['pair_name'] ?? 'BTC/USDT')));
        $category = trim((string) ($_POST['category'] ?? 'General'));
        $targetPackage = (string) ($_POST['target_package'] ?? 'all');
        $profitPercent = (float) ($_POST['profit_percent'] ?? 1);
        $expiresInMinutes = max(1, (int) ($_POST['expires_in_minutes'] ?? 60));
        $oneTimeUse = isset($_POST['one_time_use']) ? 1 : 0;
        $direction = strtoupper(trim((string) ($_POST['direction'] ?? 'LONG')));
        $entry = trim((string) ($_POST['entry_text'] ?? 'Market'));
        $description = trim((string) ($_POST['description'] ?? ''));
        $scheduled = trim((string) ($_POST['scheduled_at'] ?? ''));
        $scheduledVal = $scheduled !== '' ? $scheduled : null;

        db()->prepare('INSERT INTO trade_signals (signal_code,pair_name,category,target_package,profit_percent,expires_in_minutes,one_time_use,direction,entry_text,description,is_active,scheduled_at,created_by_admin_id,created_at,updated_at) VALUES (:code,:pair,:category,:target_package,:profit_percent,:expires_in_minutes,:one_time_use,:direction,:entry,:description,1,:scheduled,:admin,NOW(),NOW())')
            ->execute([
                'code' => $code,
                'pair' => $pair,
                'category' => $category,
                'target_package' => in_array($targetPackage, ['all', 'silver', 'gold', 'diamond'], true) ? $targetPackage : 'all',
                'profit_percent' => max(0.1, $profitPercent),
                'expires_in_minutes' => $expiresInMinutes,
                'one_time_use' => $oneTimeUse,
                'direction' => in_array($direction, ['LONG', 'SHORT'], true) ? $direction : 'LONG',
                'entry' => $entry,
                'description' => $description,
                'scheduled' => $scheduledVal,
                'admin' => (int) $_SESSION['user_id'],
            ]);
        log_admin('create_signal', $code);
    }

    if (in_array($action, ['toggle', 'delete'], true)) {
        $id = (int) ($_POST['signal_id'] ?? 0);
        if ($id > 0) {
            if ($action === 'toggle') {
                db()->prepare('UPDATE trade_signals SET is_active = 1 - is_active WHERE id = :id')->execute(['id' => $id]);
                log_admin('toggle_signal', 'id=' . $id);
            } else {
                db()->prepare('DELETE FROM trade_signals WHERE id = :id')->execute(['id' => $id]);
                log_admin('delete_signal', 'id=' . $id);
            }
        }
    }

    set_flash('success', 'Signal action completed.');
    redirect('/admin/signals.php');
}

$signals = db()->query('SELECT ts.*, u.full_name AS admin_name FROM trade_signals ts LEFT JOIN users u ON u.id = ts.created_by_admin_id ORDER BY ts.created_at DESC LIMIT 200')->fetchAll();
$pageTitle = 'Manage Signals';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="grid grid-2">
  <div class="card">
    <h1>Create Signal</h1>
    <form method="post" action="/admin/signals.php">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action" value="create">
      <label>Signal Code <input name="signal_code" value="<?= e(random_code(8)) ?>"></label>
      <label>Pair <input name="pair_name" value="BTC/USDT" required></label>
      <label>Category <input name="category" value="Scalping"></label>
      <label>Target Package
        <select name="target_package">
          <option value="all">All Plans</option>
          <option value="silver">Silver</option>
          <option value="gold">Gold</option>
          <option value="diamond">Diamond</option>
        </select>
      </label>
      <label>Profit Percent (%) <input type="number" name="profit_percent" min="0.1" step="0.01" value="1.00" required></label>
      <label>Expiry Window (minutes) <input type="number" name="expires_in_minutes" min="1" value="60" required></label>
      <label><input type="checkbox" name="one_time_use" checked> One-time use per user</label>
      <label>Direction <select name="direction"><option>LONG</option><option>SHORT</option></select></label>
      <label>Entry <input name="entry_text" value="Market"></label>
      <label>Description <textarea name="description"></textarea></label>
      <label>Schedule (optional) <input type="datetime-local" name="scheduled_at"></label>
      <button class="btn" type="submit">Publish Signal</button>
    </form>
  </div>
  <div class="card">
    <h2>Signals</h2>
    <div class="table-wrap"><table class="table"><thead><tr><th>Code</th><th>Pair</th><th>Plan</th><th>Profit</th><th>Expiry</th><th>Status</th><th>Action</th></tr></thead><tbody>
      <?php foreach ($signals as $s): ?>
      <tr>
        <td><?= e($s['signal_code']) ?></td><td><?= e($s['pair_name']) ?></td><td><?= e(ucfirst((string) $s['target_package'])) ?></td><td><?= e((string) $s['profit_percent']) ?>%</td><td><?= (int) $s['expires_in_minutes'] ?>m</td><td><?= (int)$s['is_active'] ? 'Active' : 'Inactive' ?></td>
        <td><form method="post" action="/admin/signals.php" style="display:inline-flex;gap:.3rem"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="signal_id" value="<?= (int)$s['id'] ?>"><button class="btn btn-sm" name="action" value="toggle">Toggle</button><button class="btn btn-sm btn-outline" name="action" value="delete">Delete</button></form></td>
      </tr>
      <?php endforeach; ?>
    </tbody></table></div>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
