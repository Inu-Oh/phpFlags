<?php
session_start();
$_SESSION = array();
// TODO - review whether I need to destroy the session cookie
session_destroy();
header('Location: index.php');
?>