<?php
$pageTitle = 'Professional Investment Platform';
$metaDescription = 'WORKUPX provides secure account management, package-based dashboards, referrals, and copy-trade simulation workflows.';
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="card glass">
    <h1>Modern Investment Platform Experience</h1>
    <p>Professional user dashboards, transparent referral tracking, and controlled copy-trade simulation workflows with robust admin oversight.</p>
    <div style="display:flex;gap:.6rem;flex-wrap:wrap">
      <a class="btn" href="/register.php">Create Account</a>
      <a class="btn btn-outline" href="/trade.php">Open Copy Trade</a>
    </div>
    <p class="muted" style="margin-top:.8rem">Silver • Gold • Diamond • Admin Managed • Security Focused</p>
  </div>
  <div class="card glass">
    <script src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>{"symbols":[{"description":"BTC/USDT","proName":"BINANCE:BTCUSDT"},{"description":"ETH/USDT","proName":"BINANCE:ETHUSDT"},{"description":"BNB/USDT","proName":"BINANCE:BNBUSDT"},{"description":"SOL/USDT","proName":"BINANCE:SOLUSDT"},{"description":"XRP/USDT","proName":"BINANCE:XRPUSDT"}],"showSymbolLogo":true,"isTransparent":true,"displayMode":"adaptive","colorTheme":"dark","locale":"en"}</script>
    <div style="height:8px"></div>
    <div class="skeleton"></div><div style="height:8px"></div><div class="skeleton"></div>
  </div>
</section>

<h2 class="section-title">Core Platform Modules</h2>
<section class="grid grid-3">
  <article class="card"><h3>Customer Dashboard</h3><p class="muted">Balance, earnings, package progress, referral boost, salary status, and transaction history.</p></article>
  <article class="card"><h3>Admin Control Center</h3><p class="muted">User management, deposits, withdrawals, copy-trade codes, announcements, and platform settings.</p></article>
  <article class="card"><h3>Referral System</h3><p class="muted">Dynamic earning boost (+0.5% per referral) with salary qualification milestones by plan.</p></article>
  <article class="card"><h3>Copy Trading Workflow</h3><p class="muted">Plan-targeted code delivery with expiry timers, one-time usage checks, and controlled percentage outcomes.</p></article>
  <article class="card"><h3>Manual Payment Flow</h3><p class="muted">USDT TRC20/BEP20 wallet support with upload proof and admin approval lifecycle.</p></article>
  <article class="card"><h3>Security & Compliance</h3><p class="muted">Password hashing, CSRF protection, prepared statements, route guards, and audit logging.</p></article>
</section>

<h2 class="section-title">Investment Plans</h2>
<section class="grid grid-3">
  <article class="card"><h3>Silver Package</h3><p class="muted">Deposit $125 • Welcome Bonus $12</p></article>
  <article class="card"><h3>Gold Package</h3><p class="muted">Deposit $250 • Welcome Bonus $25</p></article>
  <article class="card"><h3>Diamond Package</h3><p class="muted">Deposit $500 • Welcome Bonus $50</p></article>
</section>

<h2 class="section-title">FAQ</h2>
<section class="faq">
  <details><summary>How are deposits approved?</summary><p>User submits request, receives wallet address, uploads proof, and admin manually approves after verification.</p></details>
  <details><summary>How are withdrawals handled?</summary><p>Users submit wallet/network, 20% fee is applied, and requests move through manual admin approval.</p></details>
  <details><summary>Can copy-trade codes expire?</summary><p>Yes. Admin controls code expiry and one-time usage restrictions by package.</p></details>
</section>

<footer class="card" style="margin-top:1rem">
  <a href="/privacy.php">Privacy Policy</a> • <a href="/terms.php">Terms</a> • <a href="/risk.php">Risk Disclosure</a> • <a href="/aml.php">AML/KYC Policy</a> • <a href="/contact.php">Contact</a>
</footer>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
