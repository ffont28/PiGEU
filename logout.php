<?php
session_start();
$_SESSION['username'] = "";
$_SESSION['password'] = "";
$_SESSION['tipo'] = "";
header("Location: ../index.php");
?>