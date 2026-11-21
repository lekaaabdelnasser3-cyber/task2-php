<?php
require_once 'dbc.php';
require_login();

$productSql = "SELECT id, name, price FROM products WHERE id = ?";
$ordersSql  = "SELECT id, quantity FROM orders WHERE product_id = ? ORDER BY id";
$buyersSql  = "SELECT DISTINCT c.id, c.name, c.salary
FROM customers c
JOIN orders o ON o.customer_id = c.id
WHERE o.product_id = ?
ORDER BY c.salary DESC, c.name";

$allProducts = db_all("SELECT id, name FROM products ORDER BY id");
$raw = '';
$error = '';
$searched = false;
$product = null;
$orders = [];
$buyers = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = post_str('id');
    $error = (string) check_int($raw, 1, ID_MAX, 'Product ID');
    if ($error === '') {
        $searched = true;
        $id = (int) $raw;
        $product = db_one($productSql, 'i', [$id]);
        if ($product) {
            $orders = db_all($ordersSql, 'i', [$id]);
            $buyers = db_all($buyersSql, 'i', [$id]);
        }
    }
}
$pieces = 0;
foreach ($orders as $o) { $pieces += (int) $o['quantity']; }

require_once 'inc/header.php';
require_once 'inc/nav.php';
?>
<main>
<?php require_once 'inc/errors.php'; ?>
<?php page_head($productSql . ";\n\n" . $ordersSql . ";\n\n" . $buyersSql); ?>
  <div class="container content">
    <form class="query-form" action="productDetails.php" method="post" novalidate>
      <div>
        <label for="id">Product ID</label>
        <input type="number" id="id" name="id" min="1" step="1" inputmode="numeric" placeholder="For example 3" required
               value="<?php echo e($raw) ?>"<?php echo $error !== '' ? ' aria-invalid="true"' : '' ?>>
      </div>
      <button type="submit" class="btn btn-primary">Show details</button>
      <?php field_error($error) ?>
    </form>

    <details class="ref">
      <summary>Which product IDs exist?</summary>
      <ul class="chips">
<?php foreach ($allProducts as $p) { ?>
        <li class="chip"><b><?php echo e($p['id']) ?></b><?php echo e($p['name']) ?></li>
<?php } ?>
      </ul>
    </details>

<?php if ($product) { ?>
    <h2 style="margin-top:32px"><?php echo e($product['name']) ?></h2>
    <div class="stats">
      <div class="stat"><b><?php echo number_format(count($orders)) ?></b><span>times sold (orders)</span></div>
      <div class="stat"><b><?php echo number_format($pieces) ?></b><span>pieces in total</span></div>
      <div class="stat"><b><?php echo number_format(count($buyers)) ?></b><span>different customers</span></div>
    </div>

<?php if ($orders) { ?>
    <h2>Order numbers</h2>
    <ul class="chips">
<?php foreach ($orders as $o) { ?>
      <li class="chip">#<?php echo e($o['id']) ?></li>
<?php } ?>
    </ul>

    <h2>Customers who bought it, richest first</h2>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th scope="col">#</th><th scope="col">Customer</th><th scope="col" class="num">Salary</th></tr></thead>
        <tbody>
<?php foreach ($buyers as $i => $b) { ?>
          <tr><td class="muted"><?php echo $i + 1 ?></td><td><?php echo e($b['name']) ?></td><td class="num"><?php echo fmt_num($b['salary']) ?></td></tr>
<?php } ?>
        </tbody>
      </table>
    </div>
<?php } else { ?>
    <div style="margin-top:16px"><?php empty_state('This product has not been sold yet.'); ?></div>
<?php } ?>
<?php } elseif ($searched) { ?>
    <div style="margin-top:28px"><?php empty_state('No product has ID ' . (int) $raw . '. Open the list of product IDs above.'); ?></div>
<?php } ?>
  </div>
</main>
<?php require_once 'inc/footer.php'; ?>
