<?php
    session_start();
    include('functions.php');
    include('conf.php');

    controller("segreteria", $_SESSION['username'], $_SESSION['password']);

?>
<!doctype html>
<html lang="IT" data-bs-theme="auto">
  <head>
<!-- import di Bootstrap-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

<link rel="stylesheet" href="../css/cssSegreteria.css">
<link rel="stylesheet" href="../css/from-re.css">


    <meta charset="utf-8">

    <title>SEGRETERIA · PiGEU</title>


  </head>
  <body>
  <!-- INIZIO NAVBAR -->
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link active" aria-current="page" href="#">Homepage</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="segreteria/aggiungiutente.php">Aggiungi Utenza</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="segreteria/gestisciutente.php">Gestisci Utenza</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="segreteria/rimuoviutente.php">Rimuovi Utenza</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="segreteria/aggiungiinsegnamento.php">Inserisci Insegnamento</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="segreteria/aggiungicdl.php">Inserisci corso di laurea</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="segreteria/modificacdl.php" aria-disabled="true">Modifica Corso di Laurea</a>
    </li>
  </ul>


  <!-- FINE NAVBAR -->

    <?php
    // Start the session
    session_start();
    ?>

    <h1>Benvenuto <?php echo $_SESSION['username']." ".$_SESSION['nome'] ?></h1>

<form action="../index.php" method="post">
        <input type="submit" name="logout" class="button1 black" value="logout" />
    </form>



</body>
</html>


<!--

<!doctype html>
<html lang="it">
<head><title>Segreteria</title></head>
<?php
// Start the session
session_start();
?>
<body>
<h1>Accesso segreteria</h1>
<p>==> accesso eseguito come <?php echo $_SESSION['username']  ?></p>
</body>
</html>

-->