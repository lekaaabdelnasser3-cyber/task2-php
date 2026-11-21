<?php
require_once '../dbc.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../login.php');
}

$email    = post_str('email');
$password = (isset($_POST['password']) && is_string($_POST['password'])) ? $_POST['password'] : '';

// ---- validation (task 12)
$errors = [];
if (!csrf_valid()) {
    $errors[] = 'Your session expired. Please try again.';
}
if ($msg = check_email($email)) {
    $errors[] = $msg;
}
if ($password === '' || text_length($password) > 50) {
    $errors[] = 'Enter your password (up to 50 characters).';
}

if ($errors) {
    $_SESSION['errors'] = implode(' ', $errors);
    $_SESSION['old'] = ['email' => $email];
    redirect('../login.php');
}

$user = db_one('SELECT id, name, password FROM customers WHERE email = ?', 's', [$email]);

// Verify against a dummy hash when the email is unknown so both cases take the same time.
$hash = $user ? $user['password'] : '$2y$10$UCrFCh1cgHZheVPc8bSLjO6xQTf6brDu2mL2PQqX6fdAEbllq6Etu';

if (password_verify($password, $hash) && $user) {
    session_regenerate_id(true);
    $_SESSION['user_id']   = (int) $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['success']   = 'Welcome back, ' . $user['name'] . '.';

    // Send people back to the page they originally asked for.
    $target = 'home.php';
    if (!empty($_SESSION['intended']) && page_info($_SESSION['intended'])) {
        $target = $_SESSION['intended'];
    }
    unset($_SESSION['intended']);
    redirect('../' . $target);
}

$_SESSION['errors'] = 'The email or password is not correct.';
$_SESSION['old'] = ['email' => $email];
redirect('../login.php');
