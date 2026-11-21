<?php
require_once 'dbc.php';
require_login();

$sql = "SELECT p.id, p.name,
       SUM(o.quantity)           AS pieces,
       SUM(o.quantity * p.price) AS earnings
FROM products p
JOIN orders o ON o.product_id = p.id
GROUP BY p.id, p.name
ORDER BY pieces DESC";
$rows = db_all($sql);
$max = 0;
foreach ($rows as $r) { $max = max($max, (int) $r['pieces']); }

require_once 'inc/header.php';
require_once 'inc/nav.php';
?>
<main>
<?php require_once 'inc/errors.php'; ?>
<?php page_head($sql); ?>
  <div class="container content">
<?php if ($rows) { ?>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th scope="col">#</th><th scope="col">Product</th><th scope="col" class="num">Pieces sold</th><th scope="col" class="num">Earnings</th></tr></thead>
        <tbody>
<?php foreach ($rows as $i => $r) { $n = (int) $r['pieces']; ?>
          <tr>
            <td class="muted"><?php echo $i + 1 ?></td>
            <td><?php echo e($r['name']) ?></td>
            <td class="num"><div class="bar-cell"><span class="meter" aria-hidden="true"><i style="width:<?php echo $max ? round($n / $max * 100) : 0 ?>%"></i></span><?php echo number_format($n) ?></div></td>
            <td class="num"><?php echo fmt_num($r['earnings']) ?></td>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>
<?php } else { empty_state('No product has been sold yet.'); } ?>
  </div>
</main>
<?php require_once 'inc/footer.php'; ?>
