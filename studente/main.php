<?php
    session_start();
    include('../functions.php');
    include('../conf.php');
    controller("studente", $_SESSION['username'], $_SESSION['password']);
?>
<!doctype html>
<html lang="IT" data-bs-theme="auto">
<head>
    <?php importVari();?>
    <title>STUDENTE · PiGEU</title>
</head>
<body>
<!-- INIZIO NAVBAR -->
<?php setNavbarStudente($_SERVER['REQUEST_URI']);?>
<!-- FINE NAVBAR -->
<?php include('../infoHomepage.php')?>
</body>
</html>
