<?php
$pageTitle = 'Premium Crypto Community Platform';
$metaDescription = 'WORKUPX offers community trade ideas, educational signals, transparent estimated returns, and referral rewards.';
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="card glass">
    <h1>WORKUPX Community Signals</h1>
    <p>Transparent educational copy trade signals with admin-controlled estimated outcomes. No guaranteed profits. No real brokerage execution.</p>
    <div style="display:flex;gap:.6rem;flex-wrap:wrap">
      <a class="btn" href="/register.php">Start with WORKUPX</a>
      <a class="btn btn-outline" href="/community.php">View Trade Ideas</a>
    </div>
    <p class="muted" style="margin-top:.8rem">Estimated Profit • Educational Trade Signals • Community Trade Ideas</p>
  </div>
  <div class="card glass">
    <script src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>{"symbols":[{"description":"BTC/USDT","proName":"BINANCE:BTCUSDT"},{"description":"ETH/USDT","proName":"BINANCE:ETHUSDT"},{"description":"BNB/USDT","proName":"BINANCE:BNBUSDT"},{"description":"SOL/USDT","proName":"BINANCE:SOLUSDT"},{"description":"XRP/USDT","proName":"BINANCE:XRPUSDT"}],"showSymbolLogo":true,"isTransparent":true,"displayMode":"adaptive","colorTheme":"dark","locale":"en"}</script>
    <div style="height:8px"></div>
    <div class="skeleton"></div><div style="height:8px"></div><div class="skeleton"></div>
  </div>
</section>

<h2 class="section-title">Platform Features</h2>
<section class="grid grid-3">
  <article class="card"><h3>Quote</h3><p class="muted">Live TradingView market widgets with major crypto pairs and responsive chart layout.</p></article>
  <article class="card"><h3>Trade</h3><p class="muted">Apply admin-issued educational signal codes and view estimated educational results with history.</p></article>
  <article class="card"><h3>Assets</h3><p class="muted">Track balances, deposits, withdrawals, earnings, and trade history in one mobile-first dashboard.</p></article>
  <article class="card"><h3>Referral Rewards</h3><p class="muted">Invite community members and unlock reward milestones transparently.</p></article>
  <article class="card"><h3>Manual Deposits</h3><p class="muted">Upload payment proof, confirm on WhatsApp, and wait for admin verification.</p></article>
  <article class="card"><h3>Secure Auth</h3><p class="muted">Protected sessions, password hashing, CSRF tokens, and anti brute-force controls.</p></article>
</section>

<h2 class="section-title">Investment Packages</h2>
<section class="grid grid-3">
  <article class="card"><h3>$50 Starter</h3><p class="muted">Community Signal Access • Potential Rewards • Referral bonuses</p></article>
  <article class="card"><h3>$100 Advanced</h3><p class="muted">Extended Educational Signals • Potential Rewards • Faster support</p></article>
  <article class="card"><h3>$200 Premium</h3><p class="muted">Priority Community Access • Estimated Returns • Full referral perks</p></article>
</section>

<h2 class="section-title">Referral Rewards</h2>
<section class="grid grid-3">
  <article class="card"><h3>20 Referrals</h3><p class="muted">Bike Reward</p></article>
  <article class="card"><h3>40 Referrals</h3><p class="muted">Europe Trip Reward</p></article>
  <article class="card"><h3>80 Referrals</h3><p class="muted">Car Reward</p></article>
</section>

<h2 class="section-title">FAQ</h2>
<section class="faq">
  <details><summary>Is WORKUPX a brokerage?</summary><p>No. WORKUPX is an educational platform sharing community trade ideas and estimated outcomes only.</p></details>
  <details><summary>Are profits guaranteed?</summary><p>No guaranteed profits are promised. All values are estimated and configurable by admin.</p></details>
  <details><summary>How are deposits handled?</summary><p>Deposits are manually verified by admin after proof upload and WhatsApp confirmation.</p></details>
</section>

<h2 class="section-title">Testimonials</h2>
<section class="grid grid-3">
  <article class="card"><p>“Clear signals and transparent wording. Great community support.”</p><small class="muted">- Priya</small></article>
  <article class="card"><p>“I like that they clearly state this is educational and estimated.”</p><small class="muted">- Arjun</small></article>
  <article class="card"><p>“Referral dashboard and milestones are very motivating.”</p><small class="muted">- Sofia</small></article>
</section>

<footer class="card" style="margin-top:1rem">
  <a href="/privacy.php">Privacy Policy</a> • <a href="/terms.php">Terms</a> • <a href="/risk.php">Risk Disclaimer</a> • <a href="/aml.php">AML Policy</a> • <a href="/contact.php">Contact</a>
</footer>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
