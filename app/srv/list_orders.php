<?php
declare(strict_types=1);
const ORDERS_DB = __DIR__ . '/../../onlineOrders/onlineOrders.db';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') { http_response_code(405); exit('Method must be GET'); }

$rows = [];
if (is_file(ORDERS_DB)) {
  foreach (file(ORDERS_DB, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $ln) {
    $p = explode('|',$ln);
    if (count($p)===7) $rows[] = $p; // [code, customer, product, qty, price, vat, total]
  }
}
?>
<!doctype html><html lang="en"><meta charset="utf-8"><title>All Flower Orders</title>
<style>
 body{font-family:system-ui,Arial;background:#f6f7fb} main{max-width:1000px;margin:24px auto;background:#fff;border:1px solid #e6e8ef;border-radius:12px;padding:20px}
 table{width:100%;border-collapse:collapse} th,td{border:1px solid #e6e8ef;padding:8px 10px;text-align:left} th{background:#f0f2f8}
 a.btn{display:inline-block;margin-top:12px;padding:8px 12px;border:1px solid #7d8590;border-radius:10px;text-decoration:none;color:#111}
 a.btn:hover{background:#7d8590;color:#fff}
</style>
<main>
<h1>All Flower Orders</h1>
<?php if(!$rows): ?><p>No orders yet.</p>
<?php else: ?>
<table><thead><tr>
<th>Code</th><th>Customer</th><th>Flower</th><th>Qty</th><th>Unit Price (€)</th><th>VAT 21% (€)</th><th>Total (€)</th>
</tr></thead><tbody>
<?php foreach($rows as $r): ?>
<tr>
<td><?=htmlspecialchars($r[0])?></td>
<td><?=htmlspecialchars($r[1])?></td>
<td><?=htmlspecialchars($r[2])?></td>
<td><?= (int)$r[3] ?></td>
<td><?= number_format((float)$r[4],2,'.','') ?></td>
<td><?= number_format((float)$r[5],2,'.','') ?></td>
<td><?= number_format((float)$r[6],2,'.','') ?></td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php endif; ?>
<p><a class="btn" href="../cli/menu.html">Back to Menu</a></p>
</main>
</html>
