<?php
/**
 * Shared helpers: escaping, auth, CSRF, database access, validation, page registry.
 * Written for PHP 7.4+ (XAMPP).
 */

const ID_MAX = 2147483647; // largest value of an INT column

/* ------------------------------------------------------------------ output */

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $to): void
{
    header('Location: ' . $to);
    exit();
}

/* -------------------------------------------------------------------- auth */

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

/** Every query page calls this first (task 11): guests are sent to the login form. */
function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['intended'] = basename($_SERVER['SCRIPT_NAME']);
        $_SESSION['errors'] = 'Please log in to open that page.';
        redirect('login.php');
    }
}

/* -------------------------------------------------------------------- CSRF */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    $sent = $_POST['csrf'] ?? '';
    return is_string($sent) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $sent);
}

/* ---------------------------------------------------------------- database */
// Thin wrappers over mysqli prepared statements: user input is always bound, never concatenated.

function db_all(string $sql, string $types = '', array $params = []): array
{
    global $connection;
    $stmt = mysqli_prepare($connection, $sql);
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
    return $rows;
}

function db_one(string $sql, string $types = '', array $params = []): ?array
{
    $rows = db_all($sql, $types, $params);
    return $rows ? $rows[0] : null;
}

function db_value(string $sql, string $types = '', array $params = [])
{
    $row = db_one($sql, $types, $params);
    return $row ? reset($row) : null;
}

/** INSERT / UPDATE / DELETE. Returns the new auto-increment id (or affected rows). */
function db_exec(string $sql, string $types = '', array $params = []): int
{
    global $connection;
    $stmt = mysqli_prepare($connection, $sql);
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $id = mysqli_stmt_insert_id($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $id > 0 ? $id : $affected;
}

/* -------------------------------------------------------------- validation */
// Task 12. Each check_* returns null when the value is fine, otherwise a message for the user.

/** Trimmed string from $_POST; arrays or missing fields become ''. */
function post_str(string $key): string
{
    $v = $_POST[$key] ?? '';
    return is_string($v) ? trim($v) : '';
}

function text_length(string $s): int
{
    return function_exists('mb_strlen') ? mb_strlen($s, 'UTF-8') : strlen($s);
}

/** Whole number inside [$min, $max]. Rejects letters, decimals, signs and exponent notation. */
function check_int(string $raw, int $min, int $max, string $label): ?string
{
    $range = $max >= ID_MAX
        ? 'a positive whole number'
        : 'a whole number from ' . number_format($min) . ' to ' . number_format($max);
    if ($raw === '' || !preg_match('/^[0-9]{1,10}$/', $raw)) {
        return "$label must be $range.";
    }
    $n = (int) $raw;
    if ($n < $min || $n > $max) {
        return "$label must be $range.";
    }
    return null;
}

/** Letters (any language), spaces, dots, apostrophes and hyphens; length limited. */
function check_name(string $raw, int $min, int $max, string $label): ?string
{
    $len = text_length($raw);
    if ($len < $min || $len > $max) {
        return "$label must be $min to $max characters long.";
    }
    if (!preg_match('/^[\p{L}\p{M}][\p{L}\p{M} .\'-]*$/u', $raw)) {
        return "$label can only contain letters, spaces, dots, apostrophes and hyphens.";
    }
    return null;
}

function check_email(string $raw, int $max = 100): ?string
{
    if ($raw === '' || text_length($raw) > $max || !filter_var($raw, FILTER_VALIDATE_EMAIL)) {
        return 'Enter a valid email address (up to ' . $max . ' characters).';
    }
    return null;
}

function check_password(string $raw, int $min = 6, int $max = 50): ?string
{
    $len = text_length($raw);
    if ($len < $min || $len > $max) {
        return "Password must be $min to $max characters long.";
    }
    return null;
}

/* ----------------------------------------------------------- page registry */
// One list drives the navbar, the home index and each page's heading.

function query_pages(): array
{
    return [
        ['file' => 'customers.php',      'group' => 'Customers', 'nav' => 'Rich customers',
         'title' => 'Customers earning more than 20,000',
         'lead' => 'Every customer with a salary above 20,000, highest earner first.',
         'concept' => 'WHERE'],
        ['file' => 'selectOne.php',      'group' => 'Customers', 'nav' => 'Customer by ID',
         'title' => 'Find a customer by ID',
         'lead' => 'Enter an ID to see everything stored for that customer.',
         'concept' => 'WHERE id = ?'],
        ['file' => 'ordersCount.php',    'group' => 'Sales', 'nav' => 'Orders per customer',
         'title' => 'Orders per customer',
         'lead' => 'How many orders each customer has placed, customers with the most orders first.',
         'concept' => 'LEFT JOIN, GROUP BY, COUNT'],
        ['file' => 'topProducts.php',    'group' => 'Sales', 'nav' => 'Top products',
         'title' => 'Best-selling products',
         'lead' => 'Pieces sold and money earned for each product, best seller first.',
         'concept' => 'JOIN, GROUP BY, SUM'],
        ['file' => 'employees.php',      'group' => 'Employees', 'nav' => 'Employees',
         'title' => 'Employees and their managers',
         'lead' => 'Each employee next to the name of the person they report to.',
         'concept' => 'SELF JOIN'],
        ['file' => 'searchName.php',     'group' => 'Customers', 'nav' => 'Search by name',
         'title' => 'Search customers by name',
         'lead' => 'Type a name or part of one to list every customer who matches.',
         'concept' => 'LIKE'],
        ['file' => 'byCity.php',         'group' => 'Customers', 'nav' => 'Customers by city',
         'title' => 'Customers by city',
         'lead' => 'Choose a city to list its customers in alphabetical order.',
         'concept' => 'WHERE, ORDER BY'],
        ['file' => 'productsTotal.php',  'group' => 'Sales', 'nav' => 'Products by volume',
         'title' => 'Products sold above a quantity',
         'lead' => 'Enter a number from 100 to 5,000 to list the products whose total pieces sold exceed it.',
         'concept' => 'HAVING, SUM'],
        ['file' => 'topCity.php',        'group' => 'Customers', 'nav' => 'Top 3 in a city',
         'title' => 'The three richest customers in a city',
         'lead' => 'Type a city name to see the three highest salaries there.',
         'concept' => 'ORDER BY, LIMIT'],
        ['file' => 'productDetails.php', 'group' => 'Sales', 'nav' => 'Product details',
         'title' => 'Product sales details',
         'lead' => 'How many times a product sold, in which orders, and who bought it.',
         'concept' => 'JOIN, DISTINCT'],
    ];
}

function current_page(): string
{
    return basename($_SERVER['SCRIPT_NAME']);
}

function page_info(string $file): ?array
{
    foreach (query_pages() as $p) {
        if ($p['file'] === $file) {
            return $p;
        }
    }
    return null;
}

/* ------------------------------------------------------------ UI fragments */

/** 32,500 or 1,254.50 – no pointless trailing zeros. */
function fmt_num($n): string
{
    $f = (float) $n;
    return number_format($f, floor($f) == $f ? 0 : 2);
}

/** Title block of a query page. Pass the SQL it ran to offer the "Show the SQL" disclosure. */
function page_head(string $sql = ''): void
{
    $info = page_info(current_page());
    if (!$info) {
        return;
    }
    echo '<section class="page-head"><div class="container">';
    echo '<h1>' . e($info['title']) . '</h1>';
    echo '<p class="lead">' . e($info['lead']) . '</p>';
    if ($sql !== '') {
        echo '<details class="sql"><summary>Show the SQL</summary><pre><code>' . e(trim($sql)) . '</code></pre></details>';
    }
    echo '</div></section>';
}

function empty_state(string $message): void
{
    echo '<div class="empty"><p>' . e($message) . '</p></div>';
}

function field_error(string $message): void
{
    if ($message !== '') {
        echo '<p class="field-error" role="alert">' . e($message) . '</p>';
    }
}

/** Shared customer table (tasks 1, 2, 6, 7, 9). */
function customers_table(array $rows, bool $showId = false): void
{
    echo '<div class="table-wrap"><table class="table"><thead><tr>';
    echo '<th scope="col">' . ($showId ? 'ID' : '#') . '</th><th scope="col">Name</th><th scope="col">Email</th>'
       . '<th scope="col" class="num">Salary</th><th scope="col">City</th>';
    echo '</tr></thead><tbody>';
    foreach ($rows as $i => $r) {
        echo '<tr><td class="muted">' . ($showId ? e($r['id']) : $i + 1) . '</td>'
           . '<td>' . e($r['name']) . '</td>'
           . '<td>' . e($r['email']) . '</td>'
           . '<td class="num">' . fmt_num($r['salary']) . '</td>'
           . '<td>' . ($r['city'] !== null && $r['city'] !== '' ? e($r['city']) : '<span class="muted">—</span>') . '</td></tr>';
    }
    echo '</tbody></table></div>';
}

function plural(int $n, string $one, ?string $many = null): string
{
    return number_format($n) . ' ' . ($n === 1 ? $one : ($many ?? $one . 's'));
}
