<?php
require_once 'dbc.php';
if (is_logged_in()) {
    redirect('home.php');
}
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$pageTitle = 'Log in';
require_once 'inc/header.php';
require_once 'inc/nav.php';
?>
<main class="auth">
  <aside class="auth-art">
    <div>
      <h2>Ten queries, one small shop.</h2>
      <p>Log in to explore customers, orders, products and employees stored in MySQL.</p>
    </div>
  </aside>

  <section class="auth-form">
    <div class="box">
      <h1>Log in</h1>
      <p class="sub">Use the email and password of any customer.</p>
<?php require_once 'inc/errors.php'; ?>
      <form action="handle/handleLogin.php" method="post" novalidate>
        <?php echo csrf_field() ?>
        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" maxlength="100" autocomplete="email" required
                 value="<?php echo e($old['email'] ?? '') ?>">
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" maxlength="50" autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Log in</button>
      </form>
      <p class="switch">New here? <a href="register.php">Create an account</a></p>
    </div>
  </section>
</main>
<?php require_once 'inc/footer.php'; ?>
