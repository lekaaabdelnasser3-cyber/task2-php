<?php
require_once 'dbc.php';
require_login();

$sql = "SELECT id, name, email, salary, city
FROM customers
WHERE name LIKE ?
ORDER BY name";
$raw = '';
$error = '';
$rows = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = post_str('name');
    $error = (string) check_name($raw, 1, 50, 'Name');
    if ($error === '') {
        // Validation above only lets letters through, so the text cannot contain % or _ wildcards.
        $rows = db_all($sql, 's', ['%' . $raw . '%']);
    }
}

require_once 'inc/header.php';
require_once 'inc/nav.php';
?>
<main>
<?php require_once 'inc/errors.php'; ?>
<?php page_head($sql . "\n-- ? is your text with a % on each side"); ?>
  <div class="container content">
    <form class="query-form" action="searchName.php" method="post" novalidate>
      <div>
        <label for="name">Customer name</label>
        <input type="text" id="name" name="name" maxlength="50" placeholder="For example Ahmed" required
               value="<?php echo e($raw) ?>"<?php echo $error !== '' ? ' aria-invalid="true"' : '' ?>>
      </div>
      <button type="submit" class="btn btn-primary">Search</button>
      <?php field_error($error) ?>
    </form>

<?php if ($rows !== null) { ?>
    <div style="margin-top:28px">
<?php if ($rows) { ?>
      <p class="result-meta"><strong><?php echo plural(count($rows), 'customer') ?></strong> match &ldquo;<?php echo e($raw) ?>&rdquo;.</p>
<?php customers_table($rows); ?>
<?php } else { empty_state('No customer name contains “' . $raw . '”. Try a shorter part of the name.'); } ?>
    </div>
<?php } ?>
  </div>
</main>
<?php require_once 'inc/footer.php'; ?>
