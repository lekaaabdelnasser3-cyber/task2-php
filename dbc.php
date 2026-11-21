<?php
/**
 * Bootstrap shared by every page: session + database connection + helpers.
 *
 * Database settings (XAMPP defaults). Import mysql_task_database.sql first;
 * it creates the database named below.
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mysql_task');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

require_once __DIR__ . '/inc/functions.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connection, 'utf8mb4');
} catch (Exception $ex) {
    error_log('DB connection failed: ' . $ex->getMessage());
    http_response_code(500);
    echo '<!doctype html><meta charset="utf-8"><title>Database unavailable</title>'
       . '<body style="font-family:system-ui,sans-serif;max-width:34rem;margin:12vh auto;padding:0 1rem;line-height:1.6">'
       . '<h1>The database is not reachable</h1>'
       . '<p>Start MySQL, import <code>mysql_task_database.sql</code>, and check the '
       . 'connection settings at the top of <code>dbc.php</code>.</p></body>';
    exit();
}

// Any unexpected database error ends here: log the details, show a plain page (no SQL or paths leak).
set_exception_handler(function ($ex) {
    error_log('Unhandled error: ' . $ex->getMessage());
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo '<!doctype html><meta charset="utf-8"><title>Something went wrong</title>'
       . '<body style="font-family:system-ui,sans-serif;max-width:34rem;margin:12vh auto;padding:0 1rem;line-height:1.6">'
       . '<h1>Something went wrong</h1><p>The page could not be loaded. Go back and try again.</p></body>';
});
