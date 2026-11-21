<?php
require_once 'dbc.php';

$pageTitle = 'Welcome to MySQL';
$loggedIn  = is_logged_in();

if ($loggedIn) {
    $counts = [
        'customers' => (int) db_value('SELECT COUNT(*) FROM customers'),
        'orders'    => (int) db_value('SELECT COUNT(*) FROM orders'),
        'products'  => (int) db_value('SELECT COUNT(*) FROM products'),
        'employees' => (int) db_value('SELECT COUNT(*) FROM employees'),
    ];
}

require_once 'inc/header.php';
require_once 'inc/nav.php';
require_once 'inc/errors.php';
?>
<main>
  <section class="hero">
    <div class="container">
      <h1>Welcome to MySQL</h1>
      <p>Ten practical queries on a small shop database: customers, orders, products and employees.</p>
      <div class="actions">
<?php if ($loggedIn) { ?>
        <a class="btn btn-amber" href="#queries">Browse the queries</a>
<?php } else { ?>
        <a class="btn btn-amber" href="login.php">Log in</a>
        <a class="btn btn-ghost" href="register.php">Create account</a>
<?php } ?>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container home-grid">
      <aside class="db-panel">
        <h2>What is in the database</h2>
<?php if ($loggedIn) { ?>
        <p>Four tables, created by <code>mysql_task_database.sql</code>.</p>
        <dl>
          <div class="row"><dt>customers</dt><dd><?php echo number_format($counts['customers']) ?> rows</dd></div>
          <div class="row"><dt>orders</dt><dd><?php echo number_format($counts['orders']) ?> rows</dd></div>
          <div class="row"><dt>products</dt><dd><?php echo number_format($counts['products']) ?> rows</dd></div>
          <div class="row"><dt>employees</dt><dd><?php echo number_format($counts['employees']) ?> rows</dd></div>
        </dl>
<?php } else { ?>
        <div class="notice">
          <p>The queries are only available to customers who are logged in.</p>
          <p><a href="login.php">Log in</a> or <a href="register.php">create an account</a> to open them.</p>
        </div>
<?php } ?>
      </aside>

      <div id="queries">
        <div class="index-head"><h2>The ten queries</h2></div>
        <ol class="index">
<?php foreach (query_pages() as $i => $p) { ?>
          <li>
            <a href="<?php echo e($p['file']) ?>">
              <span class="n"><?php echo $i + 1 ?></span>
              <span class="t"><?php echo e($p['title']) ?></span>
              <span class="tag"><?php echo e($p['concept']) ?></span>
              <span class="d"><?php echo e($p['lead']) ?></span>
            </a>
          </li>
<?php } ?>
        </ol>
      </div>
    </div>
  </section>
</main>
<?php require_once 'inc/footer.php'; ?>
