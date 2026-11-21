<?php
$current = current_page();
$groups = [];
foreach (query_pages() as $p) {
    $groups[$p['group']][] = $p;
}
// Customers and Sales open as menus; Employees has a single page so it stays a plain link.
$menuOrder = ['Customers', 'Sales'];
?>
<header class="site-header">
  <div class="container bar">
    <a class="brand" href="home.php">
      <svg width="26" height="26" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
        <ellipse cx="16" cy="7" rx="11" ry="4.5" fill="#F29111"/>
        <path d="M5 7v8c0 2.5 4.9 4.5 11 4.5s11-2 11-4.5V7c0 2.5-4.9 4.5-11 4.5S5 9.5 5 7z" fill="#5CC4DA"/>
        <path d="M5 15v8c0 2.5 4.9 4.5 11 4.5s11-2 11-4.5v-8c0 2.5-4.9 4.5-11 4.5S5 17.5 5 15z" fill="#00758F"/>
      </svg>
      <span>MySQL Hub</span>
    </a>

    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">Menu</button>

    <nav id="site-nav" class="site-nav" aria-label="Main">
      <ul>
        <li><a href="home.php"<?php echo $current === 'home.php' ? ' class="active" aria-current="page"' : '' ?>>Home</a></li>
<?php if (is_logged_in()) { ?>
<?php foreach ($menuOrder as $g) {
    $isActive = false;
    foreach ($groups[$g] as $p) { if ($p['file'] === $current) { $isActive = true; } }
?>
        <li class="has-menu">
          <button type="button" class="menu-btn<?php echo $isActive ? ' active' : '' ?>" aria-expanded="false" aria-haspopup="true"><?php echo e($g) ?></button>
          <ul class="menu">
<?php foreach ($groups[$g] as $p) { ?>
            <li><a href="<?php echo e($p['file']) ?>"<?php echo $p['file'] === $current ? ' class="active" aria-current="page"' : '' ?>><?php echo e($p['nav']) ?></a></li>
<?php } ?>
          </ul>
        </li>
<?php } ?>
        <li><a href="employees.php"<?php echo $current === 'employees.php' ? ' class="active" aria-current="page"' : '' ?>>Employees</a></li>
        <li class="nav-user"><span class="user-chip"><?php echo e($_SESSION['user_name'] ?? '') ?></span></li>
        <li><a class="btn btn-ghost btn-sm" href="logout.php">Log out</a></li>
<?php } else { ?>
        <li><a href="login.php"<?php echo $current === 'login.php' ? ' class="active" aria-current="page"' : '' ?>>Log in</a></li>
        <li><a class="btn btn-amber btn-sm" href="register.php">Create account</a></li>
<?php } ?>
      </ul>
    </nav>
  </div>
</header>
