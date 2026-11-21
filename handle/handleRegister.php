<?php
require_once '../dbc.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../register.php');
}

$name     = post_str('name');
$email    = post_str('email');
$password = (isset($_POST['password']) && is_string($_POST['password'])) ? $_POST['password'] : '';

// ---- validation (task 12): right type of data, sensible length
$errors = [];
if (!csrf_valid()) {
    $errors[] = 'Your session expired. Please try again.';
}
if ($msg = check_name($name, 2, 100, 'Name')) {
    $errors[] = $msg;
}
if ($msg = check_email($email)) {
    $errors[] = $msg;
}
if ($msg = check_password($password, 6, 50)) {
    $errors[] = $msg;
}

if (!$errors && db_one('SELECT id FROM customers WHERE email = ?', 's', [$email])) {
    $errors[] = 'That email is already registered. Try logging in instead.';
}

if ($errors) {
    $_SESSION['errors'] = implode(' ', $errors);
    $_SESSION['old'] = ['name' => $name, 'email' => $email];
    redirect('../register.php');
}

try {
    db_exec(
        'INSERT INTO customers (name, email, password, salary, city) VALUES (?, ?, ?, 0, NULL)',
        'sss',
        [$name, $email, password_hash($password, PASSWORD_DEFAULT)]
    );
} catch (mysqli_sql_exception $ex) {
    // 1062 = duplicate key: someone registered the same email between our check and the insert.
    $_SESSION['errors'] = $ex->getCode() === 1062
        ? 'That email is already registered. Try logging in instead.'
        : 'We could not create your account. Please try again.';
    $_SESSION['old'] = ['name' => $name, 'email' => $email];
    redirect('../register.php');
}

$_SESSION['success'] = 'Account created. You can log in now.';
redirect('../login.php');
