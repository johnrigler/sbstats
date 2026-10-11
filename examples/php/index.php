<?php
declare(strict_types=1);
// Minimal PHP-as-enriched-HTML example using SB Stats' ISO-week JSON dataset.
// No interpreter extensions or framework required beyond the PHP CLI server.
$week = $_GET['week'] ?? '2026-W41';
$token = $_GET['token'] ?? 'get-money';
if (!is_string($week) || !preg_match('/^20[0-9]{2}-W(?:0[1-9]|[1-4][0-9]|5[0-3])$/', $week)) {
    http_response_code(400); exit('Invalid ISO week');
}
if (!in_array($token, ['get-money', 'purple-squirrel'], true)) {
    http_response_code(400); exit('Invalid token');
}
[$year, $number] = explode('-W', $week);
$file = __DIR__ . '/../solana/' . $year . '/W' . $number . '/week.json';
if (!is_file($file)) { http_response_code(404); exit('Week not found in the sample'); }
$data = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
$subject = $data['subjects'][$token];
$rows = $subject['points'];
$total = $subject['total'];
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
$chart = '../solana/' . $year . '/W' . $number . '/' . $token . '.png';
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>SB Stats / PHP example</title>
<style>body{font:16px system-ui;margin:2rem auto;padding:0 1rem;max-width:860px;color:#223246} table{border-collapse:collapse;width:100%}td,th{text-align:left;padding:.6rem;border-bottom:1px solid #dbe1e8}img{width:100%;height:auto}a{color:#15609c}form{display:flex;gap:1rem;align-items:center;flex-wrap:wrap}</style></head>
<body>
<h1>SB Stats / PHP + HTML</h1>
<p>This is a PHP document reading the same <a href="../solana/">ISO-week sample</a> as the static SB Shell-style viewer.</p>
<form method="get"><label>Week <input name="week" value="<?= h($week) ?>" pattern="20[0-9]{2}-W[0-9]{2}"></label>
<label>Token <select name="token">
<option value="get-money" <?= $token==='get-money' ? 'selected' : '' ?>>Get Money</option>
<option value="purple-squirrel" <?= $token==='purple-squirrel' ? 'selected' : '' ?>>Purple Squirrel</option>
</select></label><button type="submit">Show</button></form>
<h2><?= h($token) ?> / <?= h($week) ?></h2>
<p>Week of <?= h($data['start']) ?> through <?= h($data['end']) ?> (UTC). <strong><?= $total ?> sampled signatures.</strong></p>
<img src="<?= h($chart) ?>" alt="SB Stats sampled signature graph" onerror="this.style.display='none'">
<table><thead><tr><th>UTC date</th><th>Sampled signatures</th></tr></thead><tbody>
<?php foreach ($rows as $row): ?>
<tr><td><?= h($row['date']) ?></td><td><?= (int)$row['count'] ?></td></tr>
<?php endforeach; ?>
</tbody></table>
<p><small>Counts are partial top-account signatures, not verified transfers of the target token.</small></p>
<p><a href="../../2013/demo/index.php">Historical 2013 PHP entry point</a> (available only with --legacy; may require modernization).</p>
</body></html>
