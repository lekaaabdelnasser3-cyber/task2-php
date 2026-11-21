<?php
require_once 'dbc.php';
if (is_logged_in()) {
    redirect('home.php');
}
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$pageTitle = 'Create account';
require_once 'inc/header.php';
require_once 'inc/nav.php';
?>
<main class="auth">
  <aside class="auth-art">
    <div>
      <h2>Add yourself as a customer.</h2>
      <p>Your account is a new row in the customers table, with a hashed password.</p>
    </div>
  </aside>

  <section class="auth-form">
    <div class="box">
      <h1>Create account</h1>
      <p class="sub">It takes a few seconds.</p>
<?php require_once 'inc/errors.php'; ?>
      <form action="handle/handleRegister.php" method="post" novalidate>
        <?php echo csrf_field() ?>
        <div class="field">
          <label for="name">Full name</label>
          <input type="text" id="name" name="name" minlength="2" maxlength="100" autocomplete="name" required
                 value="<?php echo e($old['name'] ?? '') ?>">
        </div>
        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" maxlength="100" autocomplete="email" required
                 value="<?php echo e($old['email'] ?? '') ?>">
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" minlength="6" maxlength="50" autocomplete="new-password" required>
          <p class="hint">6 to 50 characters.</p>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Create account</button>
      </form>
      <p class="switch">Already registered? <a href="login.php">Log in</a></p>
    </div>
  </section>
</main>
<?php require_once 'inc/footer.php'; ?>
