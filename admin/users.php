<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if (is_post()) {
    verify_csrf();
    $action = (string) ($_POST['action'] ?? '');
    $userId = (int) ($_POST['user_id'] ?? 0);

    if ($userId > 0) {
        if ($action === 'ban') {
            db()->prepare('UPDATE users SET is_banned = 1 WHERE id = :id')->execute(['id' => $userId]);
            log_admin('ban_user', 'user_id=' . $userId);
        }
        if ($action === 'unban') {
            db()->prepare('UPDATE users SET is_banned = 0 WHERE id = :id')->execute(['id' => $userId]);
            log_admin('unban_user', 'user_id=' . $userId);
        }
        if ($action === 'balance') {
            $balance = (float) ($_POST['balance'] ?? 0);
            db()->prepare('UPDATE users SET balance = :balance WHERE id = :id')->execute(['balance' => $balance, 'id' => $userId]);
            log_admin('edit_balance', 'user_id=' . $userId . ',balance=' . $balance);
        }
        if ($action === 'add_earning') {
            $amount = (float) ($_POST['earning_amount'] ?? 0);
            $note = trim((string) ($_POST['earning_note'] ?? 'Manual admin credit'));
            if ($amount > 0) {
                db()->prepare('INSERT INTO earnings (user_id, source_type, amount, note, created_at) VALUES (:uid,"manual",:amount,:note,NOW())')->execute(['uid' => $userId, 'amount' => $amount, 'note' => $note]);
                db()->prepare('UPDATE users SET balance = balance + :amount, total_earnings = total_earnings + :amount WHERE id = :id')->execute(['amount' => $amount, 'id' => $userId]);
                db()->prepare('INSERT INTO notifications (user_id, title, body, type, created_at) VALUES (:uid,"Earnings Credited",:body,"system",NOW())')->execute(['uid' => $userId, 'body' => 'Admin credited $' . number_format($amount, 2) . ': ' . $note]);
                log_admin('add_earning', 'user_id=' . $userId . ',amount=' . $amount);
            }
        }
    }
    set_flash('success', 'User action completed.');
    redirect('/admin/users.php');
}

$q = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 20;

if ($q !== '') {
    $countStmt = db()->prepare('SELECT COUNT(*) c FROM users WHERE role="user" AND (full_name LIKE :q OR email LIKE :q)');
    $countStmt->execute(['q' => '%' . $q . '%']);
    $total = (int) ($countStmt->fetch()['c'] ?? 0);
} else {
    $total = (int) (db()->query('SELECT COUNT(*) c FROM users WHERE role="user"')->fetch()['c'] ?? 0);
}

$p = paginate($total, $page, $perPage);

if ($q !== '') {
    $stmt = db()->prepare('SELECT * FROM users WHERE role="user" AND (full_name LIKE :q OR email LIKE :q) ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
    $stmt->bindValue(':q', '%' . $q . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $p['perPage'], PDO::PARAM_INT);
    $stmt->bindValue(':offset', $p['offset'], PDO::PARAM_INT);
    $stmt->execute();
} else {
    $stmt = db()->prepare('SELECT * FROM users WHERE role="user" ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
    $stmt->bindValue(':limit', $p['perPage'], PDO::PARAM_INT);
    $stmt->bindValue(':offset', $p['offset'], PDO::PARAM_INT);
    $stmt->execute();
}
$users = $stmt->fetchAll();

$pageTitle = 'Manage Users';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
  <h1>Manage Users</h1>
  <p><a href="/admin/index.php">Back to Dashboard</a></p>
  <form method="get" action="/admin/users.php"><input name="q" value="<?= e($q) ?>" placeholder="Search by name/email"><button class="btn btn-sm" type="submit">Search</button></form>
  <div class="table-wrap"><table class="table"><thead><tr><th>User</th><th>Email</th><th>Balance</th><th>Status</th><th>Actions</th></tr></thead><tbody>
    <?php foreach ($users as $u): ?>
    <tr>
      <td><?= e($u['full_name']) ?></td><td><?= e($u['email']) ?></td><td><?= format_money((float) $u['balance']) ?></td><td><?= $u['is_banned'] ? 'Banned' : 'Active' ?></td>
      <td>
        <form method="post" action="/admin/users.php" style="display:flex;gap:.3rem;flex-wrap:wrap">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
          <input type="number" step="0.01" name="balance" placeholder="Set balance">
          <button class="btn btn-sm" name="action" value="balance">Set Bal</button>
          <input type="number" step="0.01" min="0.01" name="earning_amount" placeholder="Add earning">
          <input name="earning_note" placeholder="Earning note" maxlength="120">
          <button class="btn btn-sm btn-gold" name="action" value="add_earning">Credit</button>
          <?php if ((int) $u['is_banned'] === 0): ?><button class="btn btn-sm btn-outline" name="action" value="ban">Ban</button><?php else: ?><button class="btn btn-sm" name="action" value="unban">Unban</button><?php endif; ?>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody></table></div>
  <p style="display:flex;gap:.5rem;align-items:center">
    <?php if ($p['page'] > 1): ?><a class="btn btn-sm btn-outline" href="?q=<?= urlencode($q) ?>&page=<?= $p['page'] - 1 ?>">← Prev</a><?php endif; ?>
    Page <?= $p['page'] ?> / <?= $p['totalPages'] ?>
    <?php if ($p['page'] < $p['totalPages']): ?><a class="btn btn-sm btn-outline" href="?q=<?= urlencode($q) ?>&page=<?= $p['page'] + 1 ?>">Next →</a><?php endif; ?>
  </p>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
