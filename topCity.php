<?php
require_once 'dbc.php';
require_login();

$citySql = "SELECT DISTINCT city FROM customers WHERE city IS NOT NULL AND city <> '' ORDER BY city";
$sql = "SELECT id, name, email, salary, city
FROM customers
WHERE city = ?
ORDER BY salary DESC, name
LIMIT 3";

$cities = array_column(db_all($citySql), 'city');
$raw = '';
$error = '';
$rows = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = post_str('city');
    $error = (string) check_name($raw, 1, 50, 'City');
    if ($error === '') {
        $rows = db_all($sql, 's', [$raw]);
    }
}

require_once 'inc/header.php';
require_once 'inc/nav.php';
?>
<main>
<?php require_once 'inc/errors.php'; ?>
<?php page_head($sql); ?>
  <div class="container content">
    <form class="query-form" action="topCity.php" method="post" novalidate>
      <div>
        <label for="city">City name</label>
        <input type="text" id="city" name="city" maxlength="50" list="city-list" placeholder="For example Cairo" required
               autocomplete="off" value="<?php echo e($raw) ?>"<?php echo $error !== '' ? ' aria-invalid="true"' : '' ?>>
        <datalist id="city-list">
<?php foreach ($cities as $c) { ?>
          <option value="<?php echo e($c) ?>"></option>
<?php } ?>
        </datalist>
      </div>
      <button type="submit" class="btn btn-primary">Show top 3</button>
      <?php field_error($error) ?>
    </form>

<?php if ($rows !== null) { ?>
    <div style="margin-top:28px">
<?php if ($rows) { ?>
      <p class="result-meta">Highest salaries in <strong><?php echo e($rows[0]['city']) ?></strong>.</p>
<?php customers_table($rows); ?>
<?php } else { empty_state('No customers found in “' . $raw . '”. Available cities: ' . implode(', ', $cities) . '.'); } ?>
    </div>
<?php } ?>
  </div>
</main>
<?php require_once 'inc/footer.php'; ?>
