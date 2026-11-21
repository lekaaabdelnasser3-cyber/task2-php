<?php
require_once 'dbc.php';
require_login();

$sql = "SELECT c.id, c.name, COUNT(o.id) AS orders_count
FROM customers c
LEFT JOIN orders o ON o.customer_id = c.id
GROUP BY c.id, c.name
ORDER BY orders_count DESC, c.name";
$rows = db_all($sql);
$max = 0;
foreach ($rows as $r) { $max = max($max, (int) $r['orders_count']); }

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
        <thead><tr><th scope="col">#</th><th scope="col">Customer</th><th scope="col" class="num">Orders</th></tr></thead>
        <tbody>
<?php foreach ($rows as $i => $r) { $n = (int) $r['orders_count']; ?>
          <tr>
            <td class="muted"><?php echo $i + 1 ?></td>
            <td><?php echo e($r['name']) ?></td>
            <td class="num"><div class="bar-cell"><span class="meter" aria-hidden="true"><i style="width:<?php echo $max ? round($n / $max * 100) : 0 ?>%"></i></span><?php echo number_format($n) ?></div></td>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>
<?php } else { empty_state('There are no customers yet.'); } ?>
  </div>
</main>
<?php require_once 'inc/footer.php'; ?>
