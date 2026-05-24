<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
bootstrap_remember_me();
$flashMessages = get_flash();
$pageTitle = $pageTitle ?? APP_NAME;
$metaDescription = $metaDescription ?? 'WORKUPX community investment education platform with transparent estimated outcomes.';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <meta name="theme-color" content="#05070d">
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
<div class="bg-grid" aria-hidden="true"></div>
<header class="topbar glass">
    <div class="topbar-main">
        <a href="/" class="brand">WORKUPX<span>.COM</span></a>
        <button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" data-nav-toggle>
            <span></span><span></span><span></span>
        </button>
        <nav class="topbar-nav" data-nav-menu>
            <a href="/community.php">Community</a>
            <a href="/quote.php">Quote</a>
            <a href="/trade.php">Copy Trade</a>
            <a href="/assets.php">Dashboard</a>
            <a href="/referral.php">Referral</a>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <a href="/profile.php">Profile</a>
                <a href="/logout.php">Logout</a>
            <?php else: ?>
                <a href="/login.php">Login</a>
                <a href="/register.php" class="btn btn-sm">Join</a>
            <?php endif; ?>
        </nav>
    </div>
    <div class="ticker" aria-hidden="true">
        <span>BTC +2.1%</span>
        <span>ETH +1.6%</span>
        <span>SOL +3.9%</span>
        <span>BNB +1.1%</span>
        <span>XRP +0.8%</span>
    </div>
</header>
<main class="container">
<?php foreach ($flashMessages as $flash): ?>
    <div class="toast toast-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endforeach; ?>
