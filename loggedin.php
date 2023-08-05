<?php
session_start();
include_once('functions.php');
check_login($_POST['username'], md5($_POST['password']));
?>