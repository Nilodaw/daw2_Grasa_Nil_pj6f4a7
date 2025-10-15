<?php
declare(strict_types=1);
const ORDERS_DB = __DIR__.'/../../onlineOrders/onlineOrders.db';

function parse_order_line(string $ln): ?array { // incluye parseo de items a multidimensional
  $p=explode('|',trim($ln)); if(count($p)!==9) return null;
  $items=[]; foreach(array_filter(explode(',',$p[5])) as $ch){ [$n,$q]=array_pad(explode('x',$ch,2),2,'0'); $items[]=['name'=>$n,'qty'=>(int)$q]; }
  return ['code'=>$p[0],'customer'=>$p[1],'address'=>$p[2],'email'=>$p[3],'phone'=>$p[4],
          'items'=>$items,'items_str'=>$p[5],'subtotal'=>$p[6],'vat'=>$p[7],'total'=>$p[8]];
}
function read_all_orders(string $db): array { // llama a parse_order_line()
  $out=[]; if(!is_file($db)) return $out; $fp=fopen($db,'rb');
  while(($ln=fgets($fp))!==false){ $o=parse_order_line($ln); if($o) $out[]=$o; } fclose($fp); return $out;
}

if($_SERVER['REQUEST_METHOD']!=='GET'){ http_response_code(405); exit('Method must be GET'); }
$orders=read_all_orders(ORDERS_DB);
?>
<!doctype html><html lang="en"><meta charset="utf-8"><title>All Flower Orders</title>
<style>
 body{font-family:system-ui,Arial;background:#f6f7fb;margin:0}
 main{max-width:1000px;margin:24px auto;background:#fff;border:1px solid #e6e8ef;border-radius:12px;padding:20px}
 .actions{display:flex;gap:12px;margin-top:14px}
 a.btn{display:inline-block;padding:8px 12px;border:1px solid #7d8590;border-radius:10px;text-decoration:none;color:#111}
 a.btn:hover{background:#7d8590;color:#fff}
 .line{padding:6px 8px;border-bottom:1px dashed #e0e3ea;white-space:pre-wrap;font-family:ui-monospace,Menlo,monospace}
 .empty{color:#666}
</style>
<main>
  <h1>All Flower Orders</h1>
  <?php if(!$orders): ?><p class="empty">No orders yet.</p>
  <?php else: foreach($orders as $o): ?>
    <div class="line">
      <?=htmlspecialchars($o['code'])?> :
      <?=htmlspecialchars($o['customer'])?> :
      <?=htmlspecialchars($o['address'])?> :
      <?=htmlspecialchars($o['email'])?> :
      <?=htmlspecialchars($o['phone'])?> :
      <?=htmlspecialchars($o['items_str'])?> :
      <?=htmlspecialchars($o['subtotal'])?> :
      <?=htmlspecialchars($o['vat'])?> :
      <?=htmlspecialchars($o['total'])?>
    </div>
  <?php endforeach; endif; ?>
  <div class="actions">
    <a class="btn" href="../cli/menu.html">Back to Menu</a>
    <a class="btn" href="../cli/index.html">Back to Home</a>
  </div>
</main>
</html>
