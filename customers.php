<?php
require_once 'dbc.php';
require_login();

$sql = "SELECT id, name, email, salary, city
FROM customers
WHERE salary > 20000
ORDER BY salary DESC, name";
$rows = db_all($sql);

require_once 'inc/header.php';
require_once 'inc/nav.php';
?>
<main>
<?php require_once 'inc/errors.php'; ?>
<?php page_head($sql); ?>
  <div class="container content">
<?php if ($rows) { ?>
    <p class="result-meta"><strong><?php echo plural(count($rows), 'customer') ?></strong> earn more than 20,000.</p>
<?php customers_table($rows); ?>
<?php } else { empty_state('No customer earns more than 20,000.'); } ?>
  </div>
</main>
<?php require_once 'inc/footer.php'; ?>
