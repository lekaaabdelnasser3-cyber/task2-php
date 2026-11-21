<?php
require_once 'dbc.php';
require_login();

$citySql = "SELECT DISTINCT city FROM customers WHERE city IS NOT NULL AND city <> '' ORDER BY city";
$sql = "SELECT id, name, email, salary, city
FROM customers
WHERE city = ?
ORDER BY name";

$cities = array_column(db_all($citySql), 'city');
$chosen = '';
$error = '';
$rows = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chosen = post_str('city');
    // Only a city that really exists in the list is accepted.
    if (!in_array($chosen, $cities, true)) {
        $error = 'Choose a city from the list.';
        $chosen = '';
    } else {
        $rows = db_all($sql, 's', [$chosen]);
    }
}

require_once 'inc/header.php';
require_once 'inc/nav.php';
?>
<main>
<?php require_once 'inc/errors.php'; ?>
<?php page_head($citySql . ";\n\n" . $sql); ?>
  <div class="container content">
    <form class="query-form" action="byCity.php" method="post" novalidate>
      <div>
        <label for="city">City</label>
        <select id="city" name="city" required<?php echo $error !== '' ? ' aria-invalid="true"' : '' ?>>
          <option value="">Choose a city</option>
<?php foreach ($cities as $c) { ?>
          <option value="<?php echo e($c) ?>"<?php echo $c === $chosen ? ' selected' : '' ?>><?php echo e($c) ?></option>
<?php } ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Show customers</button>
      <?php field_error($error) ?>
    </form>

<?php if ($rows !== null) { ?>
    <div style="margin-top:28px">
<?php if ($rows) { ?>
      <p class="result-meta"><strong><?php echo plural(count($rows), 'customer') ?></strong> in <?php echo e($chosen) ?>, A to Z.</p>
<?php customers_table($rows); ?>
<?php } else { empty_state('No customers live in ' . $chosen . '.'); } ?>
    </div>
<?php } ?>
  </div>
</main>
<?php require_once 'inc/footer.php'; ?>
