<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$pageTitle = 'Quote';
require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h1>Live Quotes</h1>
  <input placeholder="Search coins..." data-filter-target=".coin-row">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Pair</th><th>Type</th></tr></thead>
      <tbody>
        <tr class="coin-row"><td>BTC/USDT</td><td>Educational market tracking</td></tr>
        <tr class="coin-row"><td>ETH/USDT</td><td>Educational market tracking</td></tr>
        <tr class="coin-row"><td>BNB/USDT</td><td>Educational market tracking</td></tr>
        <tr class="coin-row"><td>SOL/USDT</td><td>Educational market tracking</td></tr>
        <tr class="coin-row"><td>XRP/USDT</td><td>Educational market tracking</td></tr>
      </tbody>
    </table>
  </div>
</section>
<section class="card" style="margin-top:1rem">
<script src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>{"autosize":true,"symbol":"BINANCE:BTCUSDT","interval":"15","timezone":"Etc/UTC","theme":"dark","style":"1","locale":"en","allow_symbol_change":true,"support_host":"https://www.tradingview.com"}</script>
<div style="height:480px"></div>
</section>
<?php require_once __DIR__ . '/includes/user_nav.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
