<?php
$pageTitle = 'Professional Investment Platform';
$metaDescription = 'WORKUPX provides secure account management, package-based dashboards, referrals, and copy-trade simulation workflows.';
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="card glass reveal fade-up">
    <span class="badge">Trusted by global fintech learners</span>
    <h1>Premium Crypto Growth Dashboard Experience</h1>
    <p>Institutional-style portfolio insights, controlled copy-trade simulation, referral acceleration and transparent package outcomes in one modern workspace.</p>
    <div class="hero-actions">
      <a class="btn" href="/register.php">Create Account</a>
      <a class="btn btn-outline" href="/trade.php">Open Copy Trade</a>
    </div>
    <div class="hero-stats">
      <div class="stat-card">
        <div class="muted">Active Learners</div>
        <div class="kpi counter" data-count="12480">0</div>
      </div>
      <div class="stat-card">
        <div class="muted">Simulated Daily Volume</div>
        <div class="kpi">$<span class="counter" data-count="3.6">0</span>M</div>
      </div>
      <div class="stat-card">
        <div class="muted">Average Uptime</div>
        <div class="kpi">99.<span class="counter" data-count="94">0</span>%</div>
      </div>
    </div>
  </div>
  <div class="card glass trading-panel reveal slide-up">
    <h2>Live Market Pulse</h2>
    <div class="market-tabs" role="tablist" aria-label="Chart timeframe">
      <button type="button" class="active" data-timeframe>1H</button>
      <button type="button" data-timeframe>4H</button>
      <button type="button" data-timeframe>1D</button>
      <button type="button" data-timeframe>1W</button>
    </div>
    <script src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>{"symbols":[{"description":"BTC/USDT","proName":"BINANCE:BTCUSDT"},{"description":"ETH/USDT","proName":"BINANCE:ETHUSDT"},{"description":"BNB/USDT","proName":"BINANCE:BNBUSDT"},{"description":"SOL/USDT","proName":"BINANCE:SOLUSDT"},{"description":"XRP/USDT","proName":"BINANCE:XRPUSDT"}],"showSymbolLogo":true,"isTransparent":true,"displayMode":"adaptive","colorTheme":"dark","locale":"en"}</script>
    <div class="mini-market-grid">
      <div class="mini-market-card"><span>BTC</span><strong class="accent">Live Widget</strong></div>
      <div class="mini-market-card"><span>ETH</span><strong class="accent">Live Widget</strong></div>
      <div class="mini-market-card"><span>SOL</span><strong class="accent">Live Widget</strong></div>
    </div>
    <div class="skeleton"></div>
  </div>
</section>

<h2 class="section-title reveal fade-up">Core Platform Modules</h2>
<section class="grid grid-3">
  <article class="card reveal fade-up"><h3>Customer Dashboard</h3><p class="muted">Balance, earnings, package progress, referral boost, salary status and transaction history.</p></article>
  <article class="card reveal fade-up"><h3>Admin Control Center</h3><p class="muted">User management, deposits, withdrawals, signals, announcements and settings.</p></article>
  <article class="card reveal fade-up"><h3>Referral Growth</h3><p class="muted">Dynamic earning boost (+0.5% per referral) with milestone-based salary qualification.</p></article>
  <article class="card reveal fade-up"><h3>Copy Trading Workflow</h3><p class="muted">Plan-based signal code access, expiry timers and controlled educational outcomes.</p></article>
  <article class="card reveal fade-up"><h3>Manual Payment Flow</h3><p class="muted">USDT TRC20/BEP20 support with proof uploads and verified admin approval lifecycle.</p></article>
  <article class="card reveal fade-up"><h3>Security Controls</h3><p class="muted">CSRF validation, password hashing, route guards and prepared statements.</p></article>
</section>

<h2 class="section-title reveal fade-up">Investment Plans</h2>
<section class="grid grid-3 pricing-grid">
  <article class="card pricing-card reveal slide-up">
    <h3>Silver</h3>
    <p class="kpi">$125</p>
    <p class="muted">Welcome Bonus: $12</p>
    <a class="btn btn-outline" href="/register.php">Get Silver</a>
  </article>
  <article class="card pricing-card popular reveal slide-up">
    <h3>Gold</h3>
    <p class="kpi">$250</p>
    <p class="muted">Welcome Bonus: $25</p>
    <a class="btn" href="/register.php">Get Gold</a>
  </article>
  <article class="card pricing-card reveal slide-up">
    <h3>Diamond</h3>
    <p class="kpi">$500</p>
    <p class="muted">Welcome Bonus: $50</p>
    <a class="btn btn-outline" href="/register.php">Get Diamond</a>
  </article>
</section>

<h2 class="section-title reveal fade-up">Reward Milestones</h2>
<section class="grid grid-3 rewards-grid">
  <article class="card reward-card reveal fade-up">
    <h3>Starter Stage</h3>
    <p class="muted">Build referral network foundation</p>
    <div class="progress" data-progress="45"><span></span></div>
  </article>
  <article class="card reward-card reveal fade-up">
    <h3>Growth Stage</h3>
    <p class="muted">Unlock higher monthly salary tiers</p>
    <div class="progress" data-progress="68"><span></span></div>
  </article>
  <article class="card reward-card reveal fade-up">
    <h3>Elite Stage</h3>
    <p class="muted">Top-tier referral achievement rewards</p>
    <div class="progress" data-progress="82"><span></span></div>
  </article>
</section>

<h2 class="section-title reveal fade-up">FAQ</h2>
<section class="faq reveal fade-up">
  <details><summary>How are deposits approved?</summary><p>User submits request, receives wallet address, uploads proof, and admin manually approves after verification.</p></details>
  <details><summary>How are withdrawals handled?</summary><p>Users submit wallet/network, 20% fee is applied, and requests move through manual admin approval.</p></details>
  <details><summary>Can copy-trade codes expire?</summary><p>Yes. Admin controls code expiry and one-time usage restrictions by package.</p></details>
</section>

<footer class="card reveal fade-up" style="margin-top:1rem">
  <a href="/privacy.php">Privacy Policy</a> • <a href="/terms.php">Terms</a> • <a href="/risk.php">Risk Disclosure</a> • <a href="/aml.php">AML/KYC Policy</a> • <a href="/contact.php">Contact</a>
</footer>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
