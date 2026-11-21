<?php
require_once 'dbc.php';
require_login();

$sql = "SELECT e.id, e.name AS employee, m.name AS manager
FROM employees e
LEFT JOIN employees m ON e.manager_id = m.id
ORDER BY e.id";
$rows = db_all($sql);

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
        <thead><tr><th scope="col">ID</th><th scope="col">Employee</th><th scope="col">Manager</th></tr></thead>
        <tbody>
<?php foreach ($rows as $r) { ?>
          <tr>
            <td class="muted"><?php echo e($r['id']) ?></td>
            <td><?php echo e($r['employee']) ?></td>
            <td><?php echo $r['manager'] !== null ? e($r['manager']) : '<span class="muted">No manager (top of the company)</span>' ?></td>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>
<?php } else { empty_state('There are no employees yet.'); } ?>
  </div>
</main>
<?php require_once 'inc/footer.php'; ?>
