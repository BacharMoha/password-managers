<?php
require_once __DIR__ . '/../includes/auth.php';

session_destroy();
redirect('/pages/login.php');
?>