<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

function adminLogout() {
    session_destroy();
    header('Location: index.php');
    exit();
}
