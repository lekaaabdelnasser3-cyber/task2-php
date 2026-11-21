<?php
require_once 'dbc.php';
session_unset();
session_destroy();
session_start();
session_regenerate_id(true);
$_SESSION['success'] = 'You have been logged out.';
redirect('login.php');
