<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

if (!isset($_SESSION['cliente_role']) || $_SESSION['cliente_role'] !== 'admin') {
    header('Location: ' . page('login.php'));
    exit;
}
