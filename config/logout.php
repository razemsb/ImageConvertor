<?php
require_once "AuthConfig.php";
require_once "DatabaseConnect.php";
$pdo = DB::connect();
$auth = Auth::getInstance($pdo);
$auth->logout();