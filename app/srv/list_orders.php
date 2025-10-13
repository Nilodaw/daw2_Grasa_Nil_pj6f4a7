<?php
/* list_orders.php
 * GET: lee el fichero y genera una tabla HTML simple.
 */
declare(strict_types=1);

define('ORDERS_DIR', __DIR__ . '/../../onlineOrders');
define('ORDERS_DB',  ORDERS_DIR . '/onlineOrders.db');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Content-Type: text/plain; charset=utf-8');
    echo "ERROR: Method must be GET";
    exit;
}

$rows = [];
if (is_file(ORDERS_DB)) {
    $lines = file(ORDERS_DB, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $ln) {
        $parts = explode('|', $ln);
        if (count($parts) !== 7) continue; // línea corrupta
        [$code,$customer,$product,$qty,$price,$vat,$total] = $parts;
        $rows[] = [
            'code'     => $code,
            'customer' => $customer,
            'product'  => $product,
            'qty'      => (int)$qty,
            'price'    => (float)$price,
            'vat'      => (float)$vat,
            'total'    => (float)$total,
        ];
    }
}

/* Salida HTML mínima (solo aquí el servidor genera HTML) */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Orders</title>
<style>
  body { font-family: system-ui, Arial, sans-serif; background:#f6f7fb; color:#111; }
  main { max-width: 1000px; margin: 24px auto; background:#fff; border:1px solid #e6e8ef; border-radius:12px; padding:20px; }
  table { width:100%; border-collapse: collapse; }
  th, td { border:1px solid #e6e8ef; padding:8px 10px; text-align:left; }
  th { background:#f0f2f8; }
  .actions { margin-top:12px; }
  .btn { display:inline-block; padding:8px 12px; border:1px solid #7d8590; border-radius:10px; text-decoration:none; color:#111; }
  .btn:hover { background:#7d8590; color:#fff; }
</style>
</head>
<body>
<main>
  <h1>All Orders</h1>
  <?php if (!$rows): ?>
    <p>No orders yet.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Code</th>
        <th>Customer</th>
        <th>Product</th>
        <th>Qty</th>
        <th>Unit Price (€)</th>
        <th>VAT 21% (€)</th>
        <th>Total (€)</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['code']) ?></td>
        <td><?= htmlspecialchars($r['customer']) ?></td>
        <td><?= htmlspecialchars($r['product']) ?></td>
        <td><?= (int)$r['qty'] ?></td>
        <td><?= number_format($r['price'], 2, '.', '') ?></td>
        <td><?= number_format($r['vat'],   2, '.', '') ?></td>
        <td><?= number_format($r['total'], 2, '.', '') ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <div class="actions">
    <a class="btn" href="../cli/menu.html">Back to Menu</a>
  </div>
</main>
</body>
</html>
