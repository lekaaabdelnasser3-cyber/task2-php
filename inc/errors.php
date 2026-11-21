<?php
// Flash messages set by handlers/pages: $_SESSION['errors'] and $_SESSION['success'].
if (isset($_SESSION['errors']) || isset($_SESSION['success'])) {
    echo '<div class="container flash">';
    if (isset($_SESSION['errors'])) {
        echo '<div class="alert alert-error" role="alert">' . e($_SESSION['errors']) . '</div>';
    }
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success" role="status">' . e($_SESSION['success']) . '</div>';
    }
    echo '</div>';
}
unset($_SESSION['errors'], $_SESSION['success']);
