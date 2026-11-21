<?php
require_once 'dbc.php';
require_login();

$sql = "SELECT id, name, email, salary, city
FROM customers
WHERE id = ?";
$raw = '';
$error = '';
$customer = null;
$searched = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = post_str('id');
    $error = (string) check_int($raw, 1, ID_MAX, 'Customer ID');
    if ($error === '') {
        $searched = true;
        $customer = db_one($sql, 'i', [(int) $raw]);
    }
}

require_once 'inc/header.php';
require_once 'inc/nav.php';
?>
<main>
<?php require_once 'inc/errors.php'; ?>
<?php page_head($sql); ?>
  <div class="container content">
    <form class="query-form" action="selectOne.php" method="post" novalidate>
      <div>
        <label for="id">Customer ID</label>
        <input type="number" id="id" name="id" min="1" step="1" inputmode="numeric" placeholder="For example 7"
               required value="<?php echo e($raw) ?>"<?php echo $error !== '' ? ' aria-invalid="true"' : '' ?>>
      </div>
      <button type="submit" class="btn btn-primary">Show customer</button>
      <?php field_error($error) ?>
    </form>

<?php if ($customer) { ?>
    <h2 style="margin-top:28px">Customer #<?php echo e($customer['id']) ?></h2>
    <dl class="profile">
      <div><dt>ID</dt><dd><?php echo e($customer['id']) ?></dd></div>
      <div><dt>Name</dt><dd><?php echo e($customer['name']) ?></dd></div>
      <div class="wide"><dt>Email</dt><dd><?php echo e($customer['email']) ?></dd></div>
      <div><dt>Salary</dt><dd><?php echo fmt_num($customer['salary']) ?></dd></div>
      <div><dt>City</dt><dd><?php echo $customer['city'] ? e($customer['city']) : '<span class="muted">Not set</span>' ?></dd></div>
    </dl>
    <p class="hint">The password is stored as a hash and is never shown.</p>
<?php } elseif ($searched) { ?>
    <div style="margin-top:28px"><?php empty_state('No customer has ID ' . (int) $raw . '. Try a smaller number.'); ?></div>
<?php } ?>
  </div>
</main>
<?php require_once 'inc/footer.php'; ?>
