<?php
require_once 'db.php';
unset($_SESSION['admin']);
header('Location: admin_login.php');
exit;
?>