<?php
session_start();
$_SESSION['username'] = "";
$_SESSION['password'] = "";
header("Location: ../index.php");
?>