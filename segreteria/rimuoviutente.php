<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head>
<!-- import di Bootstrap-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>


<script src="../js/segreteria.js"></script>
<link rel="stylesheet" href="../css/cssSegreteria.css">
<link rel="stylesheet" href="../css/from-re.css">

    <meta charset="utf-8">

    <title>Rimozione utente</title>


  </head>
  <body>
  <!-- INIZIO NAVBAR  -->
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link" aria-current="page" href="../segreteria.php">Homepage</a>
    </li>
    <li class="nav-item">
          <a class="nav-link" href="/segreteria/aggiungiutente.php">Aggiungi Utenza</a>
        </li>
    <li class="nav-item">
      <a class="nav-link active" href="/segreteria/aggiungiutente.php">Rimuovi Utenza</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">Inserisci corso di laurea</a>
    </li>
    <li class="nav-item">
      <a class="nav-link disabled" aria-disabled="true">Modifica Corso di Laurea</a>
    </li>
  </ul>
  <!-- FINE NAVBAR -->
 <h1> PAGINA DI RIMOZIONE UTENZA </h1>

<form method="post" >
    <div class="center bred">
        <div class="mb-3">
          <label for="exampleFormControlInput1" class="form-label">Nome Utente da rimuovere</label>
          <input type="text" class="form-control" id="nome" placeholder="inserisci l'ID utente che si vuole rimuovere" name="utente">

        </div>


  <input type="submit" class="button1 red" value="RIMUOVI UTENTE" />


    </div>
</form>

<?php
 if($_SERVER['REQUEST_METHOD']=='POST'){  //if(isset($_POST)){

    include_once('../functions.php');
    $db = open_pg_connection();

    //inserimento generale a livello di utente sia che sia docente, studente o segreteria
    $params = array ($_POST['utente']);
    $sql = "DELETE FROM utente WHERE email = $1";
    $result = pg_prepare($db,'rem',$sql);
    $result = pg_execute($db,'rem', $params);
}
 ?>


<form action="../index.php">
    <input type="submit" class="button1 lightblue" value="RITORNA ALLA HOMEPAGE" />
    </form>
</body>
</html>