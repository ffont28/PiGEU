<?php
    session_start();
    include('../functions.php');
    include('../conf.php');
    controller("segreteria", $_SESSION['username'], $_SESSION['password']);
?>
<!doctype html>
<html lang="IT" data-bs-theme="auto">
  <head>
    <?php importVari();?>
    <title>SEGRETERIA · PiGEU</title>


  </head>
  <body>

  <!-- INIZIO NAVBAR -->
  <?php setNavbarSegreteria($_SERVER['REQUEST_URI']);?>
  <!-- FINE NAVBAR -->

  <h1>Benvenuto <?php echo $_SESSION['nome']." ".$_SESSION['cognome'] ?></h1>

<form action="../index.php" method="post">
        <input type="submit" name="logout" class="button1 black" value="logout" />
    </form>



</body>
</html>