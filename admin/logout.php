<?php
session_start();
unset($_SESSION['admin_id']);
unset($_SESSION['admin_loggin']);
$_SESSION['admin_loggin'] = false;
header("Location: ../");
exit;
