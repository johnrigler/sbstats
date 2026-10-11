<?php
declare(strict_types=1);
/**
 * SB Stats directory index. Included by the local PHP router.
 * Cascades through nested folders without replacing legacy index.php symlinks.
 */
$root = realpath(dirname(__DIR__));
$directory = realpath($directory ?? $root);
if ($root === false || $directory === false ||
    !($directory === $root || str_starts_with($directory, $root . DIRECTORY_SEPARATOR))) {
    http_response_code(403); exit('Forbidden');
}
$relative = trim(substr($directory, strlen($root)), DIRECTORY_SEPARATOR);
$items = [];
foreach (new DirectoryIterator($directory) as $entry) {
    if ($entry->isDot()) continue;
    $name = $entry->getFilename();
    if (str_starts_with($name, '.') || preg_match('/\.(?:pem|crt|key|tar|ext|int)$/i', $name)) continue;
    if ($name === 'keep') continue;
    if ($entry->isLink()) {
        // Show historical symlink index.php as the inherited directory view.
        if ($name !== 'index.php') continue;
        $items[] = ['name' => $name, 'dir' => false, 'size' => null,
                    'time' => $entry->getMTime(), 'image' => false, 'stats' => false];
        continue;
    }
    $items[] = ['name' => $name, 'dir' => $entry->isDir(), 'size' => $entry->isFile() ? $entry->getSize() : null,
                'time' => $entry->getMTime(), 'stats' => $entry->isFile() && str_ends_with($name, '.sbstats'), 'image' => $entry->isFile() &&
                preg_match('/\\.(?:png|jpe?g|gif|webp)$/i', $name) === 1 &&
                $entry->getSize() <= 5 * 1024 * 1024];
}
usort($items, static function($a, $b) {
    return (($b['dir'] <=> $a['dir']) ?: strnatcasecmp($a['name'], $b['name']));
});
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function sbstatsFormatSize(int $n): string {
    $units = ['B', 'KB', 'MB', 'GB']; $v = (float)$n; $i=0;
    while ($v >= 1024 && $i < count($units)-1) { $v /= 1024; $i++; }
    return ($i ? number_format($v, 1) : (string)$n) . ' ' . $units[$i];
}
$parts = $relative === '' ? [] : explode(DIRECTORY_SEPARATOR, $relative);
$breadcrumbs = [['label'=>'SB Stats', 'url'=>'/']];
$url = '';
foreach ($parts as $part) { $url .= rawurlencode($part) . '/'; $breadcrumbs[] = ['label'=>$part,'url'=>'/'.$url]; }
$prefix = '/' . ($relative === '' ? '' : implode('/',array_map('rawurlencode',$parts)).'/');
$isLegacy = getenv('SBSTATS_LEGACY') === '1';
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Index of <?= h('/'.$relative) ?> | SB Stats</title>
<style>
body{font:15px system-ui,sans-serif;background:#f6f8fa;color:#203249;margin:0}
main{max-width:1000px;padding:22px;margin:auto}
a{color:#245e8e;text-decoration:none}a:hover{text-decoration:underline}
nav{display:flex;flex-wrap:wrap;gap:6px;color:#657589}
table{border-collapse:collapse;width:100%;background:white;border:1px solid #d6e0e9}
th,td{padding:9px 11px;border-bottom:1px solid #e9edf1;text-align:left}
th{background:#eaf0f6}td:nth-child(2),td:nth-child(3){white-space:nowrap;color:#617386}
.ico{display:inline-block;width:1.7em}.note{color:#617386}.preview{display:block;max-width:min(100%,380px);max-height:175px;object-fit:contain;margin:8px 0;border:1px solid #e4ebf2;border-radius:4px}
@media(max-width:600px){td:nth-child(3),th:nth-child(3){display:none}}
</style></head><body><main>
<nav><?php foreach($breadcrumbs as $i=>$crumb): ?><?= $i ? ' / ' : '' ?><a href="<?=h($crumb['url'])?>"><?=h($crumb['label'])?></a><?php endforeach; ?></nav>
<h1>Index of /<?=h($relative)?></h1>
<?php if (!$isLegacy): ?><p class="note">Legacy PHP execution is disabled. Use <code>bash scripts/php-serve.sh --legacy</code> locally to inspect the original scripts.</p><?php endif; ?>
<table><thead><tr><th>Name</th><th>Size</th><th>Modified</th></tr></thead><tbody>
<?php if ($relative !== ''): ?><tr><td><span class="ico">↩</span><a href="../">Parent directory</a></td><td></td><td></td></tr><?php endif; ?>
<?php foreach ($items as $item):
 $link=$prefix.rawurlencode($item['name']).($item['dir']?'/':'');
 $icon=$item['dir']?'📁':(str_ends_with(strtolower($item['name']),'.php')?'⌘':'▤');
?>
<tr><td><span class="ico"><?= $icon ?></span><a href="<?=h($link)?>"><?=h($item['name'])?><?= $item['dir']?'/':'' ?></a>
<?php if ($item['stats'] && str_starts_with($relative,'2010/')): 
  $graph='/scripts/vmstat-graph.php?file='.rawurlencode(str_replace(DIRECTORY_SEPARATOR,'/',$relative).'/'.$item['name']); ?>
<a href="<?=h($graph)?>" target="_blank" rel="noopener">Graph PNG</a>
<a href="<?=h($graph)?>" target="_blank" rel="noopener"><img class="preview" loading="lazy" src="<?=h($graph)?>" alt="Five-minute vmstat chart: <?=h($item['name'])?>"></a>
<?php endif; ?>
<?php if ($item['image']): ?><a href="<?=h($link)?>"><img class="preview" loading="lazy" src="<?=h($link)?>" alt="Preview of <?=h($item['name'])?>"></a><?php endif; ?></td>
<td><?= $item['size']===null?'—':h(sbstatsFormatSize($item['size'])) ?></td><td><?=h(gmdate('Y-m-d H:i',$item['time']))?> UTC</td></tr>
<?php endforeach; ?></tbody></table>
<p class="note"><?=count($items)?> entries. Directory contents are read from the filesystem; no database or generated navigation manifest.</p>
</main></body></html>
