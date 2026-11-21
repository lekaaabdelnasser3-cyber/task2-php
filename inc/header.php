<?php
// Optional per-page variables: $pageTitle (string), $bodyClass (string)
$info = page_info(current_page());
$pageTitle = $pageTitle ?? ($info['title'] ?? 'Welcome to MySQL');
$bodyClass = $bodyClass ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($pageTitle) ?> | MySQL Hub</title>
  <link rel="icon" type="image/svg+xml" href="asset/images/favicon.svg">
  <link rel="preload" href="asset/fonts/dm-sans.woff" as="font" type="font/woff" crossorigin>
  <link rel="stylesheet" href="asset/css/style.css">
</head>
<body class="<?php echo e($bodyClass) ?>">
