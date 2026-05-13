<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if (is_post()) {
    verify_csrf();
    $id = (int) ($_POST['deposit_id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    $note = trim((string) ($_POST['admin_note'] ?? ''));

    $stmt = db()->prepare('SELECT * FROM deposits WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $d = $stmt->fetch();

    if ($d && $d['status'] === 'pending') {
        if ($action === 'approve') {
            db()->prepare('UPDATE deposits SET status="approved", admin_note = :note WHERE id = :id')->execute(['note' => $note, 'id' => $id]);
            db()->prepare('UPDATE users SET balance = balance + :amount WHERE id = :uid')->execute(['amount' => $d['amount'], 'uid' => $d['user_id']]);
            db()->prepare('INSERT INTO notifications (user_id, title, body, type, created_at) VALUES (:uid,"Deposit Approved",:body,"deposit",NOW())')->execute(['uid' => $d['user_id'], 'body' => 'Your deposit has been approved.']);
            log_admin('approve_deposit', 'deposit_id=' . $id);
        }
        if ($action === 'reject') {
            db()->prepare('UPDATE deposits SET status="rejected", admin_note = :note WHERE id = :id')->execute(['note' => $note, 'id' => $id]);
            db()->prepare('INSERT INTO notifications (user_id, title, body, type, created_at) VALUES (:uid,"Deposit Rejected",:body,"deposit",NOW())')->execute(['uid' => $d['user_id'], 'body' => 'Your deposit was rejected. Please contact support.']);
            log_admin('reject_deposit', 'deposit_id=' . $id);
        }
        set_flash('success', 'Deposit updated.');
    }
    redirect('/admin/deposits.php');
}

$rows = db()->query('SELECT d.*, u.full_name, u.email FROM deposits d INNER JOIN users u ON u.id = d.user_id ORDER BY d.created_at DESC LIMIT 200')->fetchAll();
$pageTitle = 'Manage Deposits';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card"><h1>Manage Deposits</h1><p><a href="/admin/index.php">Back</a></p>
<div class="table-wrap"><table class="table"><thead><tr><th>User</th><th>Amount</th><th>Asset</th><th>Status</th><th>Proof</th><th>Action</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
<tr>
<td><?= e($r['full_name']) ?><br><small><?= e($r['email']) ?></small></td>
<td><?= format_money((float)$r['amount']) ?></td>
<td><?= e($r['asset']) ?></td>
<td class="<?= e($r['status']) ?>"><?= e($r['status']) ?></td>
<td><?php if (!empty($r['screenshot_path'])): ?><a target="_blank" href="/uploads/<?= e($r['screenshot_path']) ?>">View</a><?php endif; ?></td>
<td>
<?php if ($r['status'] === 'pending'): ?>
<form method="post" action="/admin/deposits.php" style="display:grid;gap:.3rem">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="deposit_id" value="<?= (int)$r['id'] ?>"><input name="admin_note" placeholder="Admin note"><div><button class="btn btn-sm" name="action" value="approve">Approve</button> <button class="btn btn-sm btn-outline" name="action" value="reject">Reject</button></div>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody></table></div></section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
