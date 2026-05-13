<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$rows = db()->query('SELECT e.*, u.full_name, u.email FROM earnings e INNER JOIN users u ON u.id = e.user_id ORDER BY e.created_at DESC LIMIT 200')->fetchAll();
$pageTitle = 'Earnings Log';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card"><h1>Earnings Log</h1><p><a href="/admin/index.php">Back</a></p>
<div class="table-wrap"><table class="table"><thead><tr><th>Time</th><th>User</th><th>Type</th><th>Amount</th><th>Note</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
<tr>
<td><?= e($r['created_at']) ?></td>
<td><?= e($r['full_name']) ?><br><small><?= e($r['email']) ?></small></td>
<td><?= e($r['source_type']) ?></td>
<td><?= format_money((float) $r['amount']) ?></td>
<td><?= e($r['note']) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div></section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
