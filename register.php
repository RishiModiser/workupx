<?php
require_once __DIR__ . '/includes/functions.php';

$prefilledReferralCode = strtoupper(trim((string) ($_GET['ref'] ?? '')));

if (is_post()) {
    verify_csrf();

    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');
    $referralCode = strtoupper(trim((string) ($_POST['referral_code'] ?? $prefilledReferralCode)));
    $package = (string) ($_POST['package_name'] ?? 'silver');

    if ($fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '') {
        set_flash('error', 'Please fill all required fields correctly.');
        redirect('/register.php');
    }

    if ($password !== $confirmPassword || strlen($password) < 8) {
        set_flash('error', 'Password must match and be at least 8 characters.');
        redirect('/register.php');
    }

    if (!in_array($package, ['silver', 'gold', 'diamond'], true)) {
        $package = 'silver';
    }

    $existsStmt = db()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $existsStmt->execute(['email' => $email]);
    if ($existsStmt->fetch()) {
        set_flash('error', 'Email already registered.');
        redirect('/register.php');
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $referredBy = null;
        if ($referralCode !== '') {
            $r = $pdo->prepare('SELECT id FROM users WHERE referral_code = :code LIMIT 1');
            $r->execute(['code' => $referralCode]);
            $ref = $r->fetch();
            if ($ref) {
                $referredBy = (int) $ref['id'];
            }
        }

        $myCode = '';
        for ($i = 0; $i < MAX_REFERRAL_CODE_ATTEMPTS; $i++) {
            $candidate = random_code(10);
            $codeCheck = $pdo->prepare('SELECT id FROM users WHERE referral_code = :code LIMIT 1');
            $codeCheck->execute(['code' => $candidate]);
            if (!$codeCheck->fetch()) {
                $myCode = $candidate;
                break;
            }
        }
        if ($myCode === '') {
            throw new RuntimeException('Unable to generate unique referral code.');
        }
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $packageInfo = package_config($package);
        $welcomeBonus = (float) $packageInfo['welcome_bonus'];

        $stmt = $pdo->prepare('INSERT INTO users (full_name, email, phone, password_hash, referral_code, referred_by_user_id, package_name, balance, total_earnings, created_at, updated_at) VALUES (:full_name,:email,:phone,:password_hash,:referral_code,:referred_by,:package,:balance,:total_earnings,NOW(),NOW())');
        $stmt->execute([
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'password_hash' => $passwordHash,
            'referral_code' => $myCode,
            'referred_by' => $referredBy,
            'package' => $package,
            'balance' => $welcomeBonus,
            'total_earnings' => $welcomeBonus,
        ]);

        $newUserId = (int) $pdo->lastInsertId();

        if ($referredBy) {
            $refStmt = $pdo->prepare('INSERT INTO referrals (referrer_user_id, referred_user_id, commission_amount, status, created_at) VALUES (:referrer,:referred,0,"pending",NOW())');
            $refStmt->execute(['referrer' => $referredBy, 'referred' => $newUserId]);
        }

        $pdo->prepare('INSERT INTO earnings (user_id, source_type, source_id, amount, note, created_at) VALUES (:uid,"manual",NULL,:amount,:note,NOW())')
            ->execute([
                'uid' => $newUserId,
                'amount' => $welcomeBonus,
                'note' => package_label($package) . ' welcome bonus credited on registration.',
            ]);

        $pdo->commit();
        set_flash('success', 'Registration successful. Welcome bonus credited. Please log in.');
        redirect('/login.php');
    } catch (Throwable $e) {
        $pdo->rollBack();
        error_log('Registration failed: ' . $e->getMessage());
        set_flash('error', 'Registration failed, please try again.');
        redirect('/register.php');
    }
}

$pageTitle = 'Register';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width:640px;margin:0 auto">
  <h1>Create your WORKUPX account</h1>
  <form method="post" action="/register.php">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Full Name <input name="full_name" maxlength="120" required></label>
    <label>Email <input type="email" name="email" maxlength="190" required></label>
    <label>Phone Number <input name="phone" maxlength="30" required></label>
    <label>Password <input type="password" name="password" minlength="8" required></label>
    <label>Confirm Password <input type="password" name="confirm_password" minlength="8" required></label>
    <label>Referral Code (optional) <input name="referral_code" maxlength="20" value="<?= e($prefilledReferralCode) ?>"></label>
    <label>Investment Package
      <select name="package_name" required>
        <option value="silver">Silver • Deposit $125 • Welcome Bonus $12</option>
        <option value="gold">Gold • Deposit $250 • Welcome Bonus $25</option>
        <option value="diamond">Diamond • Deposit $500 • Welcome Bonus $50</option>
      </select>
    </label>
    <button class="btn" type="submit">Create Account</button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
