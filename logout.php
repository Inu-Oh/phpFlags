<?php
require_once __DIR__ . '/src/config/config.php';

// Delete the session aray
$_SESSION = array();
// TODO - review whether I need to destroy the session cookie

// Destroy the session and redirect to home page
session_destroy();
header('Location: index.php');
?>