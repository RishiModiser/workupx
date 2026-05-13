<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$pageTitle = 'Quote';
require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h1>Live Market Quotes</h1>
  <script src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>{"symbols":[{"description":"BTC/USDT","proName":"BINANCE:BTCUSDT"},{"description":"ETH/USDT","proName":"BINANCE:ETHUSDT"},{"description":"BNB/USDT","proName":"BINANCE:BNBUSDT"},{"description":"SOL/USDT","proName":"BINANCE:SOLUSDT"},{"description":"XRP/USDT","proName":"BINANCE:XRPUSDT"},{"description":"DOGE/USDT","proName":"BINANCE:DOGEUSDT"},{"description":"ADA/USDT","proName":"BINANCE:ADAUSDT"},{"description":"TRX/USDT","proName":"BINANCE:TRXUSDT"}],"showSymbolLogo":true,"isTransparent":true,"displayMode":"adaptive","colorTheme":"dark","locale":"en"}</script>
  <div class="skeleton" style="height:46px;margin-top:.5rem"></div>
  <input placeholder="Search pairs..." data-filter-target=".coin-row" style="margin-top:.8rem">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Pair</th><th>Type</th></tr></thead>
      <tbody>
        <tr class="coin-row"><td>BTC/USDT</td><td>Educational market tracking</td></tr>
        <tr class="coin-row"><td>ETH/USDT</td><td>Educational market tracking</td></tr>
        <tr class="coin-row"><td>BNB/USDT</td><td>Educational market tracking</td></tr>
        <tr class="coin-row"><td>SOL/USDT</td><td>Educational market tracking</td></tr>
        <tr class="coin-row"><td>XRP/USDT</td><td>Educational market tracking</td></tr>
        <tr class="coin-row"><td>DOGE/USDT</td><td>Educational market tracking</td></tr>
        <tr class="coin-row"><td>ADA/USDT</td><td>Educational market tracking</td></tr>
        <tr class="coin-row"><td>TRX/USDT</td><td>Educational market tracking</td></tr>
        <tr class="coin-row"><td>MATIC/USDT</td><td>Educational market tracking</td></tr>
        <tr class="coin-row"><td>DOT/USDT</td><td>Educational market tracking</td></tr>
      </tbody>
    </table>
  </div>
</section>
<section class="card" style="margin-top:1rem">
  <h2>Advanced Chart</h2>
  <div style="height:500px">
    <script src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>{"autosize":true,"symbol":"BINANCE:BTCUSDT","interval":"15","timezone":"Etc/UTC","theme":"dark","style":"1","locale":"en","allow_symbol_change":true,"support_host":"https://www.tradingview.com"}</script>
  </div>
</section>
<section class="card" style="margin-top:1rem">
  <h2>Market Overview</h2>
  <div style="height:400px">
    <script src="https://s3.tradingview.com/external-embedding/embed-widget-market-overview.js" async>{"colorTheme":"dark","dateRange":"12M","showChart":false,"locale":"en","width":"100%","height":"400","largeChartUrl":"","isTransparent":true,"showSymbolLogo":true,"showFloatingTooltip":false,"plotLineColorGrowing":"rgba(41, 98, 255, 1)","plotLineColorFalling":"rgba(41, 98, 255, 1)","gridLineColor":"rgba(255, 255, 255, 0.06)","scaleFontColor":"rgba(150, 162, 201, 1)","belowLineFillColorGrowing":"rgba(41, 98, 255, 0.12)","belowLineFillColorFalling":"rgba(41, 98, 255, 0.02)","belowLineFillColorGrowingBottom":"rgba(41, 98, 255, 0)","belowLineFillColorFallingBottom":"rgba(41, 98, 255, 0)","symbolActiveColor":"rgba(41, 98, 255, 0.12)","tabs":[{"title":"Crypto","symbols":[{"s":"BINANCE:BTCUSDT","d":"Bitcoin"},{"s":"BINANCE:ETHUSDT","d":"Ethereum"},{"s":"BINANCE:BNBUSDT","d":"BNB"},{"s":"BINANCE:SOLUSDT","d":"Solana"},{"s":"BINANCE:XRPUSDT","d":"XRP"},{"s":"BINANCE:DOGEUSDT","d":"Dogecoin"}],"originalTitle":"Crypto"}]}</script>
  </div>
</section>
<?php require_once __DIR__ . '/includes/user_nav.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
