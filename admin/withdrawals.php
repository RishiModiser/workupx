<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if (is_post()) {
    verify_csrf();
    $id = (int) ($_POST['withdrawal_id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    $note = trim((string) ($_POST['admin_note'] ?? ''));

    $stmt = db()->prepare('SELECT * FROM withdrawals WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $w = $stmt->fetch();

    if ($w && $w['status'] === 'pending') {
        if ($action === 'approve') {
            $available = calculate_available_withdrawal_balance((int) $w['user_id'], $id);

            if ((float) $w['amount'] > $available) {
                set_flash('error', 'Cannot approve: user available balance is lower than this request.');
                redirect('/admin/withdrawals.php');
            }

            db()->prepare('UPDATE withdrawals SET status="approved", admin_note = :note WHERE id = :id')->execute(['note' => $note, 'id' => $id]);
            db()->prepare('UPDATE users SET balance = GREATEST(0, balance - :amount) WHERE id = :uid')->execute(['amount' => $w['amount'], 'uid' => $w['user_id']]);
            db()->prepare('INSERT INTO notifications (user_id, title, body, type, created_at) VALUES (:uid,"Withdrawal Approved",:body,"withdrawal",NOW())')->execute(['uid' => $w['user_id'], 'body' => 'Your withdrawal has been approved.']);
            log_admin('approve_withdrawal', 'withdrawal_id=' . $id);
        }
        if ($action === 'reject') {
            db()->prepare('UPDATE withdrawals SET status="rejected", admin_note = :note WHERE id = :id')->execute(['note' => $note, 'id' => $id]);
            db()->prepare('INSERT INTO notifications (user_id, title, body, type, created_at) VALUES (:uid,"Withdrawal Rejected",:body,"withdrawal",NOW())')->execute(['uid' => $w['user_id'], 'body' => 'Your withdrawal request was rejected.']);
            log_admin('reject_withdrawal', 'withdrawal_id=' . $id);
        }
    }

    set_flash('success', 'Withdrawal updated.');
    redirect('/admin/withdrawals.php');
}

$rows = db()->query('SELECT w.*, u.full_name, u.email FROM withdrawals w INNER JOIN users u ON u.id = w.user_id ORDER BY w.created_at DESC LIMIT 200')->fetchAll();
$pageTitle = 'Manage Withdrawals';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card"><h1>Manage Withdrawals</h1><p><a href="/admin/index.php">Back</a></p>
<div class="table-wrap"><table class="table"><thead><tr><th>User</th><th>Gross</th><th>Fee</th><th>Net</th><th>Wallet</th><th>Network</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
<tr>
<td><?= e($r['full_name']) ?><br><small><?= e($r['email']) ?></small></td>
<td><?= format_money((float)$r['amount']) ?></td>
<td><?= format_money((float)($r['fee_amount'] ?? 0)) ?></td>
<td><?= format_money((float)($r['net_amount'] ?? $r['amount'])) ?></td>
<td><?= e($r['wallet_address']) ?></td>
<td><?= e($r['network']) ?></td>
<td class="<?= e($r['status']) ?>"><?= e($r['status']) ?></td>
<td>
<?php if ($r['status'] === 'pending'): ?>
<form method="post" action="/admin/withdrawals.php" style="display:grid;gap:.3rem">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="withdrawal_id" value="<?= (int)$r['id'] ?>"><input name="admin_note" placeholder="Admin note"><div><button class="btn btn-sm" name="action" value="approve">Approve</button> <button class="btn btn-sm btn-outline" name="action" value="reject">Reject</button></div>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody></table></div></section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
