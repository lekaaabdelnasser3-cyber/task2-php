<?php
require_once 'dbc.php';
require_login();

$sql = "SELECT p.id, p.name, SUM(o.quantity) AS total
FROM products p
JOIN orders o ON o.product_id = p.id
GROUP BY p.id, p.name
HAVING SUM(o.quantity) > ?
ORDER BY total DESC";
$raw = '';
$error = '';
$rows = null;
$number = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = post_str('number');
    $error = (string) check_int($raw, 100, 5000, 'Number');
    if ($error === '') {
        $number = (int) $raw;
        $rows = db_all($sql, 'i', [$number]);
    }
}

require_once 'inc/header.php';
require_once 'inc/nav.php';
?>
<main>
<?php require_once 'inc/errors.php'; ?>
<?php page_head($sql); ?>
  <div class="container content">
    <form class="query-form" action="productsTotal.php" method="post" novalidate>
      <div>
        <label for="number">Minimum pieces sold</label>
        <input type="number" id="number" name="number" min="100" max="5000" step="1" inputmode="numeric"
               placeholder="100 to 5,000" required value="<?php echo e($raw) ?>"<?php echo $error !== '' ? ' aria-invalid="true"' : '' ?>>
      </div>
      <button type="submit" class="btn btn-primary">Show products</button>
      <?php field_error($error) ?>
    </form>

<?php if ($rows !== null) { ?>
    <div style="margin-top:28px">
<?php if ($rows) { ?>
      <p class="result-meta"><strong><?php echo plural(count($rows), 'product') ?></strong> sold more than <?php echo number_format($number) ?> pieces in total.</p>
      <div class="table-wrap">
        <table class="table">
          <thead><tr><th scope="col">#</th><th scope="col">Product</th><th scope="col" class="num">Total pieces</th></tr></thead>
          <tbody>
<?php foreach ($rows as $i => $r) { ?>
            <tr><td class="muted"><?php echo $i + 1 ?></td><td><?php echo e($r['name']) ?></td><td class="num"><?php echo number_format((int) $r['total']) ?></td></tr>
<?php } ?>
          </tbody>
        </table>
      </div>
<?php } else { empty_state('No product has sold more than ' . number_format($number) . ' pieces. Try a smaller number.'); } ?>
    </div>
<?php } ?>
  </div>
</main>
<?php require_once 'inc/footer.php'; ?>
